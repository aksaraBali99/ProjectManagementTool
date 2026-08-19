<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->roles = collect(['super_admin', 'owner', 'management', 'staff', 'client'])
        ->mapWithKeys(fn ($slug) => [$slug => Role::create(['name' => ucfirst($slug), 'slug' => $slug, 'is_system' => true])]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach($this->roles['owner']->id);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->roles()->attach($this->roles['super_admin']->id);
});

function validUserPayload(array $overrides = []): array
{
    return array_merge([
        'username' => 'newperson',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'New Person',
        'employee_id' => 'EMP-9001',
        'emails' => [['label' => '', 'value' => 'newperson@example.com']],
        'phones' => [['label' => '', 'value' => '+1 202 555 0100']],
    ], $overrides);
}

test('a non-owner/non-super-admin cannot access the user management pages', function () {
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $this->roles['management']->id]);

    $this->actingAs($manager)->get('/users')->assertForbidden();
    $this->actingAs($manager)->get('/users/create')->assertForbidden();
});

test('an owner can view the user management page', function () {
    $this->actingAs($this->owner)->get('/users')->assertOk();
});

test('a super admin can view the user management page', function () {
    $this->actingAs($this->superAdmin)->get('/users')->assertOk();
});

test('an owner can create a user with management in one company and staff in another', function () {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'roles' => [
            $this->orgA->id => 'management',
            $this->orgB->id => 'staff',
        ],
    ]));

    $response->assertRedirect('/users');

    $user = User::where('username', 'newperson')->firstOrFail();
    expect($user->isManagementInOrg($this->orgA->id))->toBeTrue()
        ->and($user->isStaffInOrg($this->orgB->id))->toBeTrue()
        ->and($user->isManagementInOrg($this->orgB->id))->toBeFalse()
        ->and($user->primaryEmail())->toBe('newperson@example.com')
        ->and($user->primaryPhone())->toBe('+1 202 555 0100');
});

test('creating a user requires at least one company role', function () {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'noaccess',
        'employee_id' => 'EMP-9002',
        'emails' => [['label' => '', 'value' => 'noaccess@example.com']],
        'roles' => [
            $this->orgA->id => 'none',
            $this->orgB->id => 'none',
        ],
    ]));

    $response->assertSessionHasErrors('roles');
    $this->assertDatabaseMissing('users', ['username' => 'noaccess']);
});

test('creating a user requires a safe password', function () {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'weakpass',
        'password' => 'password',
        'employee_id' => 'EMP-9003',
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('users', ['username' => 'weakpass']);
});

test('creating a user requires at least one email and one phone', function () {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'nocontact',
        'employee_id' => 'EMP-9004',
        'emails' => [],
        'phones' => [],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionHasErrors(['emails', 'phones']);
    $this->assertDatabaseMissing('users', ['username' => 'nocontact']);
});

test('an owner can create a user with multiple emails and phones, auto-numbering blank labels', function () {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'multicontact',
        'employee_id' => 'EMP-9005',
        'emails' => [
            ['label' => 'Work email', 'value' => 'multicontact.work@example.com'],
            ['label' => '', 'value' => 'multicontact.other@example.com'],
            ['label' => '', 'value' => 'multicontact.third@example.com'],
        ],
        'phones' => [
            ['label' => 'Mobile phone number', 'value' => '+1 202 555 0111'],
            ['label' => '', 'value' => '+1 202 555 0112'],
        ],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertRedirect('/users');

    $user = User::where('username', 'multicontact')->firstOrFail();
    expect($user->emails->pluck('label')->all())->toBe(['Work email', 'Email 1', 'Email 2'])
        ->and($user->phones->pluck('label')->all())->toBe(['Mobile phone number', 'Phone number 1']);
});

test('creating a user rejects an email already used by another user', function () {
    $existing = User::factory()->withEmail('taken@example.com')->create();

    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'dupeemail',
        'employee_id' => 'EMP-9006',
        'emails' => [['label' => '', 'value' => 'taken@example.com']],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionHasErrors('emails.0.value');
    $this->assertDatabaseMissing('users', ['username' => 'dupeemail']);
});

test('an owner can edit a user and change their role from management in one company to staff, while adding a second company', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['management']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'emails' => [['label' => '', 'value' => $target->primaryEmail()]],
        'phones' => [['label' => '', 'value' => $target->primaryPhone()]],
        'roles' => [
            $this->orgA->id => 'staff',
            $this->orgB->id => 'management',
        ],
    ]);

    $response->assertRedirect('/users');

    $target->refresh();
    expect($target->isStaffInOrg($this->orgA->id))->toBeTrue()
        ->and($target->isManagementInOrg($this->orgA->id))->toBeFalse()
        ->and($target->isManagementInOrg($this->orgB->id))->toBeTrue();
});

test('editing a user replaces their emails and phones with the submitted set', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'emails' => [['label' => 'New email', 'value' => 'brandnew@example.com']],
        'phones' => [['label' => 'New phone', 'value' => '+1 202 555 0199']],
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $target->refresh();
    expect($target->emails)->toHaveCount(1)
        ->and($target->primaryEmail())->toBe('brandnew@example.com')
        ->and($target->phones)->toHaveCount(1)
        ->and($target->primaryPhone())->toBe('+1 202 555 0199');
});

test('editing a user does not reject their own existing email as a duplicate', function () {
    $target = User::factory()->withEmail('keep-mine@example.com')->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'emails' => [['label' => '', 'value' => 'keep-mine@example.com']],
        'phones' => [['label' => '', 'value' => $target->primaryPhone()]],
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionDoesntHaveErrors('emails.0.value');
});

test('setting a company role to none removes the org_members row', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'emails' => [['label' => '', 'value' => $target->primaryEmail()]],
        'phones' => [['label' => '', 'value' => $target->primaryPhone()]],
        'roles' => [
            $this->orgA->id => 'none',
            $this->orgB->id => 'management',
        ],
    ]);

    expect(OrgMember::where('user_id', $target->id)->where('organization_id', $this->orgA->id)->exists())->toBeFalse();
});

test('a non-owner/non-super-admin cannot update another user', function () {
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $this->roles['management']->id]);
    $target = User::factory()->create();

    $response = $this->actingAs($manager)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'emails' => [['label' => '', 'value' => $target->primaryEmail()]],
        'phones' => [['label' => '', 'value' => $target->primaryPhone()]],
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertForbidden();
});

test('an owner can deactivate and reactivate a user', function () {
    $target = User::factory()->create(['is_active' => true]);

    $this->actingAs($this->owner)->patch("/users/{$target->id}/toggle-active");
    expect($target->fresh()->is_active)->toBeFalse();

    $this->actingAs($this->owner)->patch("/users/{$target->id}/toggle-active");
    expect($target->fresh()->is_active)->toBeTrue();
});

test('changing a password requires matching fields and a safe password', function () {
    $target = User::factory()->create();

    $mismatch = $this->actingAs($this->owner)->put("/users/{$target->id}/password", [
        'password' => 'Str0ng!Passw0rd',
        'password_confirmation' => 'Different!Passw0rd',
    ]);
    $mismatch->assertSessionHasErrors('password');

    $weak = $this->actingAs($this->owner)->put("/users/{$target->id}/password", [
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $weak->assertSessionHasErrors('password');
});

test('a valid password change succeeds and the new password works for login', function () {
    $target = User::factory()->create(['username' => 'passwordtest']);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}/password", [
        'password' => 'Str0ng!Passw0rd',
        'password_confirmation' => 'Str0ng!Passw0rd',
    ]);
    $response->assertRedirect(route('users.edit', $target));
    $response->assertSessionHasNoErrors();

    $this->post('/logout');

    $login = $this->post('/login', [
        'identifier' => 'passwordtest',
        'password' => 'Str0ng!Passw0rd',
    ]);
    $login->assertRedirect('/dashboard');
});

test('editing a user with a global role does not require or show company roles', function () {
    $response = $this->actingAs($this->owner)->get("/users/{$this->superAdmin->id}/edit");

    $response->assertOk();
    $response->assertSee('Super_admin');
    $response->assertDontSee('name="roles[', false);
});

test('a global-role user can be updated without submitting any company roles', function () {
    $response = $this->actingAs($this->owner)->put("/users/{$this->superAdmin->id}", [
        'username' => $this->superAdmin->username,
        'name' => 'Updated Name',
        'employee_id' => $this->superAdmin->employee_id,
        'emails' => [['label' => '', 'value' => $this->superAdmin->primaryEmail()]],
        'phones' => [['label' => '', 'value' => $this->superAdmin->primaryPhone()]],
    ]);

    $response->assertRedirect('/users');
    $response->assertSessionHasNoErrors();
    expect($this->superAdmin->fresh()->name)->toBe('Updated Name');
});

test('submitting company roles for a global-role user does not create org_members rows', function () {
    $this->actingAs($this->owner)->put("/users/{$this->superAdmin->id}", [
        'username' => $this->superAdmin->username,
        'name' => $this->superAdmin->name,
        'employee_id' => $this->superAdmin->employee_id,
        'emails' => [['label' => '', 'value' => $this->superAdmin->primaryEmail()]],
        'phones' => [['label' => '', 'value' => $this->superAdmin->primaryPhone()]],
        'roles' => [$this->orgA->id => 'management'],
    ]);

    expect(OrgMember::where('user_id', $this->superAdmin->id)->exists())->toBeFalse();
});

test('the user index shows a global role badge instead of "no company access"', function () {
    $response = $this->actingAs($this->owner)->get('/users');

    $response->assertOk();
    $response->assertSee('Owner');
    $response->assertSee('Super_admin');
});

dataset('invalidEmails', [
    'not-an-email',
    'missing-domain@',
    '@missing-local.com',
    'no-at-sign.example.com',
    'spaces in@email.com',
    'double@@example.com',
]);

test('creating a user rejects an invalid email format', function (string $email) {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'emailtest',
        'employee_id' => 'EMP-9010',
        'emails' => [['label' => '', 'value' => $email]],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionHasErrors('emails.0.value');
    $this->assertDatabaseMissing('users', ['username' => 'emailtest']);
})->with('invalidEmails');

dataset('invalidPhones', [
    'not-a-phone-number',
    '123',
    '12345678901234567890',
    '+1 555-CALL-NOW',
    '++1 555 0100',
]);

test('creating a user rejects an invalid phone format', function (string $phone) {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'phonetest',
        'employee_id' => 'EMP-9011',
        'phones' => [['label' => '', 'value' => $phone]],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionHasErrors('phones.0.value');
    $this->assertDatabaseMissing('users', ['username' => 'phonetest']);
})->with('invalidPhones');

dataset('validPhones', [
    '+1 202 555 0175',
    '+62 811-0000-0001',
    '08123456789',
    '0812 3456 7890',
]);

test('creating a user accepts a valid phone format', function (string $phone) {
    $response = $this->actingAs($this->owner)->post('/users', validUserPayload([
        'username' => 'validphone',
        'employee_id' => 'EMP-9012',
        'phones' => [['label' => '', 'value' => $phone]],
        'roles' => [$this->orgA->id => 'staff'],
    ]));

    $response->assertSessionDoesntHaveErrors('phones.0.value');
    $this->assertDatabaseHas('users', ['username' => 'validphone']);
})->with('validPhones');
