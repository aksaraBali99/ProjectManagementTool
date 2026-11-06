<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

/**
 * task #121: the Add/Edit Task page's back link survives a save and a
 * Deactivate/Activate. The forms carry return_to/return_label as hidden
 * inputs; store(), update() and toggleActive() redirect with them
 * (ResolvesReturnTo::returnToParams()). The Tasks list's Deactivate/Activate
 * goes back to the exact list URL it was clicked on.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Existing task', 'priority' => 'medium', 'status' => 'pending',
    ]);
    $this->kanbanUrl = url('/kanban/'.$this->org->id);
    $this->kanban = ['return_to' => $this->kanbanUrl, 'return_label' => 'Kanban'];
    $this->actingAs($this->owner);
});

function taskFields(object $context, array $overrides = []): array
{
    return array_merge([
        'project_id' => $context->project->id,
        'department_id' => $context->dept->id,
        'title' => 'A task',
        'priority' => 'medium',
        'status' => 'pending',
    ], $overrides);
}

function newestTask(): Task
{
    return Task::withTrashed()->latest('id')->firstOrFail();
}

/** The edit page shows "← <label>" linking to $url. */
function assertBackLink(string $html, string $label, string $url): void
{
    expect($html)->toContain('← '.$label)
        ->toContain('href="'.e($url).'"');
}

// --- Create and update ------------------------------------------------------

test('creating from Kanban lands on the edit page with the Kanban back link', function () {
    $response = $this->post('/tasks', taskFields($this, $this->kanban));

    $task = newestTask();
    $response->assertRedirect(route('tasks.edit', ['task' => $task, ...$this->kanban]))
        ->assertSessionHas('status', 'Task created.');

    $page = $this->get($response->headers->get('Location'))->assertOk()->getContent();
    assertBackLink($page, 'Kanban', $this->kanbanUrl);
    // The page's own forms carry the pair on for the next save.
    expect($page)->toContain('name="return_to" value="'.e($this->kanbanUrl).'"')
        ->toContain('name="return_label" value="Kanban"');
});

test('updating from Kanban lands back on the edit page with the Kanban back link', function () {
    $response = $this->put("/tasks/{$this->task->id}", taskFields($this, ['title' => 'Renamed', ...$this->kanban]));

    $response->assertRedirect(route('tasks.edit', ['task' => $this->task, ...$this->kanban]))
        ->assertSessionHas('status', 'Task updated.');
    assertBackLink($this->get($response->headers->get('Location'))->getContent(), 'Kanban', $this->kanbanUrl);
    expect($this->task->fresh()->title)->toBe('Renamed');
});

test('without return_to, create and update land on the plain edit page with the Tasks back link', function () {
    $created = $this->post('/tasks', taskFields($this));
    $created->assertRedirect(route('tasks.edit', newestTask()));

    $updated = $this->put("/tasks/{$this->task->id}", taskFields($this));
    $updated->assertRedirect(route('tasks.edit', $this->task));

    $page = $this->get($updated->headers->get('Location'))->getContent();
    assertBackLink($page, 'Tasks', route('tasks.index', $this->org->id));
    // The Tasks fallback is never sent on as an explicit value.
    expect($page)->not->toContain('name="return_to"')->not->toContain('name="return_label"');
});

test('a validation error on create keeps the back link in the form', function () {
    $response = $this->from(route('tasks.create', $this->project))
        ->post('/tasks', taskFields($this, ['title' => '', ...$this->kanban]));

    $response->assertSessionHasErrors('title');
    $page = $this->get(route('tasks.create', $this->project))->getContent();
    expect($page)->toContain('name="return_to" value="'.e($this->kanbanUrl).'"')
        ->toContain('name="return_label" value="Kanban"');
    // The back link agrees with the inputs.
    assertBackLink($page, 'Kanban', $this->kanbanUrl);
});

test('after a validation error, a submitted return_to is checked like any other: an unsafe one never reaches the page', function () {
    $evil = 'javascript://'.appHost().'/%0Aalert(1)';
    $this->from(route('tasks.edit', $this->task))
        ->put("/tasks/{$this->task->id}", taskFields($this, ['title' => '', 'return_to' => $evil, 'return_label' => 'Kanban']))
        ->assertSessionHasErrors('title');

    $page = $this->get(route('tasks.edit', $this->task))->getContent();

    assertBackLink($page, 'Tasks', route('tasks.index', $this->org->id));
    expect($page)->not->toContain('href="javascript:')->not->toContain('alert(1)')->not->toContain('name="return_to"');
});

test('a validation error on edit keeps the back link in the form', function () {
    $response = $this->from(route('tasks.edit', $this->task))
        ->put("/tasks/{$this->task->id}", taskFields($this, ['title' => '', ...$this->kanban]));

    $response->assertSessionHasErrors('title');
    $page = $this->get(route('tasks.edit', $this->task))->getContent();
    expect($page)->toContain('name="return_to" value="'.e($this->kanbanUrl).'"');
});

// --- Unsafe values ------------------------------------------------------------

/** This app's own host, as the test requests use it (APP_URL differs between local and CI). */
function appHost(): string
{
    return parse_url(url('/'), PHP_URL_HOST);
}

/** The scheme the test requests don't use: http when they're https, and the other way round. */
function otherScheme(): string
{
    return parse_url(url('/'), PHP_URL_SCHEME) === 'https' ? 'http' : 'https';
}

dataset('unsafe return_to', [
    'another host' => ['https://evil.example/x'],
    'protocol-relative' => ['//evil.example'],
    'backslash' => ['/\\evil.example'],
    // This app's host, but a link that runs script (the review's finding:
    // the host check alone let it through).
    'javascript: with this host' => [fn () => 'javascript://'.appHost().'/%0Aalert(document.cookie)'],
    'javascript: as a path' => ['javascript:/tasks'],
    // This app's host on the other scheme: an https site never redirects
    // to http.
    'this host, other scheme' => [fn () => otherScheme().'://'.appHost().'/tasks'],
]);

test('an unsafe return_to is dropped on create, update and toggle, never followed', function (string $unsafe) {
    // The label goes with it: "← Kanban" never links to the Tasks list.
    $sent = ['return_to' => $unsafe, 'return_label' => 'Kanban'];

    $created = $this->post('/tasks', taskFields($this, $sent));
    $created->assertRedirect(route('tasks.edit', newestTask()));

    $updated = $this->put("/tasks/{$this->task->id}", taskFields($this, $sent));
    $updated->assertRedirect(route('tasks.edit', $this->task));

    $toggled = $this->patch("/tasks/{$this->task->id}/toggle-active", $sent);
    $toggled->assertRedirect(route('tasks.edit', $this->task));

    // From the list too: an unsafe return_to falls back to the edit page.
    $fromList = $this->patch("/tasks/{$this->task->id}/toggle-active", ['after' => 'list', 'return_to' => $unsafe]);
    $fromList->assertRedirect(route('tasks.edit', $this->task));

    foreach ([$created, $updated, $toggled, $fromList] as $response) {
        $location = $response->headers->get('Location');
        expect(parse_url($location, PHP_URL_HOST))->toBe(appHost())
            ->and(parse_url($location, PHP_URL_SCHEME))->toBe(parse_url(url('/'), PHP_URL_SCHEME));
    }
})->with('unsafe return_to');

test('an unsafe return_to in a link never reaches the page\'s back link or its forms', function (string $unsafe) {
    $page = $this->get(route('tasks.edit', ['task' => $this->task, 'return_to' => $unsafe, 'return_label' => 'Kanban']))
        ->assertOk()->getContent();

    assertBackLink($page, 'Tasks', route('tasks.index', $this->org->id));
    // Not the word "javascript:" itself: the unsaved-changes guard's
    // script mentions it.
    expect($page)->not->toContain('href="javascript:')
        ->not->toContain('alert(document.cookie)')
        ->not->toContain('← Kanban')
        ->not->toContain('name="return_to"');
})->with('unsafe return_to');

test('a label sent without return_to is dropped: no "← Kanban" linking to the Tasks list', function () {
    $page = $this->get(route('tasks.edit', ['task' => $this->task, 'return_label' => 'Kanban']))->getContent();
    assertBackLink($page, 'Tasks', route('tasks.index', $this->org->id));
    expect($page)->not->toContain('← Kanban')->not->toContain('name="return_label"');

    $this->put("/tasks/{$this->task->id}", taskFields($this, ['return_label' => 'Kanban']))
        ->assertRedirect(route('tasks.edit', $this->task));
    $this->post('/tasks', taskFields($this, ['return_label' => 'Kanban']))
        ->assertRedirect(route('tasks.edit', newestTask()));
});

test('saving a task into another company drops the back link: the old company\'s board no longer has it', function () {
    $other = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#000000']);
    $otherDept = Department::create(['organization_id' => $other->id, 'name' => 'Ops', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $other->id, 'name' => 'Project B', 'description' => 'd']);

    $response = $this->put("/tasks/{$this->task->id}", taskFields($this, [
        'project_id' => $otherProject->id, 'department_id' => $otherDept->id, ...$this->kanban,
    ]));

    $response->assertRedirect(route('tasks.edit', $this->task));
    assertBackLink($this->get($response->headers->get('Location'))->getContent(), 'Tasks', route('tasks.index', $other->id));

    // Within the same company the back link stays.
    $this->put("/tasks/{$this->task->id}", taskFields($this, [
        'project_id' => $otherProject->id, 'department_id' => $otherDept->id, 'title' => 'Again', ...$this->kanban,
    ]))->assertRedirect(route('tasks.edit', ['task' => $this->task, ...$this->kanban]));
});

test('an unknown return_label is dropped, keeping a safe return_to', function () {
    $sent = ['return_to' => $this->kanbanUrl, 'return_label' => 'Elsewhere'];

    $this->post('/tasks', taskFields($this, $sent))
        ->assertRedirect(route('tasks.edit', ['task' => newestTask(), 'return_to' => $this->kanbanUrl]));
    $this->put("/tasks/{$this->task->id}", taskFields($this, $sent))
        ->assertRedirect(route('tasks.edit', ['task' => $this->task, 'return_to' => $this->kanbanUrl]));
    $response = $this->patch("/tasks/{$this->task->id}/toggle-active", $sent)
        ->assertRedirect(route('tasks.edit', ['task' => $this->task, 'return_to' => $this->kanbanUrl]));

    // The label falls back to Tasks, as on the page today.
    expect($this->get($response->headers->get('Location'))->getContent())->toContain('← Tasks')->not->toContain('Elsewhere');
});

// --- Deactivate / Activate ---------------------------------------------------

test('Deactivate on the edit page opened from Kanban stays on the edit page with the Kanban back link', function () {
    $response = $this->patch("/tasks/{$this->task->id}/toggle-active", $this->kanban);

    $response->assertRedirect(route('tasks.edit', ['task' => $this->task, ...$this->kanban]))
        ->assertSessionHas('status', 'Task deactivated.');
    expect($this->task->fresh()->trashed())->toBeTrue();
    assertBackLink($this->get($response->headers->get('Location'))->getContent(), 'Kanban', $this->kanbanUrl);
});

test('the edit page\'s Deactivate form carries the back link', function () {
    $page = $this->get(route('tasks.edit', ['task' => $this->task, ...$this->kanban]))->getContent();

    // The Deactivate form and the edit form: two of each.
    expect(substr_count($page, 'name="return_to" value="'.e($this->kanbanUrl).'"'))->toBe(2)
        ->and(substr_count($page, 'name="return_label" value="Kanban"'))->toBe(2);
});

test('Deactivate on the Tasks list goes back to the exact list URL, search, filters, sort and page included', function () {
    $listUrl = route('tasks.index', $this->org->id).'?'.http_build_query([
        'q' => 'task', 'priority' => 'medium', 'status' => 'pending', 'sort' => 'title', 'direction' => 'desc', 'page' => 2,
    ]);

    $this->patch("/tasks/{$this->task->id}/toggle-active", ['after' => 'list', 'return_to' => $listUrl])
        ->assertRedirect($listUrl)
        ->assertSessionHas('status', 'Task deactivated.');
    expect($this->task->fresh()->trashed())->toBeTrue();
});

test('Activate on the Tasks list (show inactive on) goes back to the same list, where the row now offers Deactivate', function () {
    $this->task->delete();
    $listUrl = route('tasks.index', $this->org->id).'?'.http_build_query(['show_inactive' => 1, 'q' => 'Existing']);

    $response = $this->patch("/tasks/{$this->task->id}/toggle-active", ['after' => 'list', 'return_to' => $listUrl]);

    $response->assertRedirect($listUrl)->assertSessionHas('status', 'Task activated.');
    expect($this->task->fresh()->trashed())->toBeFalse();
    $page = $this->get($listUrl)->assertOk();
    $page->assertSee('Task activated.');
    expect($page->getContent())->toMatch('/<button type="submit" class="ml-3 text-gray-500 hover:underline">\s*Deactivate\s*<\/button>/');
});

test('the Tasks list\'s Deactivate form carries after=list and the list\'s own URL', function () {
    $listUrl = route('tasks.index', $this->org->id).'?q=Existing';

    $page = $this->get($listUrl)->assertOk()->getContent();

    expect($page)->toContain('<input type="hidden" name="after" value="list">')
        ->toContain('<input type="hidden" name="return_to" value="'.e($listUrl).'">');
});

test('deactivating the only row on the last page lands on the previous page, not an empty list', function () {
    // Two per page: Existing task + two more = page 2 holds one row.
    $this->owner->update(['tasks_per_page' => 2]);
    foreach (['Second task', 'Third task'] as $title) {
        Task::create([
            'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
            'title' => $title, 'priority' => 'medium', 'status' => 'pending',
        ]);
    }
    $listUrl = route('tasks.index', $this->org->id).'?'.http_build_query(['sort' => 'title', 'direction' => 'asc', 'page' => 2]);
    // Sorted by title, page 2 is "Third task" alone.
    $this->get($listUrl)->assertSee('Third task')->assertDontSee('Second task');

    $third = Task::where('title', 'Third task')->firstOrFail();
    $response = $this->patch("/tasks/{$third->id}/toggle-active", ['after' => 'list', 'return_to' => $listUrl]);
    $response->assertRedirect($listUrl);

    // index() clamps page 2 to the new last page, 1.
    $this->get($listUrl)->assertOk()
        ->assertSee('Task deactivated.')
        ->assertSee('Existing task')
        ->assertSee('Second task')
        ->assertDontSee('No tasks');
});

test('permissions are unchanged: a user who can\'t deactivate still can\'t, whatever return_to says', function () {
    $outsider = User::factory()->create();

    // Outside the task's company the organization scope hides it (404);
    // either way it's refused before any redirect.
    $status = $this->actingAs($outsider)
        ->patch("/tasks/{$this->task->id}/toggle-active", ['after' => 'list', 'return_to' => route('tasks.index')])
        ->getStatusCode();

    expect($status)->toBeIn([403, 404])
        ->and($this->task->fresh()->trashed())->toBeFalse();
});
