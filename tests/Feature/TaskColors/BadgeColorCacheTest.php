<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusColor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 8: badge colours are read from the cache store at most
 * once per enum per request (Cache::memo()), not once per badge.
 *
 * On the database cache store, as on dev and production — phpunit.xml's
 * array store makes a cache read free, which is how a read per badge went
 * unseen. app()->forgetScopedInstances() starts each request as a new one
 * would: one application serves every request in a test.
 */
beforeEach(function () {
    config(['cache.default' => 'database']);

    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Colour Org', 'slug' => 'colour-org', 'accent_color' => '#1D9E75']);
    $department = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    foreach (range(1, 24) as $i) {
        Task::create([
            'organization_id' => $this->org->id,
            'project_id' => $project->id,
            'department_id' => $department->id,
            'title' => 'Badge task '.$i,
            'priority' => Priority::cases()[$i % 3]->value,
            'status' => TaskStatus::cases()[$i % 4]->value,
        ]);
    }
});

/**
 * Runs one request as a new request and returns how many reads it sent to
 * the cache table (selects only, not a cold store's write) and to the two
 * colour tables.
 *
 * @return array{cache: int, colors: int}
 */
function colourReadsDuring(callable $request): array
{
    app()->forgetScopedInstances();

    $counts = ['cache' => 0, 'colors' => 0];
    $counting = true;

    DB::listen(function ($query) use (&$counts, &$counting) {
        if (! $counting) {
            return;
        }
        if (preg_match('/^select .* from ["`]?cache["`]?\s/is', $query->sql)) {
            $counts['cache']++;
        }
        if (preg_match('/from ["`]?task_(status|priority)_colors["`]?/i', $query->sql)) {
            $counts['colors']++;
        }
    });

    try {
        $request();
    } finally {
        $counting = false;
    }

    return $counts;
}

test('a page full of badges reads the colours from the store at most once per enum', function (string $routeName) {
    $url = route($routeName, $this->org);

    // Cold store (after a deploy or a colour save): each enum misses once
    // and loads its colour table once. Writing it back clears that key's
    // memo, so the next badge reads the store once more: 2 per enum.
    $cold = colourReadsDuring(fn () => $this->actingAs($this->owner)->get($url)->assertOk());
    expect($cold['cache'])->toBeLessThanOrEqual(4)
        ->and($cold['colors'])->toBeLessThanOrEqual(2);

    // Warm store: one read per enum, and the colour tables aren't touched.
    $warm = colourReadsDuring(fn () => $this->actingAs($this->owner)->get($url)->assertOk());
    expect($warm['cache'])->toBe(2)
        ->and($warm['colors'])->toBe(0);
})->with(['dashboard', 'kanban', 'tasks.index', 'projects.index']);

test('the next request after saving on the Status & Priority Colors page shows the new colour', function () {
    $this->actingAs($this->owner)->get(route('kanban', $this->org))->assertOk()->assertDontSee('#123456', false);

    $colors = TaskStatusColor::all()->mapWithKeys(fn (TaskStatusColor $color) => [
        $color->status => ['background_color' => $color->background_color, 'text_color' => $color->text_color],
    ])->all();
    $colors['pending'] = ['background_color' => '#123456', 'text_color' => '#ABCDEF'];

    app()->forgetScopedInstances();
    $this->actingAs($this->owner)->put('/task-colors/status', ['colors' => $colors])
        ->assertRedirect(route('task-colors.edit'));

    app()->forgetScopedInstances();
    $this->actingAs($this->owner)->get(route('kanban', $this->org))->assertOk()->assertSee('#123456', false);
    $this->actingAs($this->owner)->get(route('dashboard', $this->org))->assertOk()->assertSee('#ABCDEF', false);
});

test('forgetColorCache() clears the memo and the store in the same request', function () {
    expect(TaskStatus::Pending->badgeBackground())->not->toBe('#654321');

    TaskStatusColor::where('status', 'pending')->update(['background_color' => '#654321']);
    TaskStatus::forgetColorCache();

    expect(TaskStatus::Pending->badgeBackground())->toBe('#654321')
        ->and(Cache::store('database')->get('task_status_colors')['pending']['background_color'])->toBe('#654321');
});

test('the memo does not carry over to the next request: it reads the store again', function () {
    $this->actingAs($this->owner)->get(route('kanban', $this->org))->assertOk();

    // Change the stored copy behind the memo's back. A request that kept
    // the previous request's memo would still render the old colour.
    $stored = Cache::store('database')->get('task_status_colors');
    $stored['pending']['background_color'] = '#0A0B0C';
    Cache::store('database')->forever('task_status_colors', $stored);

    $reads = colourReadsDuring(function () use (&$html) {
        $html = $this->actingAs($this->owner)->get(route('kanban', $this->org))->assertOk()->getContent();
    });

    expect($reads['cache'])->toBe(2)
        ->and($html)->toContain('#0A0B0C');
});
