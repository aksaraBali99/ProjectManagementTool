<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Shared by TaskManagementController (Add/Edit Task's Assignee dropdown)
 * and KanbanController (the Kanban card's inline assignee select) so the
 * two surfaces can't drift out of sync on who's offered as an assignee.
 */
trait BuildsAssigneeOptions
{
    /**
     * Assignee options for a task/subtask are anyone attached to the
     * project — via project_staff (any role: management, staff, ...) or
     * project_clients (the project's client) — not scoped to a "Staff"
     * role, and not every company member company-wide, UNIONed with
     * anyone holding a global role (super_admin/owner). Global-role users
     * are deliberately never added to project_staff/project_clients —
     * they're global by design, not scoped to any one project — so
     * they're merged into every project's option list here rather than
     * needing an explicit membership row of their own (matches
     * ValidatesTaskAssignment::isAssignableStaffForProject(), the
     * server-side check for a submitted assignee id, so the two can't
     * drift out of sync). Joins the pivots directly rather than relying
     * on eager-loaded relations, so this works whether $projects is an
     * Eloquent or a plain Support collection (e.g.
     * Task::with('project')->get()->pluck('project')).
     *
     * @param  Collection<int, Project>  $projects
     * @return array<int, array<int, array{id: int, name: string}>> keyed by project id
     */
    private function staffOptionsByProject(Collection $projects): array
    {
        if ($projects->isEmpty()) {
            return [];
        }

        $projectIds = $projects->pluck('id');

        $staffRows = DB::table('project_staff')
            ->join('users', 'users.id', '=', 'project_staff.user_id')
            ->whereIn('project_staff.project_id', $projectIds)
            ->get(['project_staff.project_id', 'users.id', 'users.name']);

        $clientRows = DB::table('project_clients')
            ->join('users', 'users.id', '=', 'project_clients.user_id')
            ->whereIn('project_clients.project_id', $projectIds)
            ->get(['project_clients.project_id', 'users.id', 'users.name']);

        $globalRoleUsers = User::withGlobalRole()->orderBy('name')->get(['id', 'name']);

        $membersByProject = $staffRows->concat($clientRows)->groupBy('project_id');

        return $projects->mapWithKeys(function (Project $project) use ($membersByProject, $globalRoleUsers) {
            $members = ($membersByProject->get($project->id) ?? collect())
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->concat($globalRoleUsers->map(fn ($user) => ['id' => $user->id, 'name' => $user->name]))
                ->unique('id')
                ->sortBy('name')
                ->values();

            return [$project->id => $members];
        })->all();
    }
}
