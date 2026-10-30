<?php

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Carbon;

/**
 * task #72 phase 7: the Tasks list hides tasks completed before the
 * company's window by default. The "Completed" filter offers Recent
 * (default), All and Date range (completed_from / completed_to, completed
 * tasks only, inclusive); an explicit Status = Completed shows every
 * completed task. A sortable "Completed" column puts tasks with no date
 * last in both directions.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);

    $this->open = makeWindowTask($this, 'in_progress', null, ['title' => 'Open task']);
    $this->recent = makeWindowTask($this, 'completed', '2026-10-25 08:00:00', ['title' => 'Recent completed']);
    $this->undated = makeWindowTask($this, 'completed', 'none', ['title' => 'Undated completed']);
    $this->oct5 = makeWindowTask($this, 'completed', '2026-10-05 23:59:00', ['title' => 'Old Oct 5']);
    $this->oct1 = makeWindowTask($this, 'completed', '2026-10-01 00:00:00', ['title' => 'Old Oct 1']);
    $this->sep30 = makeWindowTask($this, 'completed', '2026-09-30 23:59:59', ['title' => 'Old Sep 30']);
});

function listIds(object $context, array $query = [], ?object $user = null): array
{
    $ids = taskListIdsAcrossPages($user ?? $context->management, $context->org->id, $query);
    sort($ids);

    return $ids;
}

function idsOf(Task ...$tasks): array
{
    $ids = array_map(fn (Task $task) => $task->id, $tasks);
    sort($ids);

    return $ids;
}

test('Recent (the default) hides completed tasks older than the window', function () {
    expect(listIds($this))->toBe(idsOf($this->open, $this->recent, $this->undated));

    $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk()->getContent();
    expect($html)->toMatch('/<option value=""\s+selected>Recent \(14 days\)<\/option>/');
});

test('All shows every task', function () {
    expect(listIds($this, ['completed' => 'all']))->toBe(idsOf($this->open, $this->recent, $this->undated, $this->oct5, $this->oct1, $this->sep30));
});

test('choosing Status = Completed shows every completed task', function () {
    expect(listIds($this, ['status' => 'completed']))->toBe(idsOf($this->recent, $this->undated, $this->oct5, $this->oct1, $this->sep30));
});

test('Date range: only tasks completed on those dates, inclusive', function () {
    expect(listIds($this, ['completed' => 'range', 'completed_from' => '2026-10-01', 'completed_to' => '2026-10-05']))
        ->toBe(idsOf($this->oct5, $this->oct1));

    // One end only.
    expect(listIds($this, ['completed' => 'range', 'completed_from' => '2026-10-05']))->toBe(idsOf($this->recent, $this->oct5))
        ->and(listIds($this, ['completed' => 'range', 'completed_to' => '2026-09-30']))->toBe(idsOf($this->sep30));

    // The dates alone switch to the range, whatever the select says.
    expect(listIds($this, ['completed_from' => '2026-10-01', 'completed_to' => '2026-10-01']))->toBe(idsOf($this->oct1));

    // A range with Status = Completed still applies the range.
    expect(listIds($this, ['status' => 'completed', 'completed_from' => '2026-10-01', 'completed_to' => '2026-10-05']))->toBe(idsOf($this->oct5, $this->oct1));
});

test('Date range with no dates shows every completed task; invalid dates are ignored', function () {
    expect(listIds($this, ['completed' => 'range']))->toBe(idsOf($this->recent, $this->undated, $this->oct5, $this->oct1, $this->sep30))
        ->and(listIds($this, ['completed_from' => 'not-a-date']))->toBe(idsOf($this->open, $this->recent, $this->undated));
});

test('"Show inactive" applies first, then the window', function () {
    $inactiveRecent = makeWindowTask($this, 'completed', '2026-10-28 08:00:00');
    $inactiveRecent->delete();
    $inactiveOld = makeWindowTask($this, 'completed', '2026-09-01 08:00:00');
    $inactiveOld->delete();

    $withInactive = listIds($this, ['show_inactive' => 1]);
    expect($withInactive)->toContain($inactiveRecent->id)->not->toContain($inactiveOld->id);

    expect(listIds($this, ['show_inactive' => 1, 'completed' => 'all']))->toContain($inactiveRecent->id, $inactiveOld->id)
        ->and(listIds($this))->not->toContain($inactiveRecent->id);
});

test('the Completed sort walks every page in order, tasks with no date last, both directions', function (string $direction) {
    // 30 more completed tasks with repeated dates and titles, so the
    // title/id tie-breaks matter across a page boundary (25 per page).
    foreach (range(1, 30) as $i) {
        makeWindowTask($this, 'completed', '2026-0'.(($i % 3) + 7).'-1'.($i % 4).' 10:00:00', ['title' => 'Batch '.($i % 5)]);
    }
    foreach (range(1, 3) as $i) {
        makeWindowTask($this, 'pending', null, ['title' => 'Open '.$i]);
    }

    $query = ['completed' => 'all', 'sort' => 'completed', 'direction' => $direction];
    $walked = taskListIdsAcrossPages($this->management, $this->org->id, $query);

    $plain = Task::where('organization_id', $this->org->id)->get()->all();
    usort($plain, function (Task $a, Task $b) use ($direction) {
        if (($a->completed_at === null) !== ($b->completed_at === null)) {
            return $a->completed_at === null ? 1 : -1;
        }
        $byDate = ($a->completed_at?->timestamp ?? 0) <=> ($b->completed_at?->timestamp ?? 0);
        if ($byDate !== 0) {
            return $direction === 'asc' ? $byDate : -$byDate;
        }

        return [strtolower($a->title), $a->id] <=> [strtolower($b->title), $b->id];
    });

    expect($walked)->toBe(array_map(fn (Task $task) => $task->id, $plain))
        ->and($walked)->toHaveCount(39);

    // No date last, in both directions.
    expect(array_slice($walked, -5))->each->toBeIn(Task::whereNull('completed_at')->pluck('id')->all())
        ->and(Task::whereNull('completed_at')->count())->toBe(5);
})->with(['asc', 'desc']);

test('the Completed column shows the date, and its header sorts', function () {
    $html = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk()->getContent();

    expect($html)->toContain('Oct 25')
        ->toContain(e(route('tasks.index', $this->org).'?'.http_build_query(['sort' => 'completed', 'direction' => 'asc'])));
});

test('the Completed filter survives paging and sorting, and the company tabs', function () {
    foreach (range(1, 30) as $i) {
        makeWindowTask($this, 'completed', '2026-10-02 10:00:00');
    }

    $response = $this->actingAs($this->management)
        ->get(route('tasks.index', [$this->org, 'completed' => 'range', 'completed_from' => '2026-10-01', 'completed_to' => '2026-10-05']))
        ->assertOk();

    expect($response->viewData('tasks')->total())->toBe(32);
    $html = $response->getContent();

    // The page 2 link and a sort link both keep the range.
    expect($html)->toMatch('/href="[^"]*completed_from=2026-10-01[^"]*page=2"/')
        ->toMatch('/href="[^"]*completed_from=2026-10-01[^"]*sort=title[^"]*"/');

    // Page 2 is still the range.
    $page2 = $this->actingAs($this->management)
        ->get(route('tasks.index', [$this->org, 'completed' => 'range', 'completed_from' => '2026-10-01', 'completed_to' => '2026-10-05', 'page' => 2]))
        ->assertOk();
    expect($page2->viewData('tasks')->count())->toBe(7)->and($page2->viewData('tasks')->total())->toBe(32);

    // The range inputs show for a range, and stay hidden otherwise.
    expect($html)->toMatch('/<div id="task-filter-completed-range" class="flex items-end gap-3" >/');
    $default = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->getContent();
    expect($default)->toMatch('/<div id="task-filter-completed-range" class="flex items-end gap-3" hidden>/');
});

test('the filter dropdowns still list options that only old completed tasks use', function () {
    $oldOnly = Project::create(['organization_id' => $this->org->id, 'name' => 'Finished project', 'description' => 'd']);
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00', ['project_id' => $oldOnly->id]);

    $response = $this->actingAs($this->management)->get(route('tasks.index', $this->org))->assertOk();

    expect($response->viewData('filterProjects')->pluck('name'))->toContain('Finished project');
});

test('impossible dates are ignored, not rolled over to a real day', function (string $date) {
    expect(listIds($this, ['completed_to' => $date]))->toBe(idsOf($this->open, $this->recent, $this->undated))
        ->and(listIds($this, ['completed_from' => $date]))->toBe(idsOf($this->open, $this->recent, $this->undated));
})->with(['2026-02-31', '2026-13-01', '2026-00-10', '2026-10-32']);

test('a real date at a month edge still works', function () {
    expect(listIds($this, ['completed_from' => '2026-09-30', 'completed_to' => '2026-09-30']))->toBe(idsOf($this->sep30));
});

test('a date range with another Status explains why nothing matches', function () {
    $html = $this->actingAs($this->management)
        ->get(route('tasks.index', [$this->org, 'status' => 'pending', 'completed' => 'range', 'completed_from' => '2026-10-01']))
        ->assertOk()->getContent();

    expect($html)->toContain('task-filter-range-hint')
        ->toContain("so it can't match Status = Pending");

    // Not for Status = Completed, no status, or no range.
    foreach ([
        ['status' => 'completed', 'completed' => 'range', 'completed_from' => '2026-10-01'],
        ['completed' => 'range', 'completed_from' => '2026-10-01'],
        ['status' => 'pending'],
    ] as $query) {
        $html = $this->actingAs($this->management)->get(route('tasks.index', [$this->org] + $query))->assertOk()->getContent();
        expect($html)->not->toContain('task-filter-range-hint');
    }
});
