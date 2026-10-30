<?php

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 7: the Projects page's task drilldown hides tasks
 * completed before the company's window, the Tasks column reads
 * "shown / total" (sorting stays on the total), and "Show all" switches to
 * ?completed=all for the visit.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);

    $this->open = makeWindowTask($this, 'pending');
    $this->recent = makeWindowTask($this, 'completed', '2026-10-25 08:00:00');
    $this->old = makeWindowTask($this, 'completed', '2026-09-01 08:00:00');
    $this->older = makeWindowTask($this, 'completed', '2026-08-01 08:00:00');
});

function projectRow(object $context, array $query = [], ?object $user = null): Project
{
    return $context->actingAs($user ?? $context->management)
        ->get(route('projects.index', [$context->org] + $query))
        ->assertOk()
        ->viewData('projects')->firstWhere('id', $context->project->id);
}

test('the drilldown hides old completed tasks and the count reads shown / total', function () {
    $project = projectRow($this);

    expect($project->tasks->pluck('id')->sort()->values()->all())->toBe([$this->open->id, $this->recent->id])
        ->and($project->shown_tasks_count)->toBe(2)
        ->and($project->tasks_count)->toBe(4);

    $html = $this->actingAs($this->management)->get(route('projects.index', $this->org))->getContent();
    expect($html)->toMatch('/>2 \/ 4<\/span>/')
        ->toContain('Tasks completed more than 14 days ago are hidden.')
        ->toContain('href="'.e(route('projects.index', $this->org).'?completed=all').'"');
});

test('"Show all" lists every task, the count reads total / total, and offers "Show recent only"', function () {
    $project = projectRow($this, ['completed' => 'all']);

    expect($project->tasks)->toHaveCount(4)
        ->and($project->shown_tasks_count)->toBe(4)
        ->and($project->tasks_count)->toBe(4);

    $html = $this->actingAs($this->management)->get(route('projects.index', [$this->org, 'completed' => 'all']))->getContent();
    expect($html)->toMatch('/>4 \/ 4<\/span>/')
        ->toContain('Show recent only')
        // The filter form and the sort links keep the mode for this visit.
        ->toContain('<input type="hidden" name="completed" value="all">')
        ->toMatch('/href="[^"]*sort=tasks[^"]*completed=all[^"]*"/');
});

test('sorting by Tasks uses the total, not the shown count', function () {
    // Busy: 1 shown of 4. Light: 2 shown of 2. By total, Light comes first ascending.
    $light = Project::create(['organization_id' => $this->org->id, 'name' => 'Light', 'description' => 'd']);
    makeWindowTask($this, 'pending', null, ['project_id' => $light->id]);
    makeWindowTask($this, 'pending', null, ['project_id' => $light->id]);
    $this->open->delete();

    $names = $this->actingAs($this->management)
        ->get(route('projects.index', [$this->org, 'sort' => 'tasks', 'direction' => 'asc']))
        ->viewData('projects')->pluck('name')->all();

    expect($names)->toBe(['Light', 'Project A']);
});

test('a project whose tasks are all old completed says so instead of "No tasks"', function () {
    $this->open->delete();
    $this->recent->update(['status' => 'completed']);
    DB::table('tasks')->where('id', $this->recent->id)->update(['completed_at' => '2026-01-01 00:00:00']);

    $html = $this->actingAs($this->management)->get(route('projects.index', $this->org))->getContent();

    expect($html)->toContain('No recent tasks')->not->toContain('No tasks on this project yet.');
});

test('staff and clients get the same window, on top of their own visibility', function (string $who) {
    $project = projectRow($this, [], $this->{$who});

    expect($project->tasks->pluck('id')->sort()->values()->all())->toBe([$this->open->id, $this->recent->id])
        ->and($project->tasks_count)->toBe(4);
})->with(['staff', 'client']);
