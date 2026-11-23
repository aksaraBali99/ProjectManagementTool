<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * SOL-0028: the sidebar shows the version under "Solava": v2026.11.2 on
 * PROD, [DEV] v2026.11.2 elsewhere. A link to the Release notes page for the
 * owner and super_admin, plain text for everyone else; no tooltip.
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

/** The page's version element: its tag (a or span), attributes and label. */
function sidebarVersion(string $html): ?array
{
    if (! preg_match('/<(a|span)\s([^>]*data-app-version[^>]*)>([^<]*)<\/\1>/', $html, $match)) {
        return null;
    }

    return ['tag' => $match[1], 'attributes' => $match[2], 'label' => $match[3]];
}

function onEnvironment(string $env, ?string $version): void
{
    app()['env'] = $env;
    config(['solava.version' => $version]);
}

test('production: a plain member sees the release number under Solava, as plain text', function () {
    onEnvironment('production', 'v2026.11.2');

    $html = $this->actingAs($this->member)->get(route('dashboard'))->assertOk()->getContent();
    $version = sidebarVersion($html);

    expect($version)->not->toBeNull()
        ->and($version['tag'])->toBe('span')
        ->and($version['label'])->toBe('v2026.11.2')
        ->and($version['attributes'])->toContain('text-gray-400')->toContain('whitespace-nowrap')
        ->not->toContain('bg-amber-50')->not->toContain('title=')->not->toContain('truncate');
    // Under the name, in the column wrapper.
    expect($html)->toMatch('/<div class="flex min-w-0 flex-col justify-center leading-tight">\s*<span class="text-sm font-medium text-gray-900">Solava<\/span>\s*<span[^>]*data-app-version/');
});

test('production: the owner and a super_admin see it as a link to the Release notes page', function (string $who) {
    onEnvironment('production', 'v2026.11.2');
    $user = $who === 'owner' ? $this->owner : tap(User::factory()->create(), fn (User $u) => $u->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id));

    $version = sidebarVersion($this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent());

    expect($version['tag'])->toBe('a')
        ->and($version['label'])->toBe('v2026.11.2')
        ->and($version['attributes'])->toContain('href="'.route('release-notes.index').'"')
        ->toContain('hover:text-gray-700')
        ->not->toContain('title=');
})->with(['owner', 'super_admin']);

test('dev: [DEV] and the version in an amber pill, and the owner\'s link stays amber on hover', function () {
    onEnvironment('dev', 'v2026.11.2');

    $version = sidebarVersion($this->actingAs($this->owner)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('[DEV] v2026.11.2')
        ->and($version['attributes'])->toContain('bg-amber-50')->toContain('text-amber-700')->toContain('px-1 ')
        ->toContain('hover:text-amber-900')
        ->not->toContain('hover:text-gray-700')->not->toContain('title=');
});

test('local: [LOCAL] and the version', function () {
    onEnvironment('local', 'v2026.11.2');

    $version = sidebarVersion($this->actingAs($this->member)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('[LOCAL] v2026.11.2')
        ->and($version['attributes'])->not->toContain('title=');
});

test('dev without a version: just [DEV]', function () {
    onEnvironment('dev', null);

    $version = sidebarVersion($this->actingAs($this->member)->get(route('dashboard'))->getContent());

    expect($version['label'])->toBe('[DEV]');
});

test('production without a version: the page renders with no version element', function () {
    onEnvironment('production', null);

    $html = $this->actingAs($this->owner)->get(route('dashboard'))->assertOk()->getContent();

    expect($html)->not->toContain('data-app-version')
        ->toMatch('/<span class="text-sm font-medium text-gray-900">Solava<\/span>\s*<\/div>/');
});

test('the version is escaped', function () {
    onEnvironment('production', '<script>alert(1)</script>');

    $html = $this->actingAs($this->member)->get(route('dashboard'))->getContent();

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
});

test('the login page shows no version', function () {
    onEnvironment('production', 'v2026.11.2');

    expect($this->get(route('login'))->assertOk()->getContent())->not->toContain('data-app-version');
});
