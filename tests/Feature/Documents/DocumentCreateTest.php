<?php

use App\Enums\DocumentAccessLevel;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

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

test('the "+ Add new document" button appears on the Documents list for management but not staff', function () {
    $staff = makeStaffForDocumentCreate($this->orgA);

    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)
        ->assertSee('+ Add new document');

    $this->actingAs($staff)->get('/documents/'.$this->orgA->id)
        ->assertDontSee('+ Add new document');
});
