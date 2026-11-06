<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * task #72 phase 2: GET /tasks/{task}/preview — the Tasks list row
 * drilldown, now a read-only fragment fetched on first open.
 *
 * Two things this file has to pin down. First that the fragment is
 * genuinely read-only: no editor, no comment form, no reaction buttons,
 * no subtask inputs, because commenting and subtask editing moved to the
 * task page and a stray control here would be a half-working one.
 * Second that it is exactly as visible as the task page — the
 * authorization block below asserts that against
 * Gate::forUser(...)->allows('view', ...) for every user shape rather
 * than by hand, so the two can't drift apart.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->otherOrg = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#111111']);

    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Project B', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        // Editor HTML, not legacy plain text — RichText::toHtml() escapes
        // the latter, so a bare "<strong>" here would assert the wrong path.
        'description' => '<p>The <strong>description</strong>.</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function previewUrl(Task $task): string
{
    return "/tasks/{$task->id}/preview";
}

function addComment(Task $task, User $author, string $body, ?Comment $parent = null): Comment
{
    return Comment::create([
        'task_id' => $task->id,
        'user_id' => $author->id,
        'parent_comment_id' => $parent?->id,
        'body' => $body,
    ]);
}

// --- Contents ------------------------------------------------------------

test('the preview renders the description', function () {
    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertSee('<strong>description</strong>', false)
        ->assertSee('data-rich-text-content', false);
});

test('a task with no description shows the same empty text the drilldown used', function () {
    $this->task->update(['description' => null]);

    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertSee('No description.');
});

test('every subtask shows done state, title, assignee first name and both dates', function () {
    $assignee = User::factory()->create(['name' => 'Rina Pratama']);
    Subtask::create([
        'task_id' => $this->task->id, 'title' => 'First subtask', 'assignee_id' => $assignee->id,
        'is_done' => true, 'start_date' => '2026-03-02', 'due_date' => '2026-03-09',
    ]);
    Subtask::create(['task_id' => $this->task->id, 'title' => 'Second subtask', 'is_done' => false]);

    $response = $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk();

    $response->assertSee('First subtask')
        ->assertSee('Second subtask')
        ->assertSee('Rina')          // first name only, as the row does
        ->assertSee('Unassigned')    // the one with no assignee
        ->assertSee('Mar 2')
        ->assertSee('Mar 9');
});

test('a task with no subtasks says so', function () {
    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        // Same wording as the task page's own partial.
        ->assertSee('No subtasks yet.');
});

test('subtasks are read-only: no input, select or textarea anywhere in the preview', function () {
    Subtask::create(['task_id' => $this->task->id, 'title' => 'Editable on the task page only']);

    $html = $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk()->getContent();

    // Counted as real elements, not substrings — a class name or a script
    // mentioning "input" must not be able to satisfy this.
    $document = new DOMDocument;
    @$document->loadHTML('<div>'.$html.'</div>');
    $xpath = new DOMXPath($document);

    expect($xpath->query('//input')->length)->toBe(0);
    expect($xpath->query('//select')->length)->toBe(0);
    expect($xpath->query('//textarea')->length)->toBe(0);
    expect($xpath->query('//button')->length)->toBe(0);
    expect($xpath->query('//form')->length)->toBe(0);
});

test('the preview carries none of the task page comment machinery', function () {
    addComment($this->task, $this->management, '<p>A comment</p>');

    $response = $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk();

    $response->assertDontSee('new-comment-editor', false)      // the editor
        ->assertDontSee('post-comment-btn', false)             // the submit control
        ->assertDontSee('reaction-pill', false)                // reactions
        ->assertDontSee('react-comment-btn', false)
        ->assertDontSee('reply-comment-btn', false)
        ->assertDontSee('edit-comment-btn', false)
        ->assertDontSee('delete-comment-btn', false)
        ->assertDontSee('subtask-container', false)            // the live subtask partial
        ->assertDontSee('new-subtask-title', false)
        ->assertDontSee('comment-container', false);           // and its 7s poll root
});

test('the latest three top-level comments show newest first, with a count of the rest', function () {
    foreach (range(1, 5) as $i) {
        // forceFill, not update(): created_at isn't in Comment's #[Fillable],
        // so mass assignment drops it silently and every row lands on the
        // same timestamp.
        addComment($this->task, $this->management, "<p>Comment {$i}</p>")
            ->forceFill(['created_at' => now()->addMinutes($i)])
            ->save();
    }

    $html = $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk()->getContent();

    // Newest three only.
    expect($html)->toContain('Comment 5')->toContain('Comment 4')->toContain('Comment 3')
        ->not->toContain('Comment 2')
        ->not->toContain('Comment 1');

    // Newest first.
    expect(strpos($html, 'Comment 5'))->toBeLessThan(strpos($html, 'Comment 4'));
    expect(strpos($html, 'Comment 4'))->toBeLessThan(strpos($html, 'Comment 3'));

    expect($html)->toContain('2 more comments');
});

test('exactly three comments shows all three and no more-comments line', function () {
    foreach (range(1, 3) as $i) {
        addComment($this->task, $this->management, "<p>Comment {$i}</p>");
    }

    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertDontSee('more comment');
});

test('replies are counted, not rendered, and never fill a top-level slot', function () {
    $parent = addComment($this->task, $this->management, '<p>Top level</p>');
    addComment($this->task, $this->management, '<p>First reply</p>', $parent);
    addComment($this->task, $this->management, '<p>Second reply</p>', $parent);

    $response = $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk();

    $response->assertSee('Top level')
        ->assertSee('2 replies')
        ->assertDontSee('First reply')
        ->assertDontSee('Second reply')
        // Three replies plus one parent must not read as "3 more comments".
        ->assertDontSee('more comment');
});

test('a single reply is pluralised correctly', function () {
    $parent = addComment($this->task, $this->management, '<p>Top level</p>');
    addComment($this->task, $this->management, '<p>Only reply</p>', $parent);

    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertSee('1 reply')
        ->assertDontSee('1 replies');
});

test('a task with no comments says so', function () {
    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertSee('No comments yet.');
});

test('a mention in a comment carries the same markup the task page renders', function () {
    $mentioned = User::factory()->create(['name' => 'Budi Santoso']);
    $comment = addComment($this->task, $this->management, '<p>Ping @Budi Santoso</p>');
    $comment->mentionedUsers()->attach($mentioned->id);

    // The server emits data-mentioned-users; mention-highlight.js turns the
    // "@Name" into <span class="mention"> client-side, exactly as it does on
    // the task page. So the attribute is the contract to assert here.
    $this->actingAs($this->management)
        ->get(previewUrl($this->task))
        ->assertOk()
        ->assertSee('data-mentioned-users', false)
        ->assertSee('Budi Santoso');
});

test('the Open task link preserves the list filters and sort, and the hint is shown', function () {
    // Built from the route, not a hardcoded "http://localhost": return_to is
    // only accepted when its host matches the request's, which follows
    // APP_URL - localhost on a dev machine, founderos.test in CI.
    $listUrl = route('tasks.index', $this->org->id).'?status=pending&sort=title&direction=desc';

    $response = $this->actingAs($this->management)
        ->get(previewUrl($this->task).'?'.http_build_query(['return_to' => $listUrl, 'return_label' => 'Tasks']))
        ->assertOk();

    $response->assertSee('Open task')
        ->assertSee('To comment or edit subtasks, open the task.');

    // return_to travels URL-encoded inside the href, so decode it and
    // compare the real value rather than hunting for substrings that the
    // encoding has broken up.
    expect(returnToFromOpenTaskLink($response->getContent()))->toBe($listUrl);
});

/** The decoded return_to the preview's "Open task" link carries. */
function returnToFromOpenTaskLink(string $html): ?string
{
    $document = new DOMDocument;
    @$document->loadHTML('<div>'.$html.'</div>');
    $xpath = new DOMXPath($document);

    $href = $xpath->query('//a[normalize-space(text())="Open task"]')->item(0)?->getAttribute('href');
    if ($href === null) {
        return null;
    }

    parse_str((string) parse_url($href, PHP_URL_QUERY), $query);

    return $query['return_to'] ?? null;
}

test('an off-site return_to is rejected rather than linked', function () {
    $response = $this->actingAs($this->management)
        ->get(previewUrl($this->task).'?'.http_build_query([
            'return_to' => 'https://evil.example.com/steal',
            'return_label' => 'Tasks',
        ]))
        ->assertOk();

    $response->assertDontSee('evil.example.com');

    // Falls back to the company's own list, never the supplied host.
    expect(returnToFromOpenTaskLink($response->getContent()))
        ->toBe(route('tasks.index', $this->org->id));
});

// --- Authorization -------------------------------------------------------

/**
 * Every user shape the task page's own policy tests use. Each one must get
 * 200 from the preview exactly when Gate 'view' allows it and 403
 * otherwise — compared against the Gate itself, not against a hand-written
 * expectation, so the preview can never drift away from tasks.edit.
 */
dataset('viewerShapes', [
    'management in the company' => ['management'],
    'staff with department access' => ['staffWithAccess'],
    'staff without department access' => ['staffNoAccess'],
    'staff who is only the task assignee' => ['staffAssigneeOnly'],
    'staff who is only a subtask assignee' => ['staffSubtaskAssigneeOnly'],
    'client on the task project' => ['clientOnProject'],
    'client on a different project' => ['clientOtherProject'],
    'a user whose role lost view_tasks' => ['viewTasksRevoked'],
    'a user from another company' => ['foreignUser'],
]);

test('the preview is visible exactly when the task page is', function (string $shape) {
    $user = makePreviewViewer($this, $shape);

    $allowed = Gate::forUser($user)->allows('view', $this->task);

    $previewStatus = $this->actingAs($user)->get(previewUrl($this->task))->getStatusCode();
    $editStatus = $this->actingAs($user)->get("/tasks/{$this->task->id}/edit")->getStatusCode();

    // 200 exactly when the Gate allows it.
    expect($previewStatus === 200)->toBe($allowed);

    // And when it doesn't, refused the SAME way the task page refuses.
    // Not hardcoded to 403: a user from another company can't resolve the
    // task at all, because Task carries BelongsToOrganization, so route
    // binding 404s before any policy runs — on both endpoints.
    expect($previewStatus)->toBe($editStatus);
})->with('viewerShapes');

test('a deactivated task previews for whoever can still open its task page', function (string $shape) {
    // The viewer is built FIRST: two of the shapes mutate the task
    // (assignee, a subtask), and a Task instance fetched before that would
    // carry stale attributes into the Gate while the real request re-reads
    // the row and disagrees.
    $user = makePreviewViewer($this, $shape);

    $this->task->delete();
    $task = Task::withTrashed()->findOrFail($this->task->id);

    $allowed = Gate::forUser($user)->allows('view', $task);

    $previewStatus = $this->actingAs($user)->get(previewUrl($task))->getStatusCode();
    $editStatus = $this->actingAs($user)->get("/tasks/{$task->id}/edit")->getStatusCode();

    expect($previewStatus === 200)->toBe($allowed);
    expect($previewStatus)->toBe($editStatus);
})->with('viewerShapes');

function makePreviewViewer(object $context, string $shape): User
{
    $staffRoleId = Role::where('slug', 'staff')->firstOrFail()->id;
    $clientRoleId = Role::where('slug', 'client')->firstOrFail()->id;

    $staffIn = function (Organization $org) use ($staffRoleId) {
        $user = User::factory()->create();
        OrgMember::create(['organization_id' => $org->id, 'user_id' => $user->id, 'role_id' => $staffRoleId]);

        return $user;
    };

    return match ($shape) {
        'management' => $context->management,

        'staffWithAccess' => tap($staffIn($context->org), function (User $user) use ($context) {
            AccessPermission::create([
                'user_id' => $user->id, 'organization_id' => $context->org->id,
                'department_id' => $context->dept->id, 'allowed' => true,
            ]);
        }),

        'staffNoAccess' => tap($staffIn($context->org), function (User $user) use ($context) {
            // Access to a DIFFERENT department in the same company.
            AccessPermission::create([
                'user_id' => $user->id, 'organization_id' => $context->org->id,
                'department_id' => $context->otherDept->id, 'allowed' => true,
            ]);
        }),

        'staffAssigneeOnly' => tap($staffIn($context->org), function (User $user) use ($context) {
            $context->task->update(['assignee_id' => $user->id]);
        }),

        'staffSubtaskAssigneeOnly' => tap($staffIn($context->org), function (User $user) use ($context) {
            Subtask::create(['task_id' => $context->task->id, 'title' => 'Theirs', 'assignee_id' => $user->id]);
        }),

        'clientOnProject' => tap(User::factory()->create(), function (User $user) use ($context, $clientRoleId) {
            OrgMember::create(['organization_id' => $context->org->id, 'user_id' => $user->id, 'role_id' => $clientRoleId]);
            $context->project->clients()->attach($user->id);
        }),

        'clientOtherProject' => tap(User::factory()->create(), function (User $user) use ($context, $clientRoleId) {
            OrgMember::create(['organization_id' => $context->org->id, 'user_id' => $user->id, 'role_id' => $clientRoleId]);
            $context->otherProject->clients()->attach($user->id);
        }),

        'viewTasksRevoked' => tap($staffIn($context->org), function (User $user) use ($context) {
            AccessPermission::create([
                'user_id' => $user->id, 'organization_id' => $context->org->id,
                'department_id' => $context->dept->id, 'allowed' => true,
            ]);
            Role::where('slug', 'staff')->firstOrFail()
                ->permissions()
                ->detach(Permission::where('slug', 'view_tasks')->firstOrFail()->id);
        }),

        'foreignUser' => $staffIn($context->otherOrg),
    };
}

test('an unauthenticated preview request REDIRECTS to login rather than returning 401', function () {
    // Pins the contract the drilldown's fetch is written against. Because
    // the request asks for text/html, Laravel's auth middleware redirects
    // instead of returning a status code, and fetch follows redirects by
    // default — so response.ok would be true and the login page's HTML
    // would land inside a table cell. index.blade.php therefore checks
    // response.redirected explicitly. If this ever becomes a 401, that
    // check is merely redundant rather than wrong, but the JS comment
    // explaining it would need revisiting.
    $this->withHeaders(['Accept' => 'text/html'])
        ->get(previewUrl($this->task))
        ->assertRedirect('/login');
});

// --- The list page itself ------------------------------------------------

test('the Tasks list no longer renders the interactive comment or subtask machinery', function () {
    addComment($this->task, $this->management, '<p>A comment</p>');
    Subtask::create(['task_id' => $this->task->id, 'title' => 'A subtask']);

    $response = $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk();

    $response->assertDontSee('comment-container', false)
        ->assertDontSee('new-comment-editor', false)
        ->assertDontSee('post-comment-btn', false)
        ->assertDontSee('subtask-container', false)
        ->assertDontSee('new-subtask-title', false)
        ->assertDontSee('subtask-title-input', false)
        ->assertDontSee('reaction-pill', false);

    // And no editable controls in the table at all. The row's Deactivate
    // action is a form+button, so those two are expected; inputs, selects
    // and textareas are not.
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);

    expect($xpath->query('//tbody//textarea')->length)->toBe(0);
    expect($xpath->query('//tbody//select')->length)->toBe(0);
    expect($xpath->query('//tbody//input[@type="date"]')->length)->toBe(0);
    expect($xpath->query('//tbody//input[@type="text"]')->length)->toBe(0);
    expect($xpath->query('//tbody//input[@type="checkbox"]')->length)->toBe(0);
});

test('each row carries a preview container pointing at that task s preview url', function () {
    $second = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id,
        'department_id' => $this->dept->id, 'title' => 'Another task',
        'priority' => 'low', 'status' => 'pending',
    ]);

    $html = $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()->getContent();

    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);

    $containers = $xpath->query('//div[contains(@class, "task-preview")]');
    expect($containers->length)->toBe(2);

    $urls = [];
    foreach ($containers as $container) {
        $urls[] = parse_url($container->getAttribute('data-preview-url'), PHP_URL_PATH);
    }

    expect($urls)->toContain("/tasks/{$this->task->id}/preview")
        ->toContain("/tasks/{$second->id}/preview");

    // The loading line is what the row shows until the fetch lands.
    expect($xpath->query('//p[contains(@class, "task-preview-loading")]')->length)->toBe(2);
});

test('a task description is NOT rendered into the list HTML any more', function () {
    $this->task->update(['description' => '<p>UNIQUE-DESCRIPTION-MARKER-9f3a</p>']);

    $this->actingAs($this->management)
        ->get("/tasks/{$this->org->id}")
        ->assertOk()
        ->assertDontSee('UNIQUE-DESCRIPTION-MARKER-9f3a');
});

test('a comment body is NOT rendered into the list HTML any more', function () {
    addComment($this->task, $this->management, '<p>UNIQUE-COMMENT-MARKER-7b21</p>');

    $this->actingAs($this->management)
        ->get("/tasks/{$this->org->id}")
        ->assertOk()
        ->assertDontSee('UNIQUE-COMMENT-MARKER-7b21');
});

// --- Cost ----------------------------------------------------------------

test('the preview costs the same whatever the number of subtasks and comments', function () {
    // 1 subtask + 1 comment, NOT an empty task: with no rows at all Laravel
    // skips the nested assignee/user/mentionedUsers loads entirely, so an
    // empty baseline would be 3 queries cheaper for a reason that has
    // nothing to do with scaling.
    $firstAssignee = User::factory()->create();
    Subtask::create(['task_id' => $this->task->id, 'title' => 'Sub 0', 'assignee_id' => $firstAssignee->id]);
    addComment($this->task, $this->management, '<p>Comment 0</p>');

    $small = countQueries(function () {
        $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk();
    });

    $assignee = User::factory()->create();
    foreach (range(1, 20) as $i) {
        Subtask::create(['task_id' => $this->task->id, 'title' => "Sub {$i}", 'assignee_id' => $assignee->id]);
    }
    foreach (range(1, 30) as $i) {
        $parent = addComment($this->task, $this->management, "<p>Comment {$i}</p>");
        addComment($this->task, $this->management, "<p>Reply to {$i}</p>", $parent);
    }

    $large = countQueries(function () {
        $this->actingAs($this->management)->get(previewUrl($this->task))->assertOk();
    });

    expect($large)->toBe($small);
    expect($large)->toBeLessThan(15);
});
