<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;

/**
 * task #72 phase 7: the Dashboard's priority groups and My Task hide tasks
 * completed before the company's window, and link to the Tasks list with
 * the matching filter. Phase 8 dropped completed=all from those links (the
 * list's default Recent mode applies the same window) and added the count
 * where the filter matches the section exactly.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);

    $this->open = makeWindowTask($this, 'pending', null, ['priority' => 'high', 'assignee_id' => $this->staff->id]);
    $this->recent = makeWindowTask($this, 'completed', '2026-10-25 08:00:00', ['priority' => 'high', 'assignee_id' => $this->staff->id]);
    $this->old = makeWindowTask($this, 'completed', '2026-09-01 08:00:00', ['priority' => 'high', 'assignee_id' => $this->staff->id]);
});

function dashboardFor(object $context, User $user, array $query = []): TestResponse
{
    return $context->actingAs($user)->get(route('dashboard', [$context->org] + $query))->assertOk();
}

test('the priority groups hide old completed tasks', function () {
    $groups = dashboardFor($this, $this->management)->viewData('priorityGroups');

    expect($groups['high']->pluck('id')->sort()->values()->all())->toBe([$this->open->id, $this->recent->id]);
});

test('My Task hides old completed tasks, for management, staff and an owner', function (string $who) {
    $myTasks = dashboardFor($this, $this->{$who})->viewData('myTasks');

    expect($myTasks->pluck('id')->sort()->values()->all())->toBe([$this->open->id, $this->recent->id]);
})->with(['management', 'staff', 'owner']);

test('the window follows the company setting', function () {
    // 20 days ago: outside 7 and 14, inside 30. The 59-day-old one stays out.
    $twentyDays = makeWindowTask($this, 'completed', '2026-10-10 12:00:00', ['priority' => 'high']);
    $high = fn () => dashboardFor($this, $this->management)->viewData('priorityGroups')['high']->pluck('id')->sort()->values()->all();

    $this->org->update(['completed_window_days' => 7]);
    expect($high())->toBe([$this->open->id, $this->recent->id]);

    $this->org->update(['completed_window_days' => 30]);
    expect($high())->toBe([$this->open->id, $this->recent->id, $twentyDays->id]);
});

test('each priority group links to the Tasks list for that priority, in its default Recent mode', function () {
    // task #72 phase 8: completed=all dropped. The list's Recent mode
    // applies the same window, so the link opens the section's own count.
    $html = dashboardFor($this, $this->management)->getContent();

    foreach (['high', 'medium', 'low'] as $priority) {
        expect($html)->toContain('href="'.e(route('tasks.index', [$this->org, 'priority' => $priority])).'"');
    }
});

test('My Task links with the matching assignee filter where one exists', function () {
    // Staff: their own tasks, without a number (the list's assignee filter
    // doesn't match subtask-only tasks or the department rule).
    expect(dashboardFor($this, $this->staff)->viewData('myTasksLink'))->toBe([
        'url' => route('tasks.index', [$this->org, 'assignee_id' => $this->staff->id]),
        'label' => 'View all in Tasks list',
    ]);

    // Owner with exactly one person ticked: an exact filter, so the count.
    expect(dashboardFor($this, $this->owner, ['staff' => [$this->staff->id]])->viewData('myTasksLink'))->toBe([
        'url' => route('tasks.index', [$this->org, 'assignee_id' => $this->staff->id]),
        'label' => 'View all 2 in Tasks list',
    ]);
});

test('an owner with nobody ticked links to the whole Tasks list with the count', function () {
    expect(dashboardFor($this, $this->owner)->viewData('myTasksLink'))->toBe([
        'url' => route('tasks.index', $this->org),
        'label' => 'View all 2 in Tasks list',
    ]);
});

test('otherwise My Task links to the whole Tasks list as "Open in Tasks list"', function () {
    expect(dashboardFor($this, $this->management)->viewData('myTasksLink'))
        ->toBe(['url' => route('tasks.index', $this->org), 'label' => 'Open in Tasks list']);
});

test('an owner with several people ticked gets "Open in Tasks list"', function () {
    $other = User::factory()->create();

    $link = dashboardFor($this, $this->owner, ['staff' => [$this->staff->id, $other->id]])->viewData('myTasksLink');

    expect($link['label'])->toBe('Open in Tasks list')
        ->and($link['url'])->toBe(route('tasks.index', $this->org));
});

test('the header counts leave old completed tasks out too', function (string $who) {
    $counts = dashboardFor($this, $this->{$who})->viewData('sectionCounts');

    expect($counts['high'])->toBe(2);
    if ($who !== 'client') {
        expect($counts['my'])->toBe(2);
    }
})->with(['management', 'staff', 'owner', 'client']);

test('a client has no My Task section, so no link, and the window still applies', function () {
    $response = dashboardFor($this, $this->client);

    expect($response->viewData('myTasksLink'))->toBeNull()
        ->and($response->viewData('priorityGroups')['high']->pluck('id')->sort()->values()->all())->toBe([$this->open->id, $this->recent->id]);
});

test('a hand-edited ?staff[1]=… still works and links to that person', function () {
    // The request's own keys used to survive, so the single id sat at key 1
    // and reading the first id failed with a 500.
    $response = $this->actingAs($this->owner)->get(route('dashboard', $this->org).'?staff[1]='.$this->staff->id)->assertOk();

    expect($response->viewData('selectedStaffIds'))->toBe([$this->staff->id])
        ->and($response->viewData('myTasksLink')['url'])->toBe(route('tasks.index', [$this->org, 'assignee_id' => $this->staff->id]));
});
