<?php

use App\Support\DateRange;
use Carbon\Carbon;

test('due before start is only true when both are set and due is earlier', function (?string $start, ?string $due, bool $expected) {
    expect(DateRange::dueBeforeStart($start, $due))->toBe($expected);
})->with([
    'both empty' => [null, null, false],
    'start only' => ['2026-12-10', null, false],
    'due only' => [null, '2026-12-05', false],
    'empty strings' => ['', '', false],
    'same day' => ['2026-12-10', '2026-12-10', false],
    'due after start' => ['2026-12-10', '2026-12-11', false],
    'due before start' => ['2026-12-10', '2026-12-09', true],
    'across a year' => ['2027-01-01', '2026-12-31', true],
]);

test('latest ignores empty dates', function () {
    expect(DateRange::latest([null, '2026-12-05', '', '2026-12-10', '2026-11-30']))->toBe('2026-12-10');
    expect(DateRange::latest([null, '']))->toBeNull();
});

test('normalize gives Y-m-d or null', function () {
    expect(DateRange::normalize(null))->toBeNull();
    expect(DateRange::normalize(' '))->toBeNull();
    expect(DateRange::normalize('2026-12-05'))->toBe('2026-12-05');
    expect(DateRange::normalize(Carbon::create(2026, 12, 5, 14, 30)))->toBe('2026-12-05');
});

test('format reads like 10 Dec 2026', function () {
    expect(DateRange::format('2026-12-10'))->toBe('10 Dec 2026');
    expect(DateRange::format('2026-01-05'))->toBe('5 Jan 2026');
});
