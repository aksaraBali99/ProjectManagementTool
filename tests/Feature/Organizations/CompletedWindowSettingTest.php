<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 7: organizations.completed_window_days — 7, 14 or 30,
 * default 14, changed on the company edit screen by owners and super
 * admins only (checked on the server).
 */
beforeEach(function () {
    setUpCompletedWindowCompany($this);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);
});

function companyUpdate(Organization $organization, array $overrides = []): array
{
    return array_merge(['name' => $organization->name, 'accent_color' => $organization->accent_color], $overrides);
}

test('a new company defaults to 14 days, through Eloquent and through a raw insert', function () {
    expect($this->org->fresh()->completed_window_days)->toBe(14)
        ->and($this->org->completed_window_days)->toBe(14);

    $id = DB::table('organizations')->insertGetId(['name' => 'Raw Co', 'slug' => 'raw-co', 'accent_color' => '#000000', 'is_active' => true]);
    expect(Organization::find($id)->completed_window_days)->toBe(14);
});

test('a company created from the Add Company form gets 14', function () {
    $this->actingAs($this->owner)->post(route('organizations.store'), ['name' => 'Form Co', 'accent_color' => '#123456'])->assertRedirect();

    expect(Organization::where('name', 'Form Co')->sole()->completed_window_days)->toBe(14);
});

test('owners and super admins can set 7, 14 or 30', function (string $who, int $days) {
    $this->actingAs($this->{$who})
        ->put(route('organizations.update', $this->org), companyUpdate($this->org, ['completed_window_days' => $days]))
        ->assertRedirect(route('organizations.index'))
        ->assertSessionHasNoErrors();

    expect($this->org->fresh()->completed_window_days)->toBe($days);
})->with(['owner', 'superAdmin'])->with([7, 14, 30]);

test('the edit screen shows the select with the current value', function () {
    $this->org->update(['completed_window_days' => 30]);

    $html = $this->actingAs($this->owner)->get(route('organizations.edit', $this->org))->assertOk()->getContent();

    expect($html)->toContain('name="completed_window_days"')
        ->toMatch('/<option value="30"\s+selected>30 days<\/option>/');
});

test('other values are rejected and nothing changes', function (mixed $value) {
    $this->actingAs($this->owner)
        ->put(route('organizations.update', $this->org), companyUpdate($this->org, ['completed_window_days' => $value]))
        ->assertSessionHasErrors('completed_window_days');

    expect($this->org->fresh()->completed_window_days)->toBe(14);
})->with([0, 1, 15, 31, 90, -7, 'abc', '']);

test('management, staff and clients cannot change it', function (string $who) {
    $this->actingAs($this->{$who})->get(route('organizations.edit', $this->org))->assertForbidden();
    $this->actingAs($this->{$who})
        ->put(route('organizations.update', $this->org), companyUpdate($this->org, ['completed_window_days' => 7]))
        ->assertForbidden();

    expect($this->org->fresh()->completed_window_days)->toBe(14);
})->with(['management', 'staff', 'client']);

test('an update without the field keeps the current window', function () {
    $this->org->update(['completed_window_days' => 30]);

    $this->actingAs($this->owner)->put(route('organizations.update', $this->org), companyUpdate($this->org, ['name' => 'Renamed']))
        ->assertSessionHasNoErrors();

    expect($this->org->fresh())->name->toBe('Renamed')->completed_window_days->toBe(30);
});

test('the error message lists the allowed choices from the one constant', function () {
    $this->actingAs($this->owner)
        ->put(route('organizations.update', $this->org), companyUpdate($this->org, ['completed_window_days' => 15]))
        ->assertSessionHasErrors(['completed_window_days' => 'The completed tasks window must be 7, 14 or 30 days.']);
});
