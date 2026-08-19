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
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'newperson',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'New Person',
        'employee_id' => 'EMP-9001',
        'email' => 'newperson@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [
            $this->orgA->id => 'management',
            $this->orgB->id => 'staff',
        ],
    ]);

    $response->assertRedirect('/users');

    $user = User::where('username', 'newperson')->firstOrFail();
    expect($user->isManagementInOrg($this->orgA->id))->toBeTrue()
        ->and($user->isStaffInOrg($this->orgB->id))->toBeTrue()
        ->and($user->isManagementInOrg($this->orgB->id))->toBeFalse();
});

test('creating a user requires at least one company role', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'noaccess',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'No Access',
        'employee_id' => 'EMP-9002',
        'email' => 'noaccess@example.com',
        'phone' => '+1 202 555 0101',
        'roles' => [
            $this->orgA->id => 'none',
            $this->orgB->id => 'none',
        ],
    ]);

    $response->assertSessionHasErrors('roles');
    $this->assertDatabaseMissing('users', ['username' => 'noaccess']);
});

test('creating a user requires a safe password', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'weakpass',
        'password' => 'password',
        'name' => 'Weak Pass',
        'employee_id' => 'EMP-9003',
        'email' => 'weakpass@example.com',
        'phone' => '+1 202 555 0102',
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('users', ['username' => 'weakpass']);
});

test('an owner can edit a user and change their role from management in one company to staff, while adding a second company', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['management']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
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

test('editing a user rejects an email with no TLD on the domain', function () {
    $target = User::factory()->create(['email' => 'alex.management@solva.test']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['management']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => '',
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => 'alex.management@solva',
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'management'],
    ]);

    $response->assertSessionHasErrors(['username', 'email']);
    expect($target->fresh()->email)->toBe('alex.management@solva.test');
});

test('setting a company role to none removes the org_members row', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
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
        'email' => $target->email,
        'phone' => $target->phone,
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
        'email' => $this->superAdmin->email,
        'phone' => $this->superAdmin->phone,
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
        'email' => $this->superAdmin->email,
        'phone' => $this->superAdmin->phone,
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
    'no-tld@example',
]);

test('creating a user rejects an invalid email format', function (string $email) {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'emailtest',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Email Test',
        'employee_id' => 'EMP-9010',
        'email' => $email,
        'phone' => '+1 202 555 0100',
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionHasErrors('email');
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
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'phonetest',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Phone Test',
        'employee_id' => 'EMP-9011',
        'email' => 'phonetest@example.com',
        'phone' => $phone,
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionHasErrors('phone');
    $this->assertDatabaseMissing('users', ['username' => 'phonetest']);
})->with('invalidPhones');

dataset('validPhones', [
    '+1 202 555 0100',
    '+44 20 7946 0958',
    '+62 811-0000-0001',
    '08123456789',
]);

test('creating a user accepts a valid phone format', function (string $phone) {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'validphone',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Valid Phone',
        'employee_id' => 'EMP-9012',
        'email' => 'validphone@example.com',
        'phone' => $phone,
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionDoesntHaveErrors('phone');
    $this->assertDatabaseHas('users', ['username' => 'validphone']);
})->with('validPhones');

test('a valid E.164 phone number saves and round-trips into the edit form correctly', function () {
    $this->actingAs($this->owner)->post('/users', [
        'username' => 'roundtripphone',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Round Trip Phone',
        'employee_id' => 'EMP-9013',
        'email' => 'roundtripphone@example.com',
        'phone' => '+6281234567890',
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $user = User::where('username', 'roundtripphone')->firstOrFail();
    expect($user->phone)->toBe('+6281234567890');

    $response = $this->actingAs($this->owner)->get("/users/{$user->id}/edit");

    $response->assertOk();
    // The server hands the picker the raw stored E.164 value; the picker
    // itself (JS, verified live in-browser) then renders the flag + national
    // format from it — this asserts the server side of that contract.
    $response->assertSee('value="+6281234567890"', false);
});

test('updating a user rejects an invalid phone format', function (string $phone) {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $phone,
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionHasErrors('phone');
    expect($target->fresh()->phone)->not->toBe($phone);
})->with('invalidPhones');
