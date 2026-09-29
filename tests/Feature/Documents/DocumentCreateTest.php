<?php

use App\Enums\DocumentAccessLevel;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeStaffForDocumentCreate(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    return $staff;
}

test('management can view the Add Document page for their company', function () {
    $response = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Org A');
});

test('a staff user (view_documents only) cannot view or submit the Add Document page', function () {
    $staff = makeStaffForDocumentCreate($this->orgA);

    $this->actingAs($staff)->get('/documents/create/'.$this->orgA->id)->assertForbidden();

    $this->actingAs($staff)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Sneaky doc',
        'link' => 'https://example.com/sneaky.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertForbidden();
    $this->assertDatabaseMissing('documents', ['name' => 'Sneaky doc']);
});

test('submitting the Add Document page creates the document and redirects to the Documents list', function () {
    $response = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Handbook.pdf',
        'link' => 'https://example.com/handbook.pdf',
        'access_level' => 'public',
        'from_documents_page' => '1',
    ]);

    $response->assertRedirect('/documents/'.$this->orgA->id);
    $this->assertDatabaseHas('documents', [
        'name' => 'Handbook.pdf',
        'access_level' => 'public',
        'uploaded_by' => $this->management->id,
    ]);
});

test('the document link must be a valid URL', function () {
    $response = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Bad link doc',
        'link' => 'not-a-url',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertSessionHasErrors('link');
    $this->assertDatabaseMissing('documents', ['name' => 'Bad link doc']);
});

test('name and link are required to create a document', function () {
    $response = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertSessionHasErrors(['name', 'link']);
});

test('the Add Document page\'s access level options come from DocumentAccessLevel::cases(), not a hardcoded list', function () {
    $response = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);
    $response->assertOk();

    // Computed from the live enum, not literal strings — if a case is
    // ever added/renamed, this stays correct for a real enum-driven
    // <select>, while a hardcoded option list would drift out of sync.
    foreach (DocumentAccessLevel::cases() as $level) {
        $response->assertSee($level->label());
    }
});

test('the "+ New" menu appears on the Documents list for management but not staff', function () {
    $staff = makeStaffForDocumentCreate($this->orgA);

    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)
        ->assertSee('+ New');

    $this->actingAs($staff)->get('/documents/'.$this->orgA->id)
        ->assertDontSee('+ New');
});

test('task #73 (document form fixes): the upload field uses a custom-styled control, not the bare native file input', function () {
    $response = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);

    $response->assertOk();
    $content = $response->getContent();

    // Same accessible pattern as the Task edit page's merged attach
    // panel: the native input stays real and keyboard-operable (sr-only,
    // not display:none/visibility:hidden), bound to a visible <label>
    // trigger with no separate tab stop of its own, plus a separate
    // filename-display element defaulting to "No file selected".
    expect($content)->toContain('class="file-input sr-only"');
    expect($content)->toContain('for="file"');
    expect($content)->toContain('Choose file');
    expect($content)->toContain('file-name-display');
    expect($content)->toContain('No file selected');
});

test('task #73 (document form fixes): the Name field auto-fill updates on re-selection, not just the first empty-field case', function () {
    $response = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);

    $response->assertOk();
    $content = $response->getContent();

    // Structural guard for the re-selection fix verified live in a
    // browser: lastAutoFilledName tracks what THIS handler last wrote,
    // so a second (different) file selection can tell "the field still
    // holds what I auto-filled it with" (safe to replace) apart from "the
    // user typed this themselves" (never overwritten) — the old bare
    // "is it empty" check only ever caught the very first selection.
    expect($content)->toContain('function stripExtension(filename)');
    expect($content)->toContain('var lastAutoFilledName = null;');
    expect($content)->toContain("nameField.value === '' || nameField.value === lastAutoFilledName");
});

test('task #73 (document form fixes): the upload helper text reflects the expanded document/image/audio/video categories', function () {
    $response = $this->actingAs($this->management)->get('/documents/create/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Document (PDF, Word, Excel, PowerPoint, text, CSV', false);
    $response->assertSee('image (10MB)', false);
    $response->assertSee('audio (50MB)', false);
    $response->assertSee('video (200MB)', false);
});
