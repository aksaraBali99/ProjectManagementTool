<?php

namespace App\Services\Import;

/**
 * Single source of truth for the Import template's column layout — the
 * template builder's header row (ImportTemplateBuilder) and the upload
 * parser (ImportSpreadsheetParser) both read from here, so header text and
 * parse mapping can never drift out of sync with each other.
 */
class ImportSheetSchema
{
    /**
     * Canonical tab order — also the validation/commit processing order.
     * "Reference" is a hidden dropdown-source sheet, not one of the 9
     * user-facing tabs, so it's tracked separately (see referenceSheetName()).
     *
     * @return array<int, string>
     */
    public static function sheetOrder(): array
    {
        return [
            'Companies',
            'Departments',
            'Users',
            'Company Roles',
            'Projects',
            'Tasks',
            'Subtasks',
            'Task Documents',
            'Task Comments',
        ];
    }

    public static function referenceSheetName(): string
    {
        return 'Reference';
    }

    /**
     * @return array<int, array{column: string, field: string, header: string, required: bool}>
     */
    public static function columns(string $sheetName): array
    {
        return match ($sheetName) {
            'Companies' => [
                ['column' => 'A', 'field' => 'id', 'header' => 'ID (do not edit)', 'required' => false],
                ['column' => 'B', 'field' => 'name', 'header' => 'Company Name*', 'required' => true],
            ],
            'Departments' => [
                ['column' => 'A', 'field' => 'id', 'header' => 'ID (do not edit)', 'required' => false],
                ['column' => 'B', 'field' => 'name', 'header' => 'Department Name*', 'required' => true],
                ['column' => 'C', 'field' => 'company', 'header' => 'Company*', 'required' => true],
            ],
            'Users' => [
                ['column' => 'A', 'field' => 'username', 'header' => 'Username*', 'required' => true],
                ['column' => 'B', 'field' => 'name', 'header' => 'Full Name*', 'required' => true],
                ['column' => 'C', 'field' => 'type', 'header' => 'Type* (Employee/Client)', 'required' => true],
                ['column' => 'D', 'field' => 'employee_id', 'header' => 'Employee ID (blank = auto-generated)', 'required' => false],
                ['column' => 'E', 'field' => 'email', 'header' => 'Email*', 'required' => true],
                ['column' => 'F', 'field' => 'phone', 'header' => 'Phone (with country code)*', 'required' => true],
                ['column' => 'G', 'field' => 'password', 'header' => 'Password (blank = default temp password)', 'required' => false],
            ],
            'Company Roles' => [
                ['column' => 'A', 'field' => 'username', 'header' => 'Username*', 'required' => true],
                ['column' => 'B', 'field' => 'company', 'header' => 'Company*', 'required' => true],
                ['column' => 'C', 'field' => 'role', 'header' => 'Role*', 'required' => true],
            ],
            'Projects' => [
                ['column' => 'A', 'field' => 'id', 'header' => 'ID (do not edit)', 'required' => false],
                ['column' => 'B', 'field' => 'name', 'header' => 'Project Name*', 'required' => true],
                ['column' => 'C', 'field' => 'description', 'header' => 'Description*', 'required' => true],
                ['column' => 'D', 'field' => 'company', 'header' => 'Company*', 'required' => true],
                ['column' => 'E', 'field' => 'client_username', 'header' => 'Client Username (blank = Internal project)', 'required' => false],
                ['column' => 'F', 'field' => 'status', 'header' => 'Status', 'required' => false],
                ['column' => 'G', 'field' => 'priority', 'header' => 'Priority', 'required' => false],
                ['column' => 'H', 'field' => 'staff_usernames', 'header' => 'Assigned Staff (usernames, comma-separated)', 'required' => false],
            ],
            'Tasks' => [
                ['column' => 'A', 'field' => 'task_ref', 'header' => 'Task Ref* (unique number in this file)', 'required' => true],
                ['column' => 'B', 'field' => 'project_name', 'header' => 'Project Name*', 'required' => true],
                ['column' => 'C', 'field' => 'company', 'header' => 'Company*', 'required' => true],
                ['column' => 'D', 'field' => 'department', 'header' => 'Department*', 'required' => true],
                ['column' => 'E', 'field' => 'title', 'header' => 'Title* (cannot be changed once imported)', 'required' => true],
                ['column' => 'F', 'field' => 'description', 'header' => 'Description', 'required' => false],
                ['column' => 'G', 'field' => 'start_date', 'header' => 'Start Date (DD/MM/YYYY, blank = none)', 'required' => false],
                ['column' => 'H', 'field' => 'due_date', 'header' => 'Due Date (DD/MM/YYYY, blank = none)', 'required' => false],
                ['column' => 'I', 'field' => 'priority', 'header' => 'Priority*', 'required' => true],
                ['column' => 'J', 'field' => 'status', 'header' => 'Status*', 'required' => true],
                ['column' => 'K', 'field' => 'assignee_username', 'header' => 'Assignee (username, any company member)', 'required' => false],
            ],
            'Subtasks' => [
                ['column' => 'A', 'field' => 'task_ref', 'header' => 'Task Ref*', 'required' => true],
                ['column' => 'B', 'field' => 'title', 'header' => 'Subtask Title*', 'required' => true],
                ['column' => 'C', 'field' => 'start_date', 'header' => 'Start Date (DD/MM/YYYY)', 'required' => false],
                ['column' => 'D', 'field' => 'due_date', 'header' => 'Due Date (DD/MM/YYYY)', 'required' => false],
                ['column' => 'E', 'field' => 'assignee_username', 'header' => 'Assignee (username, blank = same as parent task)', 'required' => false],
            ],
            'Task Documents' => [
                ['column' => 'A', 'field' => 'task_ref', 'header' => 'Task Ref*', 'required' => true],
                ['column' => 'B', 'field' => 'name', 'header' => 'Document Name*', 'required' => true],
                ['column' => 'C', 'field' => 'link', 'header' => 'Document Link*', 'required' => true],
            ],
            'Task Comments' => [
                ['column' => 'A', 'field' => 'task_ref', 'header' => 'Task Ref*', 'required' => true],
                ['column' => 'B', 'field' => 'body', 'header' => 'Comment Body*', 'required' => true],
            ],
            default => throw new \InvalidArgumentException("Unknown import sheet: {$sheetName}"),
        };
    }

    /** Legend text for row 2 (merged across every data column) per sheet. */
    public static function legend(string $sheetName): string
    {
        return match ($sheetName) {
            'Companies' => 'Column A is a hidden system reference — do NOT edit or delete it, it\'s what lets renames be detected as updates instead of creating duplicates. To ADD a new company, add a row below with Column A left BLANK and just fill in the name. To RENAME an existing company, edit its Name cell but leave Column A untouched. The rows below are your REAL, existing companies — editing one updates it for real; leave a row untouched to leave that company alone.',
            'Departments' => 'Same rule as Companies: Column A is a system reference, leave blank for new rows, never edit for existing ones. Add a row to create a new department for an existing or newly-added company. The rows below are your REAL, existing departments — editing one updates it for real; leave a row untouched to leave that department alone.',
            'Users' => 'Matched for updates by Username OR Email — if a row\'s Username matches one existing user but its Email matches a DIFFERENT existing user, that\'s a hard error, fix the data first. Type determines the Employee ID prefix if left blank (EMP- or CLIENT-) and must be consistent with the roles assigned on the Company Roles tab. Role and Company assignment happen on Company Roles, not here. Example row: jdoe, Jane Doe, Employee, (blank), jane.doe@example.com, +6281234567890, (blank). This sheet starts empty below the header — every row you add here WILL be imported for real, there is no separate example row to delete.',
            'Company Roles' => 'One row per (user, company) pair. Every user needs AT LEAST ONE row here to be valid, regardless of Type — including new Clients, even if they have no project yet. This tab is always fully re-synced on import, not tracked as insert/update like other tabs. Role must match the user\'s Type on the Users tab: Client Type users must use Role=Client here; Employee Type users must use Staff or Management. Example row: jdoe, Acme Corp, Staff. This sheet starts empty below the header — every row you add here WILL be imported for real.',
            'Projects' => 'Column A is a system reference (see Companies tab note) — blank for new projects, never edit for existing ones. Client Username must belong to a user with Role=Client on the Company Roles tab, FOR THIS SAME COMPANY — validated strictly on import even though the live in-app dropdown doesn\'t fully enforce this scoping yet. Leave blank for an internal project. Status defaults to Open, Priority to Medium if left blank. Example row: (blank ID), Website Refresh, Redesign the homepage., Acme Corp, (blank client), Open, Medium, jdoe. This sheet starts empty below the header — every row you add here WILL be imported for real.',
            'Tasks' => 'Task Ref is just a number YOU assign (1, 2, 3...) unique within this file — it links rows on the Subtasks/Task Documents/Task Comments tabs back to this task, it is NOT saved to the database. Matched for updates by Project + Title — title itself cannot be changed via import, if it doesn\'t match exactly a new task is created instead of updating. Assignee can be ANY member of the company. Project Name + Company must match an existing/newly-created project. Example row: 1, Website Refresh, Acme Corp, Marketing, Draft homepage copy, (description), (blank start), (blank due), Medium, Pending, jdoe. This sheet starts empty below the header — every row you add here WILL be imported for real.',
            'Subtasks' => 'Task Ref must match a number used on the Tasks tab in THIS file — subtasks can only be added for tasks being created/updated in this same import, not attached to arbitrary existing tasks by title. Multiple rows can share the same Task Ref (one row per subtask). Start Date/Due Date/Assignee default to the parent task\'s values if left blank. Example row: 1, Research competitors, (blank), (blank), (blank). This sheet starts empty below the header — every row you add here WILL be imported for real.',
            'Task Documents' => 'Task Ref must match a number used on the Tasks tab in this file. No limit on how many document rows one task can have — add as many rows as needed, all sharing the same Task Ref. Example row: 1, Brief, https://example.com/brief. This sheet starts empty below the header — every row you add here WILL be imported for real.',
            'Task Comments' => 'Task Ref must match a number used on the Tasks tab in this file. Imported comments are always attributed to the account running the import, not to a name typed here — there\'s no author column by design. Example row: 1, Kicking this off. This sheet starts empty below the header — every row you add here WILL be imported for real.',
            default => throw new \InvalidArgumentException("Unknown import sheet: {$sheetName}"),
        };
    }
}
