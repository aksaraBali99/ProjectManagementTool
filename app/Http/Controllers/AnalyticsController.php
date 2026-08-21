<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Cross-company founder oversight — deliberately not scoped through
 * Task::scopeVisibleTo() the way every other task view is, since only
 * super_admin/owner (via manage_settings) can reach this page at all, and
 * that pair already has unrestricted visibility everywhere else in the app.
 * The only scoping here is "active companies" and "not soft-deleted" —
 * Task's own SoftDeletes default excludes trashed rows without any extra
 * query condition, as long as withTrashed()/onlyTrashed() is never called.
 */
class AnalyticsController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('analytics.view');

        $organizations = Organization::where('is_active', true)->orderBy('name')->get();

        $completionByCompany = $organizations->map(function (Organization $organization) {
            $total = Task::where('organization_id', $organization->id)->count();
            $completed = Task::where('organization_id', $organization->id)->where('status', TaskStatus::Completed->value)->count();

            return [
                'label' => $organization->name,
                'rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0.0,
                'completed' => $completed,
                'total' => $total,
            ];
        });

        $organizationIds = $organizations->pluck('id');

        $statusCounts = collect(TaskStatus::cases())->map(fn (TaskStatus $status) => [
            'label' => $status->label(),
            'count' => Task::whereIn('organization_id', $organizationIds)->where('status', $status->value)->count(),
        ]);

        $priorityCounts = collect(Priority::cases())->map(fn (Priority $priority) => [
            'label' => $priority->label(),
            'count' => Task::whereIn('organization_id', $organizationIds)->where('priority', $priority->value)->count(),
        ]);

        $overdueCount = Task::whereIn('organization_id', $organizationIds)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->count();

        $staffRoleId = Role::where('slug', Role::STAFF)->value('id');
        $staffIds = User::whereHas(
            'orgMemberships',
            fn ($query) => $query->whereIn('organization_id', $organizationIds)->where('role_id', $staffRoleId)
        )->pluck('id');

        $staffWorkload = Task::whereIn('organization_id', $organizationIds)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereIn('assignee_id', $staffIds)
            ->selectRaw('assignee_id, count(*) as open_count')
            ->groupBy('assignee_id')
            ->get()
            ->map(fn ($row) => [
                'label' => User::find($row->assignee_id)?->name ?? 'Unknown',
                'count' => (int) $row->open_count,
            ])
            ->sortByDesc('count')
            ->values();

        return view('analytics', [
            'completionByCompany' => $completionByCompany,
            'statusCounts' => $statusCounts,
            'priorityCounts' => $priorityCounts,
            'overdueCount' => $overdueCount,
            'staffWorkload' => $staffWorkload,
        ]);
    }
}
