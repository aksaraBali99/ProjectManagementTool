<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CalendarController extends Controller
{
    use ResolvesCurrentOrganization;

    public function __invoke(?Organization $organization = null): View
    {
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->boardOrganizationIds('view_calendar'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('calendar', [
                'organizations' => $organizations,
                'organization' => null,
                'emptyMessage' => $user->boardAccessDeniedReason('view_calendar')->message(),
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $view = request()->string('view')->toString() === 'week' ? 'week' : 'month';
        $anchor = $this->parseAnchorDate(request()->string('date')->toString());

        [$rangeStart, $rangeEnd] = $view === 'week'
            ? [$anchor->copy()->startOfWeek(Carbon::SUNDAY), $anchor->copy()->endOfWeek(Carbon::SATURDAY)]
            : [
                $anchor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY),
                $anchor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY),
            ];

        // Only tasks with a due_date can be placed on the grid — undated
        // tasks have no cell to sit in, so they're excluded here rather
        // than shown at some arbitrary fallback date (same rule the old
        // Gantt view applied, and the one Dashboard/Kanban never had to
        // make since neither positions tasks by date).
        $tasksByDate = Task::visibleTo($user, $organization->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->with(['project', 'department', 'assignee'])
            ->orderBy('title')
            ->get()
            ->groupBy(fn (Task $task) => $task->due_date->toDateString());

        $weeks = $this->buildWeeks($rangeStart, $rangeEnd, $view === 'week' ? null : $anchor->month, $tasksByDate);

        $canCreate = Gate::allows('create', [Task::class, $organization->id]);

        return view('calendar', [
            'organizations' => $organizations,
            'organization' => $organization,
            'view' => $view,
            'weeks' => $weeks,
            'rangeLabel' => $anchor->format('F Y'),
            'prevDate' => $this->shift($anchor, $view, -1)->toDateString(),
            'todayDate' => Carbon::today()->toDateString(),
            'nextDate' => $this->shift($anchor, $view, 1)->toDateString(),
            'canCreate' => $canCreate,
            // A task always belongs to a project, and the calendar isn't
            // project-scoped — same "pick the org's first project as a
            // starting point" convention the Tasks list's own top-of-page
            // "+ Add task" button already uses (tasks/index.blade.php).
            'defaultProject' => $canCreate ? $organization->projects()->orderBy('name')->first() : null,
        ]);
    }

    /**
     * $currentPeriodMonth is null for the week view — every cell there IS
     * the displayed period, unlike month view where leading/trailing days
     * from adjacent months need the muted "not this month" treatment.
     *
     * @return array<int, array<int, array{date: Carbon, isCurrentPeriod: bool, isToday: bool, tasks: Collection<int, Task>}>>
     */
    private function buildWeeks(Carbon $rangeStart, Carbon $rangeEnd, ?int $currentPeriodMonth, Collection $tasksByDate): array
    {
        $today = Carbon::today()->toDateString();
        $weeks = [];
        $cursor = $rangeStart->copy();

        while ($cursor->lte($rangeEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $cursor->toDateString();
                $week[] = [
                    'date' => $cursor->copy(),
                    'isCurrentPeriod' => $currentPeriodMonth === null || $cursor->month === $currentPeriodMonth,
                    'isToday' => $dateKey === $today,
                    'tasks' => $tasksByDate->get($dateKey, collect()),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    private function parseAnchorDate(string $raw): Carbon
    {
        if ($raw !== '') {
            try {
                return Carbon::parse($raw)->startOfDay();
            } catch (\Exception) {
                // Malformed ?date= value — fall through to today rather
                // than 500ing on a bad query string.
            }
        }

        return Carbon::today();
    }

    private function shift(Carbon $anchor, string $view, int $direction): Carbon
    {
        return $view === 'week'
            ? $anchor->copy()->addWeeks($direction)
            : $anchor->copy()->addMonthsNoOverflow($direction);
    }
}
