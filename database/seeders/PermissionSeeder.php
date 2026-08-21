<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seeds the exact permission set replicating current hardcoded-role
     * behavior — this seed changes nothing about what any role can
     * currently do, only how that's determined. See CLAUDE.md / the
     * permission-system PR for the audit behind these grants.
     */
    public function run(): void
    {
        $permissions = [
            ['slug' => 'view_projects', 'name' => 'View projects', 'group' => 'Projects'],
            ['slug' => 'create_edit_projects', 'name' => 'Create & edit projects', 'group' => 'Projects'],
            ['slug' => 'view_tasks', 'name' => 'View tasks', 'group' => 'Tasks'],
            ['slug' => 'create_edit_tasks', 'name' => 'Create & edit tasks', 'group' => 'Tasks'],
            ['slug' => 'toggle_subtask_done', 'name' => 'Toggle subtask done', 'group' => 'Tasks'],
            // Comments live under Tasks, not their own group — a comment
            // always belongs to exactly one task (comments.task_id is a
            // required FK, no other entity has comments), so there's no
            // scenario where this permission applies outside a task.
            ['slug' => 'view_comments', 'name' => 'View comments', 'group' => 'Tasks'],
            ['slug' => 'add_edit_own_comment', 'name' => 'Add / edit own comments', 'group' => 'Tasks'],
            ['slug' => 'view_dashboard', 'name' => 'View dashboard', 'group' => 'Dashboard'],
            ['slug' => 'view_kanban', 'name' => 'View kanban board', 'group' => 'Kanban'],
            ['slug' => 'update_kanban_cards', 'name' => 'Update cards (move to change status)', 'group' => 'Kanban'],
            ['slug' => 'manage_settings', 'name' => 'Manage settings', 'group' => 'Administration'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $allSlugs = collect($permissions)->pluck('slug')->all();

        $grants = [
            Role::SUPER_ADMIN => $allSlugs,
            Role::OWNER => $allSlugs,
            Role::MANAGEMENT => [
                'view_projects', 'create_edit_projects',
                'view_tasks', 'create_edit_tasks', 'toggle_subtask_done',
                'view_comments', 'add_edit_own_comment',
                'view_dashboard', 'view_kanban', 'update_kanban_cards',
            ],
            Role::STAFF => [
                'view_projects',
                'view_tasks', 'toggle_subtask_done',
                'view_comments', 'add_edit_own_comment',
                // Board access is gated here, but which companies/tasks
                // actually show up is still department-gated via
                // access_permissions — this permission doesn't widen that.
                // update_kanban_cards is deliberately NOT granted — staff
                // can still move a card they're the assignee of, via
                // TaskPolicy::updateStatus()'s own identity bypass, same as
                // they can edit a task assigned to them without
                // create_edit_tasks.
                'view_dashboard', 'view_kanban',
            ],
            Role::CLIENT => [
                'view_projects',
                'view_tasks', // narrowed to their own project's tasks in TaskPolicy, not company-wide
                'view_comments', 'add_edit_own_comment',
                // Same: board access is gated here, but still narrowed to
                // their own attached project(s) via User::boardOrganizationIds()
                // and Task::scopeVisibleTo() — this doesn't widen that.
                // update_kanban_cards not granted, same reasoning as staff.
                'view_dashboard', 'view_kanban',
            ],
        ];

        foreach ($grants as $roleSlug => $permissionSlugs) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();
            $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
