<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * task #72 phase 4: the Tasks list shows one page at a time (25 by default,
 * or the user's saved page size), with a "Showing x–y of z tasks" line, and
 * keeps filters and sort across page links and trips to a task and back.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Paging Org', 'slug' => 'paging-org', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function makeTaskListPagingTasks(object $context, int $count, string $titlePrefix = 'Paged task'): void
{
    for ($i = 1; $i <= $count; $i++) {
        Task::create([
            'organization_id' => $context->org->id,
            'project_id' => $context->project->id,
            'department_id' => $context->dept->id,
            // Zero-padded so title order is also creation order.
            'title' => $titlePrefix.' '.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'priority' => 'medium',
            'status' => 'pending',
        ]);
    }
}

function taskListRowCount(string $html): int
{
    return substr_count($html, 'data-drilldown-toggle=');
}

/** Every href in $html that points at the given page of the list, decoded. */
function taskListPageLinks(string $html, int $page): array
{
    preg_match_all('/href="([^"]*)"/', $html, $matches);

    return array_values(array_filter(
        array_map(fn ($href) => html_entity_decode($href), $matches[1]),
        function ($href) use ($page) {
            parse_str((string) parse_url($href, PHP_URL_QUERY), $params);

            return str_contains($href, '/tasks/') && ($params['page'] ?? null) === (string) $page;
        },
    ));
}

test('the list shows 25 tasks by default, with a Showing line', function () {
    makeTaskListPagingTasks($this, 30);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk();

    expect(taskListRowCount($response->getContent()))->toBe(25);
    $response->assertSee('Showing 1–25 of 30 tasks');
    // Said once: Laravel's own "Showing 1 to 25 of 30 results" is not here too.
    expect(substr_count($response->getContent(), 'Showing'))->toBe(1);
});

test('the second page shows the rest, and says so', function () {
    makeTaskListPagingTasks($this, 30);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org).'?page=2')->assertOk();

    expect(taskListRowCount($response->getContent()))->toBe(5);
    $response->assertSee('Showing 26–30 of 30 tasks');
    $response->assertSee('Paged task 026');
    $response->assertDontSee('Paged task 025');
});

test('the Showing line uses thousands separators and the singular for one task', function () {
    makeTaskListPagingTasks($this, 1);

    $this->actingAs($this->management)->get(route('tasks.index', $this->org))
        ->assertSee('Showing 1–1 of 1 task')
        ->assertDontSee('of 1 tasks');
});

test('a page past the last one shows the last page instead of an empty list', function () {
    makeTaskListPagingTasks($this, 30);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org).'?page=99')->assertOk();

    expect(taskListRowCount($response->getContent()))->toBe(5);
    $response->assertSee('Showing 26–30 of 30 tasks');
    expect($response->viewData('tasks')->currentPage())->toBe(2);
});

test('an invalid page is page 1', function (string $page) {
    makeTaskListPagingTasks($this, 30);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org).'?page='.urlencode($page))->assertOk();

    expect($response->viewData('tasks')->currentPage())->toBe(1);
    $response->assertSee('Showing 1–25 of 30 tasks');
})->with(['0', '-3', 'abc', '2.5', '']);

test('a single page shows no page links, and an empty list shows no Showing line', function () {
    makeTaskListPagingTasks($this, 3);

    $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk()->getContent();
    expect($html)->not->toContain('aria-label="Pagination Navigation"');

    Task::query()->forceDelete();

    $this->actingAs($this->management)->get(route('tasks.index', $this->org))
        ->assertOk()
        ->assertSee('No tasks yet.')
        ->assertDontSee('Showing ');
});

test('filters, sort and Show inactive survive the page links', function () {
    makeTaskListPagingTasks($this, 30, 'Match');
    makeTaskListPagingTasks($this, 5, 'Other');

    $query = ['q' => 'Match', 'priority' => 'medium', 'sort' => 'title', 'direction' => 'desc', 'show_inactive' => 1];
    $html = $this->actingAs($this->management)
        ->get(route('tasks.index', $this->org).'?'.http_build_query($query))
        ->assertOk()
        ->assertSee('Showing 1–25 of 30 tasks')
        ->getContent();

    $links = taskListPageLinks($html, 2);
    expect($links)->not->toBeEmpty();

    foreach ($links as $link) {
        parse_str((string) parse_url($link, PHP_URL_QUERY), $params);
        expect($params)->toMatchArray(array_map('strval', $query) + ['page' => '2']);
    }

    // And following one lands on the rest of the same filtered, sorted list.
    $this->actingAs($this->management)->get($links[0])
        ->assertOk()
        ->assertSee('Showing 26–30 of 30 tasks')
        ->assertSee('Match 005')
        ->assertDontSee('Other 001');
});

test('the page size box shows the user\'s saved value, or 25', function () {
    makeTaskListPagingTasks($this, 3);

    $box = '/name="per_page"[^>]*value="(\d+)" data-current="(\d+)"/';

    $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->getContent();
    expect(preg_match($box, $html, $match))->toBe(1)
        ->and([$match[1], $match[2]])->toBe(['25', '25']);

    $this->management->update(['tasks_per_page' => 40]);

    $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->getContent();
    expect(preg_match($box, $html, $match))->toBe(1)
        ->and([$match[1], $match[2]])->toBe(['40', '40']);
});

test('the user\'s saved page size decides how many rows a page has', function () {
    makeTaskListPagingTasks($this, 30);
    $this->management->update(['tasks_per_page' => 7]);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org).'?page=5')->assertOk();

    expect(taskListRowCount($response->getContent()))->toBe(2);
    $response->assertSee('Showing 29–30 of 30 tasks');
});

test('opening a task, or its preview, from page 2 comes back to page 2 with the same filters and sort', function () {
    makeTaskListPagingTasks($this, 30);

    // Keys in alphabetical order: that is how url()->full() writes them.
    $listUrl = route('tasks.index', $this->org).'?'.http_build_query(['direction' => 'desc', 'page' => 2, 'q' => 'Paged', 'sort' => 'title']);
    $html = $this->actingAs($this->management)->get($listUrl)->assertOk()->getContent();

    // The last task on the descending page 2 is "Paged task 001".
    $task = Task::where('title', 'Paged task 001')->firstOrFail();

    $editHref = route('tasks.edit', ['task' => $task, 'return_to' => $listUrl, 'return_label' => 'Tasks']);
    $previewHref = route('tasks.preview', ['task' => $task, 'return_to' => $listUrl, 'return_label' => 'Tasks']);
    expect($html)->toContain(e($editHref))->toContain(e($previewHref));

    // The task page's "← Back" goes to that same list URL, and the
    // preview's "Open task" opens the task with it as return_to.
    $this->actingAs($this->management)->get($editHref)->assertOk()->assertSee('href="'.e($listUrl).'"', false);
    $this->actingAs($this->management)->get($previewHref)->assertOk()->assertSee('href="'.e($editHref).'"', false);
});

test('switching company tabs and the Show inactive box both go back to page 1', function () {
    makeTaskListPagingTasks($this, 30);
    $other = Organization::create(['name' => 'Zeta Org', 'slug' => 'zeta-org', 'accent_color' => '#534AB7']);
    OrgMember::create([
        'organization_id' => $other->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $html = $this->actingAs($this->management)
        ->get(route('tasks.index', $this->org).'?sort=title&page=2')
        ->assertOk()
        ->getContent();

    // The tab keeps the sort but not the page.
    expect($html)->toContain('href="'.route('tasks.index', $other).'?sort=title"');
    // The checkbox navigates in JS; it drops page along with show_inactive.
    expect($html)->toContain("params.delete('show_inactive'); params.delete('page');");
});
