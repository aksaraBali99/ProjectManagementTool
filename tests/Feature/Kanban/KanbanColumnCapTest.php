<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * task #72 phase 5: each Kanban column shows its first 30 cards, the
 * header shows the column's TOTAL, and Load more brings the rest 30 at a
 * time. Order within a column: priority (High, Medium, Low), then due date
 * soonest first with no date last, then task id — so batches never repeat
 * or skip a card.
 *
 * The expected order is built in PHP, independently of the SQL, so the
 * same expectation holds for SQLite here and MySQL in production.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Board Org', 'slug' => 'board-org', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function makeKanbanCapTask(object $context, string $status, string $priority, ?string $dueDate, string $title = 'Card'): Task
{
    return Task::create([
        'organization_id' => $context->org->id,
        'project_id' => $context->project->id,
        'department_id' => $context->dept->id,
        'title' => $title,
        'priority' => $priority,
        'status' => $status,
        'due_date' => $dueDate,
    ]);
}

/** The agreed column order, in PHP. */
function expectedKanbanColumnOrder(int $organizationId, TaskStatus $status): array
{
    $priorityRank = array_flip(array_map(fn (Priority $case) => $case->value, Priority::cases()));

    return Task::where('organization_id', $organizationId)
        ->where('status', $status->value)
        ->get()
        ->sort(fn (Task $a, Task $b) => [$priorityRank[$a->priority->value], $a->due_date === null, $a->due_date?->timestamp ?? 0, $a->id]
            <=> [$priorityRank[$b->priority->value], $b->due_date === null, $b->due_date?->timestamp ?? 0, $b->id])
        ->pluck('id')
        ->all();
}

test('a column of 70 shows the first 30 in order, and Load more returns the rest in order with no repeats or gaps', function () {
    // 70 cards mixing every priority, a few due dates (repeated, so the id
    // tie-break matters) and no due date — created in an order unrelated
    // to the expected one.
    $dueDates = ['2026-11-20', null, '2026-11-05', '2026-11-20', null, '2026-10-30', '2026-11-05'];
    $priorities = ['low', 'high', 'medium', 'high', 'low', 'medium', 'medium', 'high'];
    for ($i = 0; $i < 70; $i++) {
        makeKanbanCapTask($this, 'pending', $priorities[$i % count($priorities)], $dueDates[$i % count($dueDates)], 'Card '.$i);
    }
    // Other columns must not leak into this one.
    makeKanbanCapTask($this, 'in_progress', 'high', '2026-10-01');

    $expected = expectedKanbanColumnOrder($this->org->id, TaskStatus::Pending);
    expect($expected)->toHaveCount(70);

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    $pending = $response->viewData('columns')->firstWhere('status', TaskStatus::Pending);

    expect($pending['tasks']->pluck('task.id')->all())->toBe(array_slice($expected, 0, 30))
        ->and($pending['total'])->toBe(70)
        ->and($pending['remaining'])->toBe(40);

    // The rendered board matches the view data, card for card.
    expect(array_slice(kanbanCardIds($response->getContent()), 0, 30))->toBe(array_slice($expected, 0, 30));

    $response->assertSee('40 more');

    expect(kanbanColumnIdsAcrossBatches($this->management, $this->org->id, TaskStatus::Pending))->toBe($expected);
});

test('Load more batches are exactly 30, and the last one says nothing remains', function () {
    for ($i = 0; $i < 65; $i++) {
        makeKanbanCapTask($this, 'pending', 'medium', null);
    }
    $url = route('kanban.column', [$this->org, 'pending']);

    $second = $this->actingAs($this->management)->getJson($url.'?offset=30')->assertOk()->json();
    expect(kanbanCardIds($second['html']))->toHaveCount(30)->and($second['remaining'])->toBe(5);

    $third = $this->actingAs($this->management)->getJson($url.'?offset=60')->assertOk()->json();
    expect(kanbanCardIds($third['html']))->toHaveCount(5)->and($third['remaining'])->toBe(0);

    $past = $this->actingAs($this->management)->getJson($url.'?offset=500')->assertOk()->json();
    expect($past['html'])->toBe('')->and($past['remaining'])->toBe(0);
});

test('an invalid offset is treated as 0', function (string $offset) {
    for ($i = 0; $i < 35; $i++) {
        makeKanbanCapTask($this, 'pending', 'medium', null);
    }
    $expectedFirst = array_slice(expectedKanbanColumnOrder($this->org->id, TaskStatus::Pending), 0, 30);

    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending']).'?offset='.urlencode($offset))
        ->assertOk()
        ->json();

    expect(kanbanCardIds($json['html']))->toBe($expectedFirst)->and($json['remaining'])->toBe(5);
})->with(['abc', '-5', '2.5', '']);

test('each column header shows its total, whether over 30, under 30 or empty', function () {
    for ($i = 0; $i < 45; $i++) {
        makeKanbanCapTask($this, 'pending', 'medium', null);
    }
    for ($i = 0; $i < 7; $i++) {
        makeKanbanCapTask($this, 'in_review', 'high', null);
    }
    // in_progress and completed stay empty.

    $response = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk();
    $columns = $response->viewData('columns')->keyBy(fn ($column) => $column['status']->value);

    expect($columns['pending']['total'])->toBe(45)->and($columns['pending']['tasks'])->toHaveCount(30)
        ->and($columns['in_review']['total'])->toBe(7)->and($columns['in_review']['tasks'])->toHaveCount(7)
        ->and($columns['in_progress']['total'])->toBe(0)->and($columns['completed']['total'])->toBe(0);

    // The header renders the total, not the 30 shown.
    $html = $response->getContent();
    expect($html)->toMatch('/'.preg_quote(TaskStatus::Pending->label(), '/').'<\/span>\s*<span[^>]*>45<\/span>/')
        ->and($html)->toMatch('/'.preg_quote(TaskStatus::InReview->label(), '/').'<\/span>\s*<span[^>]*>7<\/span>/')
        ->and($html)->toMatch('/'.preg_quote(TaskStatus::Completed->label(), '/').'<\/span>\s*<span[^>]*>0<\/span>/');

    // Load more only where there is more: one button, for Pending.
    expect(substr_count($html, 'class="kanban-load-more '))->toBe(1);
    $response->assertSee('15 more');
    $response->assertSee(route('kanban.column', [$this->org, 'pending']), false);

    // Empty columns look as they always did.
    expect(substr_count($html, 'No tasks</p>'))->toBe(2);
});

test('a column that fits entirely shows no "more" line and no button', function () {
    for ($i = 0; $i < 30; $i++) {
        makeKanbanCapTask($this, 'pending', 'medium', null);
    }

    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();

    expect(kanbanCardIds($html))->toHaveCount(30)
        ->and($html)->not->toContain('class="kanban-load-more ')
        ->and($html)->not->toMatch('/>\s*Load more\s*<\/button>/')
        ->and($html)->not->toContain(' more</p>');
});

test('cards from Load more link back to the board, not to the Load more endpoint', function () {
    for ($i = 0; $i < 31; $i++) {
        makeKanbanCapTask($this, 'pending', 'medium', null);
    }
    $last = Task::orderByDesc('id')->firstOrFail();

    $json = $this->actingAs($this->management)
        ->getJson(route('kanban.column', [$this->org, 'pending']).'?offset=30')
        ->assertOk()
        ->json();

    $editHref = route('tasks.edit', ['task' => $last, 'return_to' => route('kanban', $this->org), 'return_label' => 'Kanban']);
    expect($json['html'])->toContain(e($editHref))
        ->and($json['html'])->not->toContain('columns%2Fpending');

    // And the board's own cards use the same return URL, even when the
    // board was opened without a company in the URL.
    $this->actingAs($this->management)->get('/kanban')->assertOk()
        ->assertSee(e(route('tasks.edit', ['task' => Task::orderBy('id')->firstOrFail(), 'return_to' => route('kanban', $this->org), 'return_label' => 'Kanban'])), false);
});

test('the board script delegates its listeners, so appended cards work without re-binding', function () {
    makeKanbanCapTask($this, 'pending', 'medium', null);

    $html = $this->actingAs($this->management)->get(route('kanban', $this->org))->assertOk()->getContent();

    foreach (['dragstart', 'dragover', 'drop', 'change', 'click'] as $event) {
        expect($html)->toContain("board.addEventListener('{$event}'");
    }
    expect($html)->not->toContain("querySelectorAll('.kanban-card[draggable=\"true\"]')");
});
