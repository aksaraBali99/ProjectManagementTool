<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Support\ReleaseNotes;

/**
 * task #124: Settings > Release notes reads resources/release-notes.json
 * (config('solava.release_notes_path'), pointed at a fixture here), for
 * owner and super_admin only.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->fixture = tempnam(sys_get_temp_dir(), 'release-notes-');
    config(['solava.release_notes_path' => $this->fixture]);
});

afterEach(function () {
    @unlink($this->fixture);
});

/** Writes the fixture file: an array is JSON-encoded, a string written as is. */
function writeNotes(object $context, array|string $notes): void
{
    file_put_contents($context->fixture, is_string($notes) ? $notes : json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function twoReleases(): array
{
    return ['releases' => [
        [
            'version' => 'v2026.11.2', 'date' => '2026-11-20', 'summary' => "A fix release.\nSecond line of the summary.",
            'tasks' => [
                ['number' => 123, 'type' => 'Bugfix', 'title' => 'Read-only deactivated tasks',
                    'before' => 'A deactivated task could still be edited.', 'after' => "A deactivated task is read-only.\nActivate it to change it.", 'pr' => 152],
            ],
        ],
        [
            'version' => 'v2026.11.1', 'date' => '2026-11-10', 'summary' => 'The first release.',
            'tasks' => [
                ['number' => 121, 'type' => 'Bugfix', 'title' => 'Keep the back link',
                    'before' => 'Saving lost the back link.', 'after' => 'The back link stays.', 'pr' => 151],
                ['number' => 118, 'type' => 'Enhancement', 'title' => 'Company-coded task numbers',
                    'before' => 'Tasks had no code.', 'after' => 'Every task has a code like BGS-0012.', 'prs' => [144, 145]],
                ['number' => 119, 'type' => 'Chore', 'title' => 'Release process', 'prs' => [149, 150]],
            ],
        ],
    ]];
}

function userWithRole(Organization $org, string $slug): User
{
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', $slug)->firstOrFail()->id]);

    return $user;
}

// --- Who sees it -------------------------------------------------------------

test('the owner and a super_admin see every release, newest first, with tasks grouped by type', function (string $who) {
    writeNotes($this, twoReleases());
    $user = $who === 'owner' ? $this->owner : tap(User::factory()->create(), fn (User $u) => $u->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id));

    $html = $this->actingAs($user)->get(route('release-notes.index'))->assertOk()->getContent();

    // Newest first, and only the newest is "Latest".
    expect(strpos($html, 'v2026.11.2'))->toBeLessThan(strpos($html, 'v2026.11.1'))
        ->and(substr_count($html, '>Latest</span>'))->toBe(1)
        ->and($html)->toMatch('/data-release="v2026.11.2">.*?>Latest<\/span>.*?data-release="v2026.11.1"/s')
        // Dates, summaries, tasks, before/after.
        ->toContain('Nov 20, 2026')->toContain('Nov 10, 2026')
        ->toContain('task #118')->toContain('Company-coded task numbers')
        ->toContain('Tasks had no code.')->toContain('Every task has a code like BGS-0012.')
        // Grouped: Enhancement, then Bugfix, then Chore, inside v2026.11.1.
        ->toMatch('/data-release="v2026.11.1">.*?>Enhancement<\/h3>.*?task #118.*?>Bugfix<\/h3>.*?task #121.*?>Chore<\/h3>.*?task #119/s')
        // PR and GitHub release links.
        ->toContain('href="https://github.com/aksaraBali99/ProjectManagementTool/pull/144"')
        ->toContain('href="https://github.com/aksaraBali99/ProjectManagementTool/pull/145"')
        ->toContain('href="https://github.com/aksaraBali99/ProjectManagementTool/releases/tag/v2026.11.1"')
        // The link may not exist yet (DEV, before the release): it says so.
        ->toContain('title="Exists once v2026.11.1 is released to PROD"')
        // Line breaks are kept by CSS, the text itself printed as is.
        ->toContain("A deactivated task is read-only.\nActivate it to change it.")
        ->toContain('whitespace-pre-line');
})->with(['owner', 'super_admin']);

test('the owner sees "Release notes" in the sidebar and on the Settings page', function () {
    $this->actingAs($this->owner)->get(route('settings.index'))->assertOk()
        ->assertSee('href="'.route('release-notes.index').'"', false)
        ->assertSee('What changed in each release, newest first');
});

test('management, staff and a client get a 403, and no sidebar link or Settings card', function (string $slug) {
    $org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $user = userWithRole($org, $slug);
    if ($slug === 'client') {
        $project = Project::create(['organization_id' => $org->id, 'name' => 'P', 'description' => 'd']);
        $project->clients()->attach($user->id);
    } elseif ($slug === 'staff') {
        Department::create(['organization_id' => $org->id, 'name' => 'Ops', 'color' => '#000000']);
    }

    $this->actingAs($user)->get(route('release-notes.index'))->assertForbidden();
    $this->actingAs($user)->get(route('settings.index'))->assertOk()
        ->assertDontSee(route('release-notes.index'))
        ->assertDontSee('Release notes');
})->with(['management', 'staff', 'client']);

// --- Contents ------------------------------------------------------------------

test('an empty list says "No releases yet."', function () {
    writeNotes($this, ['releases' => []]);

    $this->actingAs($this->owner)->get(route('release-notes.index'))->assertOk()
        ->assertSee('No releases yet.')
        ->assertDontSee('Latest');
});

test('a script tag written in a note shows as plain text and never runs', function () {
    $notes = twoReleases();
    $notes['releases'][0]['summary'] = 'Hello <script>alert("x")</script> world';
    $notes['releases'][0]['tasks'][0]['after'] = '<img src=x onerror=alert(1)> <b>bold?</b>';
    writeNotes($this, $notes);

    $response = $this->actingAs($this->owner)->get(route('release-notes.index'))->assertOk();

    $response->assertSee('Hello <script>alert("x")</script> world')   // shown as text (escaped in the HTML)
        ->assertDontSee('<script>alert("x")</script>', false)        // never as a real tag
        ->assertDontSee('<img src=x onerror=alert(1)>', false)
        ->assertDontSee('<b>bold?</b>', false)
        ->assertSee('&lt;script&gt;', false);
});

// --- An invalid file -------------------------------------------------------------

dataset('invalid notes', [
    'bad JSON' => ['{"releases": [', 'not valid JSON'],
    'no releases list' => ['{"versions": []}', 'a "releases" list'],
    'missing before on a Bugfix' => [fn () => withoutBefore(), 'needs "before"'],
    'versions out of order' => [fn () => ['releases' => array_reverse(twoReleases()['releases'])], 'newest first'],
    'duplicate version' => [fn () => ['releases' => [twoReleases()['releases'][1], twoReleases()['releases'][1]]], 'appears more than once'],
    'duplicate task in one release' => [fn () => duplicateTask(), 'more than once'],
    'a bad version' => [fn () => withVersion('2026.11.3'), 'like v2026.11.1'],
    'an impossible date' => [fn () => withDate('2026-02-30'), 'like 2026-11-10'],
    'an unknown type' => [fn () => withType('Feature'), 'needs a "type"'],
    'both pr and prs' => [fn () => withBothPrs(), 'both "pr" and "prs"'],
    // The same cases as tests/scripts/run.sh's jq checks, so release-check
    // never passes a file this page then rejects.
    'a Chore with an empty before' => [fn () => withChore(['before' => '']), 'needs "before"'],
    'a Chore with a non-text after' => [fn () => withChore(['after' => 5]), 'needs "after"'],
    'a task number written 5.0' => ['{"releases": [{"version": "v2026.11.1", "date": "2026-11-10", "summary": "S.", "tasks": [{"number": 5.0, "type": "Chore", "title": "T"}]}]}', 'needs a task "number"'],
    'PR number 0' => [fn () => withChore(['pr' => 0]), 'a PR number must be a whole number'],
    'a PR number as text' => [fn () => withChore(['prs' => ['150']]), 'a PR number must be a whole number'],
    'prs that isn\'t a list' => [fn () => withChore(['prs' => 150]), '"prs" must be a list'],
    'a month 13' => [fn () => withDate('2026-13-45'), 'like 2026-11-10'],
    '29 February in a non-leap year' => [fn () => withDate('2026-02-29'), 'like 2026-11-10'],
    'a release that isn\'t an object' => ['{"releases": ["oops"]}', 'must be an object'],
    'a task that isn\'t an object' => ['{"releases": [{"version": "v2026.11.1", "date": "2026-11-10", "summary": "S.", "tasks": ["oops"]}]}', 'must be an object'],
]);

/** The newest release with one Chore task, with $fields added. */
function withChore(array $fields): array
{
    $notes = twoReleases();
    $notes['releases'][0]['tasks'] = [['number' => 119, 'type' => 'Chore', 'title' => 'T', ...$fields]];

    return $notes;
}

function withoutBefore(): array
{
    $notes = twoReleases();
    unset($notes['releases'][0]['tasks'][0]['before']);

    return $notes;
}

function duplicateTask(): array
{
    $notes = twoReleases();
    $notes['releases'][0]['tasks'][] = $notes['releases'][0]['tasks'][0];

    return $notes;
}

function withVersion(string $version): array
{
    $notes = twoReleases();
    $notes['releases'][0]['version'] = $version;

    return $notes;
}

function withDate(string $date): array
{
    $notes = twoReleases();
    $notes['releases'][0]['date'] = $date;

    return $notes;
}

function withType(string $type): array
{
    $notes = twoReleases();
    $notes['releases'][0]['tasks'][0]['type'] = $type;

    return $notes;
}

function withBothPrs(): array
{
    $notes = twoReleases();
    $notes['releases'][0]['tasks'][0]['prs'] = [1, 2];

    return $notes;
}

test('an invalid file shows why on the page, and the dashboard still loads', function (array|string $notes, string $reason) {
    writeNotes($this, $notes);

    $this->actingAs($this->owner)->get(route('release-notes.index'))->assertOk()
        ->assertSee('The release notes file is invalid:')
        ->assertSee($reason)
        ->assertDontSee('No releases yet.');

    $this->actingAs($this->owner)->get(route('dashboard'))->assertOk();
    $this->actingAs($this->owner)->get(route('settings.index'))->assertOk();
})->with('invalid notes');

test('versions compare as numbers: v2026.11.10 is newer than v2026.11.9', function () {
    expect(ReleaseNotes::compareVersions('v2026.11.10', 'v2026.11.9'))->toBeGreaterThan(0)
        ->and(ReleaseNotes::compareVersions('v2026.12.1', 'v2026.11.9'))->toBeGreaterThan(0)
        ->and(ReleaseNotes::compareVersions('v2027.01.1', 'v2026.12.5'))->toBeGreaterThan(0)
        ->and(ReleaseNotes::compareVersions('v2026.11.1', 'v2026.11.1'))->toBe(0);
});

// --- The real file ----------------------------------------------------------------

test('the real resources/release-notes.json in the repo is valid', function () {
    $notes = new ReleaseNotes(resource_path('release-notes.json'));

    expect($notes->error())->toBeNull();
});
