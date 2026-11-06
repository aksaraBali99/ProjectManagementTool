<?php

use App\Models\Organization;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;

/**
 * task #72 phase 7: Analytics adds "Completed in the last N days"
 * (?period=7|30|90, default 30), counted by completed_at across active
 * companies and active tasks. Every existing number is unchanged — they
 * still count everything, whatever the company window.
 */
beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-30 12:00:00'));
    setUpCompletedWindowCompany($this);

    makeWindowTask($this, 'completed', '2026-10-27 08:00:00');      // 3 days ago
    makeWindowTask($this, 'completed', '2026-10-15 08:00:00');      // 15 days ago
    makeWindowTask($this, 'completed', '2026-09-10 08:00:00');      // 50 days ago
    makeWindowTask($this, 'completed', '2026-06-01 08:00:00');      // 151 days ago
    makeWindowTask($this, 'pending');
    makeWindowTask($this, 'in_progress');
});

function analyticsFor(object $context, array $query = []): TestResponse
{
    return $context->actingAs($context->owner)->get(route('analytics.index', $query))->assertOk();
}

test('Completed in the last 30 days by default, counted by completed_at', function () {
    $response = analyticsFor($this);

    expect($response->viewData('completedPeriod'))->toBe(30)
        ->and($response->viewData('completedInPeriod'))->toBe(2);

    $response->assertSee('Completed in the last 30 days');
});

test('?period= picks 7, 30 or 90; anything else falls back to 30', function (mixed $period, int $expectedPeriod, int $expectedCount) {
    $response = analyticsFor($this, ['period' => $period]);

    expect($response->viewData('completedPeriod'))->toBe($expectedPeriod)
        ->and($response->viewData('completedInPeriod'))->toBe($expectedCount);
})->with([
    [7, 7, 1],
    [30, 30, 2],
    [90, 90, 3],
    [14, 30, 2],
    ['abc', 30, 2],
]);

test('inactive tasks and inactive companies are excluded', function () {
    makeWindowTask($this, 'completed', '2026-10-28 08:00:00')->delete();

    $inactive = Organization::create(['name' => 'Gone Co', 'slug' => 'gone-co', 'accent_color' => '#000000', 'is_active' => false]);
    makeWindowTask($this, 'completed', '2026-10-28 08:00:00', ['organization_id' => $inactive->id]);

    expect(analyticsFor($this)->viewData('completedInPeriod'))->toBe(2);
});

test('the existing numbers still count every task, whatever the window', function () {
    $this->org->update(['completed_window_days' => 7]);

    $response = analyticsFor($this, ['period' => 7]);

    $company = $response->viewData('completionByCompany')->firstWhere('label', 'Window Co');
    expect($company['completed'])->toBe(4)->and($company['total'])->toBe(6);

    $statusCounts = $response->viewData('statusCounts')->pluck('count', 'label');
    expect($statusCounts['Completed'])->toBe(4)
        ->and($response->viewData('priorityCounts')->sum('count'))->toBe(6);
});

test('the period links switch between 7, 30 and 90 days', function () {
    $html = analyticsFor($this, ['period' => 7])->getContent();

    expect($html)->toContain('href="'.e(route('analytics.index', ['period' => 30])).'"')
        ->toContain('href="'.e(route('analytics.index', ['period' => 90])).'"')
        ->not->toContain('href="'.e(route('analytics.index', ['period' => 7])).'"');
});
