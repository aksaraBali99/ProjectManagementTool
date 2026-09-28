<?php

use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * Covers task #73's "+ New" menu consolidation on the Documents page: one
 * dropdown (Upload file / Add link / New folder) replacing the old header
 * "+ Add new document" button and the separate "+ New folder" breadcrumb
 * link. The menu is gated entirely on manage_documents (DocumentPolicy's
 * create() check, same one the old header button used) — none of its three
 * items has its own separate visibility check.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function makeStaffForNewMenuTest(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    return $staff;
}

test('manage_documents ON renders the "+ New" button and all three menu items', function () {
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('+ New');
    $response->assertSee('Upload file');
    $response->assertSee('Add link');
    $response->assertSee('New folder');
});

test('manage_documents OFF renders no menu button, and the create endpoints still 403 directly', function () {
    $staff = makeStaffForNewMenuTest($this->orgA);

    $response = $this->actingAs($staff)->get('/documents/'.$this->orgA->id);
    $response->assertOk();
    // Structural markers, not the bare button/item text — the page's own
    // (unconditionally-rendered) script block legitimately mentions "+ New"
    // and "New folder" in its own comments, so text assertions alone would
    // pass even if the actual button leaked through by accident and fail
    // even when it correctly doesn't render.
    $response->assertDontSee('aria-haspopup="menu"', false);
    $response->assertDontSee('New folder');

    // The menu items are only a UI convenience — the real gate is still the
    // endpoints themselves, direct requests included.
    $this->actingAs($staff)->get('/documents/create/'.$this->orgA->id)->assertForbidden();
    $this->actingAs($staff)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'Sneaky folder',
    ])->assertForbidden();
});

test('the old header button and the old standalone folder-creation link no longer render', function () {
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $response->assertDontSee('+ Add new document');
    // The old dedicated link was always visible next to the breadcrumb,
    // outside any menu — it's gone in favor of the "New folder" menu item,
    // which only exists inside the dropdown behind data-new-menu-action.
    $response->assertDontSee('new-folder-toggle', false);
});

test('holding manage_documents in company A but not company B shows the button only on A\'s tab', function () {
    OrgMember::create([
        'organization_id' => $this->orgB->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)
        ->assertOk()
        ->assertSee('aria-haspopup="menu"', false);

    $this->actingAs($this->management)->get('/documents/'.$this->orgB->id)
        ->assertOk()
        ->assertDontSee('aria-haspopup="menu"', false);
});

test('Upload file and Add link menu items point at the create page with the right mode, company, and folder preserved at root', function () {
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('/documents/create/'.$this->orgA->id.'?mode=upload', false);
    $response->assertSee('/documents/create/'.$this->orgA->id.'?mode=link', false);
});

test('Upload file and Add link menu items preserve the current folder when inside a nested folder', function () {
    $folder = DocumentFolder::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Contracts',
        'created_by' => $this->management->id,
    ]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id.'?folder='.$folder->id);

    $response->assertOk();
    $response->assertSee('folder='.$folder->id.'&amp;mode=upload', false);
    $response->assertSee('folder='.$folder->id.'&amp;mode=link', false);
});

test('the create page preselects upload mode from ?mode=upload, and falls back to link for anything else', function () {
    $upload = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id.'?mode=upload');
    $upload->assertOk();
    expect($upload->viewData('initialMode'))->toBe('upload');

    $link = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id.'?mode=link');
    $link->assertOk();
    expect($link->viewData('initialMode'))->toBe('link');

    $unrecognized = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id.'?mode=bogus');
    $unrecognized->assertOk();
    expect($unrecognized->viewData('initialMode'))->toBe('link');

    $missing = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);
    $missing->assertOk();
    expect($missing->viewData('initialMode'))->toBe('link');
});

test('the "+ New" menu carries the required ARIA menu semantics', function () {
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);
    $response->assertOk();

    $content = $response->getContent();

    expect($content)->toContain('aria-haspopup="menu"');
    expect($content)->toContain('aria-expanded="false"');
    expect($content)->toContain('role="menu"');
    // Counts the actual menu-item elements specifically (role + tabindex
    // together) rather than bare `role="menuitem"`, since the page's own
    // script block also contains that bare substring once, in a
    // querySelectorAll selector string.
    expect(substr_count($content, 'role="menuitem" tabindex="-1"'))->toBe(3);
});
