<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * task #72 phase 4: PATCH /tasks/per-page, behind the Tasks list's "Per
 * page" box. Saves 1–100 on the signed-in user only, and goes back to the
 * list it came from on page 1 with the same filters and sort.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Per Page Org', 'slug' => 'per-page-org', 'accent_color' => '#1D9E75']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $this->listUrl = route('tasks.index', $this->org).'?'.http_build_query([
        'direction' => 'desc', 'page' => 3, 'q' => 'launch', 'show_inactive' => 1, 'sort' => 'title',
    ]);
    $this->listUrlOnPageOne = route('tasks.index', $this->org).'?'.http_build_query([
        'direction' => 'desc', 'q' => 'launch', 'show_inactive' => 1, 'sort' => 'title',
    ]);
});

test('saving 50 stores it on the user and returns to page 1 with the same filters and sort', function () {
    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), ['per_page' => '50', 'return_to' => $this->listUrl])
        ->assertRedirect($this->listUrlOnPageOne)
        ->assertSessionMissing('tasks_per_page_hint');

    expect($this->management->fresh()->tasks_per_page)->toBe(50);
});

test('values outside 1 to 100 are clamped, and the list says so', function (string $sent, int $saved, string $hint) {
    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), ['per_page' => $sent, 'return_to' => $this->listUrl])
        ->assertRedirect($this->listUrlOnPageOne)
        ->assertSessionHas('tasks_per_page_hint', $hint);

    expect($this->management->fresh()->tasks_per_page)->toBe($saved);

    // And the hint shows on the list after the reload.
    $this->actingAs($this->management)->get($this->listUrlOnPageOne)->assertSee($hint);
})->with([
    '0' => ['0', 1, 'Showing 1 per page, the minimum.'],
    '-5' => ['-5', 1, 'Showing 1 per page, the minimum.'],
    '101' => ['101', 100, 'Showing 100 per page, the maximum.'],
    '150' => ['150', 100, 'Showing 100 per page, the maximum.'],
    'a huge number' => ['99999999999999999999999', 100, 'Showing 100 per page, the maximum.'],
]);

test('anything that is not a whole number is ignored and the list comes back unchanged', function (mixed $sent) {
    $this->management->update(['tasks_per_page' => 40]);

    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), ['per_page' => $sent, 'return_to' => $this->listUrl])
        ->assertRedirect($this->listUrl)
        ->assertSessionMissing('tasks_per_page_hint');

    expect($this->management->fresh()->tasks_per_page)->toBe(40);
})->with([
    'abc' => ['abc'],
    'empty' => [''],
    'a decimal' => ['2.5'],
    'an array' => [['50']],
]);

test('one user\'s page size never changes another user\'s', function () {
    $other = User::factory()->create(['tasks_per_page' => 10]);

    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), ['per_page' => '60', 'return_to' => $this->listUrl]);

    expect($this->management->fresh()->tasks_per_page)->toBe(60)
        ->and($other->fresh()->tasks_per_page)->toBe(10);
});

test('an off-site or missing return URL goes to the Tasks list instead', function (?string $returnTo) {
    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), array_filter(['per_page' => '30', 'return_to' => $returnTo]))
        ->assertRedirect(route('tasks.index'));

    expect($this->management->fresh()->tasks_per_page)->toBe(30);
})->with([
    'another host' => ['https://evil.example.com/tasks?page=2'],
    'protocol-relative' => ['//evil.example.com/tasks'],
    'missing' => [null],
]);

test('a relative return URL on this site is honoured', function () {
    $this->actingAs($this->management)
        ->patch(route('tasks.per-page'), ['per_page' => '30', 'return_to' => '/tasks/'.$this->org->id.'?page=4&sort=status'])
        ->assertRedirect('/tasks/'.$this->org->id.'?sort=status');
});

test('guests are sent to the login page and nothing is saved', function () {
    $this->patch(route('tasks.per-page'), ['per_page' => '50', 'return_to' => $this->listUrl])
        ->assertRedirect(route('login'));
});
