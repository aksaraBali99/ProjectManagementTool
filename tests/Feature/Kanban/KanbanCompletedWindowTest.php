<?php

use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Support\Carbon;

/**
 * task #72 phase 7: the Kanban Completed column shows the company's
 * window; "N older completed tasks · Show all" switches to ?completed=all
 * for this visit, and Load more follows the same mode.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);
});

function completedColumn(object $context, array $query = []): array
{
    $response = $context->actingAs($context->management)
        ->get(route('kanban', [$context->org] + $query))
        ->assertOk();

    return [$response->viewData('columns')->firstWhere('status', TaskStatus::Completed), $response->getContent()];
}

test('the Completed column hides tasks completed before the window, and its header counts the window', function () {
    $recent = makeWindowTask($this, 'completed', '2026-10-28 09:00:00');
    $undated = makeWindowTask($this, 'completed', 'none');
    $old = collect(range(1, 3))->map(fn () => makeWindowTask($this, 'completed', '2026-09-01 09:00:00'));

    [$column, $html] = completedColumn($this);

    expect($column['tasks']->pluck('task.id')->all())->toBe([$recent->id, $undated->id])
        ->and($column['total'])->toBe(2)
        ->and($column['older'])->toBe(3);

    expect($html)->toMatch('/'.preg_quote(TaskStatus::Completed->label(), '/').'<\/span>\s*<span[^>]*>2<\/span>/')
        ->toMatch('/3 older completed tasks ·\s*<a href="'.preg_quote(e(route('kanban', [$this->org, 'completed' => 'all'])), '/').'"[^>]*>Show all<\/a>/');

    foreach ($old as $task) {
        expect(kanbanCardIds($html))->not->toContain($task->id);
    }
});

test('the "older" line only shows when something is hidden, and says "task" for one', function () {
    makeWindowTask($this, 'completed', '2026-10-28 09:00:00');

    [, $html] = completedColumn($this);
    expect($html)->not->toContain('older completed')->not->toContain('kanban-completed-window');

    makeWindowTask($this, 'completed', '2026-09-01 09:00:00');

    [, $html] = completedColumn($this);
    expect($html)->toContain('1 older completed task ·');
});

test('?completed=all shows every completed task, newest first, and offers "Show recent only"', function () {
    $recent = makeWindowTask($this, 'completed', '2026-10-28 09:00:00');
    $older = makeWindowTask($this, 'completed', '2026-09-01 09:00:00');
    $oldest = makeWindowTask($this, 'completed', '2026-03-01 09:00:00');

    [$column, $html] = completedColumn($this, ['completed' => 'all']);

    expect($column['tasks']->pluck('task.id')->all())->toBe([$recent->id, $older->id, $oldest->id])
        ->and($column['total'])->toBe(3)
        ->and($html)->toContain('Show recent only')
        ->and($html)->not->toContain('older completed');
});

test('Load more follows the mode: window by default, everything with completed=all', function () {
    // 35 recent and 40 old: the window holds 35 (30 + 5 via Load more).
    foreach (range(1, 35) as $i) {
        makeWindowTask($this, 'completed', Carbon::parse('2026-10-29 00:00:00')->subMinutes($i)->toDateTimeString());
    }
    foreach (range(1, 40) as $i) {
        makeWindowTask($this, 'completed', Carbon::parse('2026-08-01 00:00:00')->subMinutes($i)->toDateTimeString());
    }

    [$column, $html] = completedColumn($this);
    expect($column['total'])->toBe(35)->and($column['remaining'])->toBe(5)->and($column['older'])->toBe(40);
    expect($html)->toContain('data-url="'.e(route('kanban.column', [$this->org, 'completed'])).'"');

    $windowIds = kanbanColumnIdsAcrossBatches($this->management, $this->org->id, TaskStatus::Completed);
    expect($windowIds)->toHaveCount(35);

    // With completed=all, the board's Load more URL carries it, and every
    // batch continues the full newest-first list.
    [$all, $allHtml] = completedColumn($this, ['completed' => 'all']);
    expect($all['total'])->toBe(75)->and($all['remaining'])->toBe(45);
    expect($allHtml)->toContain('data-url="'.e(route('kanban.column', [$this->org, 'completed', 'completed' => 'all'])).'"');

    $ids = $all['tasks']->pluck('task.id')->all();
    $offset = 30;
    while (true) {
        $json = $this->actingAs($this->management)
            ->getJson(route('kanban.column', [$this->org, 'completed', 'completed' => 'all', 'offset' => $offset]))
            ->assertOk()->json();
        $batch = kanbanCardIds($json['html']);
        array_push($ids, ...$batch);
        $offset += count($batch);
        if ($json['remaining'] === 0) {
            break;
        }
    }

    $expected = Task::where('status', 'completed')->orderByDesc('completed_at')->orderByDesc('id')->pluck('id')->all();
    expect($ids)->toBe($expected)
        ->and(array_slice($ids, 0, 35))->toBe($windowIds);

    // Without the parameter, Load more stays inside the window.
    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'completed', 'offset' => 30]))->assertOk()->json();
    expect(kanbanCardIds($json['html']))->toHaveCount(5)->and($json['remaining'])->toBe(0);
});

test('the other columns are unchanged by the window', function () {
    $oldPending = makeWindowTask($this, 'pending');
    $oldPending->forceFill(['created_at' => '2020-01-01', 'updated_at' => '2020-01-01'])->saveQuietly();
    makeWindowTask($this, 'completed', '2026-01-01 00:00:00');

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    $columns = $response->viewData('columns')->keyBy(fn ($column) => $column['status']->value);

    expect($columns['pending']['tasks']->pluck('task.id')->all())->toBe([$oldPending->id])
        ->and($columns['pending']['older'])->toBe(0)
        ->and(substr_count($response->getContent(), 'kanban-completed-window'))->toBe(1);
});

test('each company uses its own window', function () {
    $other = Organization::create(['name' => 'Thirty Co', 'slug' => 'thirty-co', 'accent_color' => '#000000', 'completed_window_days' => 30]);
    $otherDept = Department::create(['organization_id' => $other->id, 'name' => 'Ops', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $other->id, 'name' => 'Project B', 'description' => 'd']);
    OrgMember::create(['organization_id' => $other->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    // 20 days ago: outside 14, inside 30.
    $here = makeWindowTask($this, 'completed', '2026-10-10 12:00:00');
    $there = makeWindowTask($this, 'completed', '2026-10-10 12:00:00', [
        'organization_id' => $other->id, 'project_id' => $otherProject->id, 'department_id' => $otherDept->id,
    ]);

    [$column] = completedColumn($this);
    expect($column['tasks'])->toHaveCount(0)->and($column['older'])->toBe(1);

    $otherColumn = $this->actingAs($this->management)->get(route('kanban', $other))->assertOk()
        ->viewData('columns')->firstWhere('status', TaskStatus::Completed);
    expect($otherColumn['tasks']->pluck('task.id')->all())->toBe([$there->id])->and($otherColumn['older'])->toBe(0);

    expect($here->id)->not->toBe($there->id);
});

test('changing the company window applies at once', function () {
    makeWindowTask($this, 'completed', '2026-10-10 12:00:00');

    [$column] = completedColumn($this);
    expect($column['total'])->toBe(0);

    $this->actingAs($this->owner)->put(route('organizations.update', $this->org), [
        'name' => $this->org->name, 'accent_color' => $this->org->accent_color, 'completed_window_days' => 30,
    ])->assertRedirect();

    [$column] = completedColumn($this);
    expect($column['total'])->toBe(1);
});

test('the Completed column window keeps the query count flat as completed tasks grow', function () {
    $measure = function () {
        return countQueries(fn () => $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk());
    };

    foreach (range(1, 3) as $i) {
        makeWindowTask($this, 'completed', '2026-10-28 09:00:00');
        makeWindowTask($this, 'completed', '2026-09-01 09:00:00');
    }
    // The first render fills per-process caches (access profile, colours);
    // compare warm renders only.
    $measure();
    $small = $measure();

    foreach (range(1, 15) as $i) {
        makeWindowTask($this, 'completed', '2026-10-28 09:00:00');
        makeWindowTask($this, 'completed', '2026-09-01 09:00:00');
    }
    $large = $measure();

    expect($large)->toBe($small)->toBeLessThanOrEqual(30);
});
