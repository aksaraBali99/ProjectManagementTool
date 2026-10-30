<?php

use App\Models\Task;
use Illuminate\Support\Carbon;

/**
 * task #72 phase 7: Task::scopeOpenOrCompletedWithin() and friends — the
 * one rule every working page uses. "Recent" = not Completed, or
 * Completed at or after the window start; no completed_at counts as
 * recent.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);
});

function windowIds(object $context, ?int $days = null): array
{
    if ($days !== null) {
        $context->org->update(['completed_window_days' => $days]);
    }

    return Task::where('organization_id', $context->org->id)
        ->openOrCompletedWithin($context->org->fresh()->completedWindowStart())
        ->orderBy('id')->pluck('id')->all();
}

test('open tasks are always included, however old', function (string $status) {
    $task = makeWindowTask($this, $status);
    $task->forceFill(['created_at' => '2020-01-01', 'updated_at' => '2020-01-01'])->saveQuietly();

    expect(windowIds($this))->toBe([$task->id]);
})->with(['pending', 'in_progress', 'in_review']);

test('completed inside, exactly at, and outside the 14-day window', function () {
    $inside = makeWindowTask($this, 'completed', '2026-10-25 08:00:00');
    $atStart = makeWindowTask($this, 'completed', '2026-10-16 12:00:00');
    $justOutside = makeWindowTask($this, 'completed', '2026-10-16 11:59:59');
    $old = makeWindowTask($this, 'completed', '2026-01-01 00:00:00');

    expect(windowIds($this))->toBe([$inside->id, $atStart->id])
        ->and(Task::where('organization_id', $this->org->id)->completedWithin($this->org->completedWindowStart())->orderBy('id')->pluck('id')->all())->toBe([$inside->id, $atStart->id])
        ->and(Task::where('organization_id', $this->org->id)->completedBefore($this->org->completedWindowStart())->orderBy('id')->pluck('id')->all())->toBe([$justOutside->id, $old->id]);
});

test('a completed task with no completed_at counts as inside the window', function () {
    $undated = makeWindowTask($this, 'completed', 'none');

    expect($undated->completed_at)->toBeNull()
        ->and(windowIds($this))->toBe([$undated->id])
        ->and(Task::where('organization_id', $this->org->id)->completedBefore($this->org->completedWindowStart())->count())->toBe(0);
});

test('the window follows the company setting: 7, 14 and 30 days', function () {
    $tenDaysAgo = makeWindowTask($this, 'completed', '2026-10-20 12:00:00');
    $twentyDaysAgo = makeWindowTask($this, 'completed', '2026-10-10 12:00:00');
    $fortyDaysAgo = makeWindowTask($this, 'completed', '2026-09-20 12:00:00');

    expect(windowIds($this, 7))->toBe([])
        ->and(windowIds($this, 14))->toBe([$tenDaysAgo->id])
        ->and(windowIds($this, 30))->toBe([$tenDaysAgo->id, $twentyDaysAgo->id]);

    expect($fortyDaysAgo->id)->not->toBeIn(windowIds($this, 30));
});

test('a reopened task shows like any open task, whatever its old completion date', function () {
    $task = makeWindowTask($this, 'completed', '2026-01-01 00:00:00');
    expect(windowIds($this))->toBe([]);

    $task->update(['status' => 'in_progress']);

    expect($task->fresh()->completed_at)->toBeNull()
        ->and(windowIds($this))->toBe([$task->id]);
});

test('the window start is now minus the company window, in UTC', function () {
    expect($this->org->completedWindowStart()->toDateTimeString())->toBe('2026-10-16 12:00:00')
        ->and($this->org->completedWindowStart()->timezoneName)->toBe('UTC');
});
