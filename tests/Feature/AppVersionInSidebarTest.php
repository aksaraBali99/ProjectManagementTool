<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * SOL-0028: the sidebar shows the version next to "Solava": the release on
 * PROD, the environment and commit elsewhere. A link to the Release notes
 * page for the owner and super_admin, plain text for everyone else.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->member = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->member->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    $this->previousEnv = $this->app['env'];
});

afterEach(function () {
    $this->app['env'] = $this->previousEnv;
});

/** The page's version element: its tag (a or span) and attributes, and label. */
function sidebarVersion(string $html): ?array
{
    if (! preg_match('/<(a|span)\s([^>]*data-app-version[^>]*)>([^<]*)<\/\1>/', $html, $match)) {
        return null;
    }

    return ['tag' => $match[1], 'attributes' => $match[2], 'label' => $match[3]];
}

function onEnvironment(object $context, string $env, ?string $version, ?string $commit): void
{
    app()['env'] = $env;
    config(['solava.build' => ['version' => $version, 'commit' => $commit]]);
}

test('production: a plain member sees the release number next to Solava, not as a link', function () {
    onEnvironment($this, 'production', 'v2026.11.2', 'a13a163');

    $html = $this->actingAs($this->member)->get(route('dashboard'))->assertOk()->getContent();
    $version = sidebarVersion($html);

    expect($version)->not->toBeNull()
        ->and($version['tag'])->toBe('span')
        ->and($version['label'])->toBe('v2026.11.2')
        ->and($version['attributes'])->toContain('title="Release v2026.11.2 · commit a13a163"')
        ->toContain('text-gray-400')
        ->not->toContain('bg-amber-50');
    expect($html)->toMatch('/<span class="text-sm font-medium text-gray-900">Solava<\/span>\s*<span[^>]*data-app-version/')
        ->not->toContain('href="'.route('release-notes.index').'" class="min-w-0');
});

test('production: the owner and a super_admin see it as a link to the Release notes page', function (string $who) {
    onEnvironment($this, 'production', 'v2026.11.2', 'a13a163');
    $user = $who === 'owner' ? $this->owner : tap(User::factory()->create(), fn (User $u) => $u->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id));

    $version = sidebarVersion($this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent());

    expect($version['tag'])->toBe('a')
        ->and($version['attributes'])->toContain('href="'.route('release-notes.index').'"')
        ->toContain('hover:text-gray-700')
        ->and($version['label'])->toBe('v2026.11.2');
})->with(['owner', 'super_admin']);

test('production without a version shows the commit', function () {
    onEnvironment($this, 'production', null, 'a13a163');

    $version = sidebarVersion($this->actingAs($this->member)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('a13a163')
        ->and($version['attributes'])->toContain('title="commit a13a163"');
});

test('dev: the environment and commit in an amber pill, and the tooltip names the release notes entry', function () {
    onEnvironment($this, 'dev', 'v2026.11.2', 'a13a163');

    $version = sidebarVersion($this->actingAs($this->owner)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('dev · a13a163')
        ->and($version['attributes'])->toContain('title="Newest release notes entry: v2026.11.2 · commit a13a163 · APP_ENV dev"')
        ->toContain('bg-amber-50')->toContain('text-amber-700');
});

test('local without a git checkout: just the environment', function () {
    onEnvironment($this, 'local', 'v2026.11.2', null);

    $version = sidebarVersion($this->actingAs($this->member)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('local')
        ->and($version['attributes'])->toContain('title="Newest release notes entry: v2026.11.2 · APP_ENV local"');
});

test('production with neither a version nor a commit: the page renders with no version element', function () {
    onEnvironment($this, 'production', null, null);

    $html = $this->actingAs($this->owner)->get(route('dashboard'))->assertOk()->getContent();

    expect($html)->not->toContain('data-app-version')
        ->toMatch('/<span class="text-sm font-medium text-gray-900">Solava<\/span>\s*<\/div>/');
});

test('the version is escaped', function () {
    onEnvironment($this, 'production', '<script>alert(1)</script>', 'a13a163');

    $html = $this->actingAs($this->member)->get(route('dashboard'))->getContent();

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
});

test('the login page shows no version', function () {
    onEnvironment($this, 'production', 'v2026.11.2', 'a13a163');

    expect($this->get(route('login'))->assertOk()->getContent())->not->toContain('data-app-version');
});
