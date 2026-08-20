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

    $this->targetOwner = User::factory()->create();
    $this->targetOwner->roles()->attach($this->roles['owner']->id);
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
    $target = User::factory()->create(['email' => 'alex.management@solava.test']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['management']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => '',
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => 'alex.management@solava',
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'management'],
    ]);

    $response->assertSessionHasErrors(['username', 'email']);
    expect($target->fresh()->email)->toBe('alex.management@solava.test');
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

test('editing an owner does not require or show company roles', function () {
    $response = $this->actingAs($this->owner)->get("/users/{$this->targetOwner->id}/edit");

    $response->assertOk();
    $response->assertSee('Owner');
    $response->assertDontSee('name="roles[', false);
});

test('an owner target can be updated without submitting any company roles', function () {
    $response = $this->actingAs($this->owner)->put("/users/{$this->targetOwner->id}", [
        'username' => $this->targetOwner->username,
        'name' => 'Updated Name',
        'employee_id' => $this->targetOwner->employee_id,
        'email' => $this->targetOwner->email,
        'phone' => $this->targetOwner->phone,
    ]);

    $response->assertRedirect('/users');
    $response->assertSessionHasNoErrors();
    expect($this->targetOwner->fresh()->name)->toBe('Updated Name');
});

test('submitting company roles for an owner target does not create org_members rows', function () {
    $this->actingAs($this->owner)->put("/users/{$this->targetOwner->id}", [
        'username' => $this->targetOwner->username,
        'name' => $this->targetOwner->name,
        'employee_id' => $this->targetOwner->employee_id,
        'email' => $this->targetOwner->email,
        'phone' => $this->targetOwner->phone,
        'roles' => [$this->orgA->id => 'management'],
    ]);

    expect(OrgMember::where('user_id', $this->targetOwner->id)->exists())->toBeFalse();
});

test('a super-admin-only target now shows editable company roles and a pre-checked super admin toggle', function () {
    $response = $this->actingAs($this->owner)->get("/users/{$this->superAdmin->id}/edit");

    $response->assertOk();
    $response->assertSee('name="roles[', false);
    $response->assertSee('name="grant_super_admin" value="1" checked', false);
});

test('an owner can assign the client role to a company', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'clientuser',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Client User',
        'employee_id' => 'EMP-9020',
        'email' => 'clientuser@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [$this->orgA->id => 'client'],
    ]);

    $response->assertRedirect('/users');

    $user = User::where('username', 'clientuser')->firstOrFail();
    $membership = OrgMember::where('user_id', $user->id)->where('organization_id', $this->orgA->id)->firstOrFail();
    expect($membership->role_id)->toBe($this->roles['client']->id);
});

test('assigning the client role in two companies is rejected', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'multiclient',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Multi Client',
        'employee_id' => 'EMP-9023',
        'email' => 'multiclient@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [
            $this->orgA->id => 'client',
            $this->orgB->id => 'client',
        ],
    ]);

    $response->assertSessionHasErrors('roles');
    $this->assertDatabaseMissing('users', ['username' => 'multiclient']);
});

test('assigning the client role in one company and another role in a different company is rejected', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'clientplusstaff',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Client Plus Staff',
        'employee_id' => 'EMP-9024',
        'email' => 'clientplusstaff@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [
            $this->orgA->id => 'client',
            $this->orgB->id => 'staff',
        ],
    ]);

    $response->assertSessionHasErrors('roles');
    $this->assertDatabaseMissing('users', ['username' => 'clientplusstaff']);
});

test('updating a user to hold the client role alongside another company role is rejected', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [
            $this->orgA->id => 'staff',
            $this->orgB->id => 'client',
        ],
    ]);

    $response->assertSessionHasErrors('roles');
    expect(OrgMember::where('user_id', $target->id)->where('organization_id', $this->orgB->id)->exists())->toBeFalse();
});

test('changing a user\'s only company role from client to staff is allowed', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['client']->id]);

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    $response->assertSessionHasNoErrors();
    expect($target->fresh()->isStaffInOrg($this->orgA->id))->toBeTrue();
});

test('an owner can grant super admin to a new user via the checkbox', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'newsuperadmin',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'New Super Admin',
        'employee_id' => 'EMP-9021',
        'email' => 'newsuperadmin@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [$this->orgA->id => 'none'],
        'grant_super_admin' => '1',
    ]);

    $response->assertRedirect('/users');

    $user = User::where('username', 'newsuperadmin')->firstOrFail();
    expect($user->roles()->where('slug', 'super_admin')->exists())->toBeTrue();
});

test('an owner can grant super admin to an existing staff member via the checkbox', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'staff'],
        'grant_super_admin' => '1',
    ]);

    expect($target->fresh()->roles()->where('slug', 'super_admin')->exists())->toBeTrue()
        ->and($target->fresh()->isStaffInOrg($this->orgA->id))->toBeTrue();
});

test('an owner can revoke super admin by unchecking the checkbox', function () {
    // Revoking the global grant still requires the user to land on at
    // least one company role — same "one access path or another" rule
    // enforced for any other user.
    $this->actingAs($this->owner)->put("/users/{$this->superAdmin->id}", [
        'username' => $this->superAdmin->username,
        'name' => $this->superAdmin->name,
        'employee_id' => $this->superAdmin->employee_id,
        'email' => $this->superAdmin->email,
        'phone' => $this->superAdmin->phone,
        'roles' => [$this->orgA->id => 'staff'],
    ]);

    expect($this->superAdmin->fresh()->roles()->where('slug', 'super_admin')->exists())->toBeFalse();
});

test('a super admin who is not owner cannot grant super admin via a tampered request', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $this->actingAs($this->superAdmin)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'staff'],
        'grant_super_admin' => '1',
    ]);

    expect($target->fresh()->roles()->where('slug', 'super_admin')->exists())->toBeFalse();
});

test('granting super admin makes a company role optional', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'globalonly',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'Global Only',
        'employee_id' => 'EMP-9022',
        'email' => 'globalonly@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [$this->orgA->id => 'none'],
        'grant_super_admin' => '1',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/users');
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
