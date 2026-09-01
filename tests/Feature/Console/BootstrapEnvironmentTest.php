<?php

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('a fresh environment seeds reference data and creates one owner holding both global roles', function () {
    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'jowner')
        ->expectsQuestion('Full Name', 'Jane Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'jane@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'yes')
        ->assertExitCode(0);

    expect(Role::count())->toBe(5);
    expect(Permission::count())->toBeGreaterThan(0);

    $user = User::where('username', 'jowner')->firstOrFail();
    expect($user->email)->toBe('jane@example.com');
    expect($user->employee_id)->toBe('EMP-0001');
    expect($user->roles->pluck('slug')->sort()->values()->all())->toBe(['owner', 'super_admin']);

    // Nothing beyond roles/permissions/this one user — no companies,
    // departments, projects, or other business data.
    expect(User::count())->toBe(1);
});

test('running the command twice does not duplicate roles/permissions and does not silently create a second owner', function () {
    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'first.owner')
        ->expectsQuestion('Full Name', 'First Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'first@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'yes')
        ->assertExitCode(0);

    $roleCount = Role::count();
    $permissionCount = Permission::count();
    $rolePermissionCount = DB::table('role_permissions')->count();
    expect(User::count())->toBe(1);

    // Second run — declines the "add another owner?" prompt, so the
    // command never even reaches the owner-detail questions.
    $this->artisan('solva:bootstrap')
        ->expectsConfirmation('Are you sure you want to add another owner account?', 'no')
        ->assertExitCode(0);

    expect(Role::count())->toBe($roleCount);
    expect(Permission::count())->toBe($permissionCount);
    expect(DB::table('role_permissions')->count())->toBe($rolePermissionCount);
    expect(User::count())->toBe(1);
});

test('explicitly confirming "yes" on a second run does create a second owner', function () {
    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'first.owner')
        ->expectsQuestion('Full Name', 'First Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'first@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'yes')
        ->assertExitCode(0);

    $this->artisan('solva:bootstrap')
        ->expectsConfirmation('Are you sure you want to add another owner account?', 'yes')
        ->expectsQuestion('Username', 'second.owner')
        ->expectsQuestion('Full Name', 'Second Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'second@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567891')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'yes')
        ->assertExitCode(0);

    expect(User::count())->toBe(2);
    expect(User::whereHas('roles', fn ($query) => $query->where('slug', Role::OWNER))->count())->toBe(2);
});

test('a weak password is rejected and re-prompted rather than accepted silently', function () {
    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'jowner')
        ->expectsQuestion('Full Name', 'Jane Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'jane@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->expectsQuestion('Password (hidden input)', 'weak')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'yes')
        ->assertExitCode(0);

    expect(User::where('username', 'jowner')->exists())->toBeTrue();
});

test('an already-taken username or email fails clearly instead of creating a duplicate', function () {
    User::factory()->create(['username' => 'taken', 'email' => 'taken@example.com']);

    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'taken')
        ->expectsQuestion('Full Name', 'Someone Else')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'taken@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->assertExitCode(1);

    expect(User::count())->toBe(1);
});

test('declining the final confirmation creates no owner account', function () {
    $this->artisan('solva:bootstrap')
        ->expectsQuestion('Username', 'jowner')
        ->expectsQuestion('Full Name', 'Jane Owner')
        ->expectsQuestion('Employee ID (leave blank to auto-generate)', '')
        ->expectsQuestion('Email', 'jane@example.com')
        ->expectsQuestion('Phone (with country code)', '+6281234567890')
        ->expectsQuestion('Password (hidden input)', 'Str0ng!Passw0rd')
        ->expectsConfirmation('Create this owner account?', 'no')
        ->assertExitCode(0);

    expect(User::count())->toBe(0);
    // Reference data is still seeded even when the owner creation itself
    // is declined at the last step.
    expect(Role::count())->toBe(5);
});

// -- --reset-owner --------------------------------------------------

test('--reset-owner lists existing owner/super_admin accounts', function () {
    $owner = createOwner();

    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->expectsOutputToContain("[1] {$owner->username} — {$owner->name} <{$owner->email}> (Owner)")
        ->expectsQuestion('Which account do you want to reset? Enter the number.', '1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Type RESET to confirm', 'RESET')
        ->assertExitCode(0);
});

test('selecting an account, providing a valid confirmed new password, and confirming updates the password and sets must_change_password', function () {
    $owner = createOwner()->refresh();
    expect($owner->must_change_password)->toBeFalse();

    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->expectsQuestion('Which account do you want to reset? Enter the number.', '1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Type RESET to confirm', 'RESET')
        ->assertExitCode(0);

    $owner->refresh();
    expect(Hash::check('N3wStr0ng!Pass', $owner->password))->toBeTrue();
    expect($owner->must_change_password)->toBeTrue();
});

test('resetting a password writes an audit_log entry tagged as a CLI recovery action', function () {
    $owner = createOwner();

    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->expectsQuestion('Which account do you want to reset? Enter the number.', '1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Type RESET to confirm', 'RESET')
        ->assertExitCode(0);

    $entry = AuditLog::where('entity_type', 'user')
        ->where('entity_id', $owner->id)
        ->where('action', 'user.password_reset_via_cli')
        ->firstOrFail();

    expect($entry->user_id)->toBeNull();
    expect($entry->organization_id)->toBeNull();
    expect($entry->changes['source'])->toBe('cli_recovery');
    expect($entry->changes['triggered_from'])->toBe('solva:bootstrap --reset-owner');
});

test('mismatched password confirmation is rejected — only a matching pair is ever applied', function () {
    $owner = createOwner();
    $originalPasswordHash = $owner->password;

    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->expectsQuestion('Which account do you want to reset? Enter the number.', '1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'DoesNotMatch!1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Type RESET to confirm', 'RESET')
        ->assertExitCode(0);

    $owner->refresh();
    expect($owner->password)->not->toBe($originalPasswordHash);
    expect(Hash::check('N3wStr0ng!Pass', $owner->password))->toBeTrue();
    // Only one audit_log entry — the mismatched attempt above never
    // reached applyPasswordReset() at all, let alone wrote a row.
    expect(AuditLog::where('entity_id', $owner->id)->where('action', 'user.password_reset_via_cli')->count())->toBe(1);
});

test('typing anything other than RESET at the final confirmation aborts without changing the password', function () {
    $owner = createOwner();
    $originalPasswordHash = $owner->password;

    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->expectsQuestion('Which account do you want to reset? Enter the number.', '1')
        ->expectsQuestion('New password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Confirm new password (hidden input)', 'N3wStr0ng!Pass')
        ->expectsQuestion('Type RESET to confirm', 'yes')
        ->assertExitCode(0);

    $owner->refresh();
    expect($owner->password)->toBe($originalPasswordHash);
    expect($owner->must_change_password)->toBeFalse();
    expect(AuditLog::where('entity_id', $owner->id)->where('action', 'user.password_reset_via_cli')->exists())->toBeFalse();
});

test('--reset-owner explains clearly when no owner/super_admin account exists, and cannot create one', function () {
    $this->artisan('solva:bootstrap', ['--reset-owner' => true])
        ->assertExitCode(1);

    expect(User::count())->toBe(0);
});
