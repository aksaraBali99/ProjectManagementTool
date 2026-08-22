# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 219 files · ~62,180 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 873 nodes · 1906 edges · 127 communities (98 shown, 29 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 116 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2b5edf5f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
- Illuminate\View\View
- NotificationSetting
- UpdateUserRequest
- StoreDepartmentRequest
- Task
- composer.json
- TaskManagementController.php
- scripts
- package.json
- Mermaid AI Skills
- .boardOrganizationIds
- LARAVEL_README.md
- AppServiceProvider.php
- Pest.php
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- tasks/index.blade.php
- CLAUDE.md
- copilot-instructions.md
- app.js
- _password-input.blade.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- User
- DepartmentPolicy
- UserManagementController.php
- Illuminate\Foundation\Http\FormRequest
- ProjectStatus.php
- AuditLog
- Illuminate\Http\RedirectResponse
- Illuminate\Http\JsonResponse
- Illuminate\Database\Eloquent\Relations\HasMany
- DocumentPolicy.php
- TaskStatus.php
- Subtask
- StoreTaskRequest
- StoreProjectRequest
- Comment
- LoginRequest
- UserFactory
- CommentPolicy
- NotificationSettingPolicy
- Illuminate\Http\Request
- TaskManagementController
- OrganizationPolicy
- AuditLogPolicy.php

## God Nodes (most connected - your core abstractions)
1. `User` - 136 edges
2. `Organization` - 81 edges
3. `Task` - 52 edges
4. `OrgMember` - 49 edges
5. `Role` - 42 edges
6. `Project` - 37 edges
7. `Department` - 34 edges
8. `AuditLog` - 29 edges
9. `Controller` - 25 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `makeStaffOnKanban()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (127 total, 29 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.06
Nodes (34): AccessPermission, Department, Document, Organization, OrgMember, Permission, Project, Role (+26 more)

### Community 1 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (9): AnalyticsController, CalendarController, Controller, KanbanController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController (+1 more)

### Community 3 - "UpdateUserRequest"
Cohesion: 0.08
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, ValidClientUser (+5 more)

### Community 4 - "StoreDepartmentRequest"
Cohesion: 0.16
Nodes (3): StoreDepartmentRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 5 - "Task"
Cohesion: 0.20
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (29): concurrently, @fontsource/inter, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js (+21 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 15 - "users/create.blade.php"
Cohesion: 0.40
Nodes (4): users._inline-validation, users._password-input, users._phone-input, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._inline-validation, users._password-input, users._phone-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.50
Nodes (3): tasks._comments, tasks._subtasks, tasks._documents

### Community 87 - "User"
Cohesion: 0.12
Nodes (7): User, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "AuditLog"
Cohesion: 0.08
Nodes (11): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, Illuminate\Bus\Queueable (+3 more)

### Community 101 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.17
Nodes (5): AccessControlController, AuthenticatedSessionController, GoogleAuthController, DepartmentManagementController, Illuminate\Http\RedirectResponse

### Community 105 - "Illuminate\Http\JsonResponse"
Cohesion: 0.21
Nodes (4): CommentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 109 - "Subtask"
Cohesion: 0.18
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 114 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 118 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (4): AuditTrailController, DocumentController, PermissionManagementController, Illuminate\Http\Request

## Knowledge Gaps
- **103 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+98 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **29 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `Illuminate\View\View`, `NotificationSetting`, `Task`, `TaskManagementController.php`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `DepartmentPolicy`, `UserManagementController.php`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentPolicy.php`, `TaskStatus.php`, `Subtask`, `LoginRequest`, `CommentPolicy`, `NotificationSettingPolicy`, `Illuminate\Http\Request`, `OrganizationPolicy`, `AuditLogPolicy.php`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\View\View`, `UpdateUserRequest`, `StoreDepartmentRequest`, `Illuminate\Http\RedirectResponse`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.boardOrganizationIds`, `TaskStatus.php`, `Illuminate\Http\Request`, `TaskManagementController`, `OrganizationPolicy`, `UserManagementController.php`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `Illuminate\View\View`, `Illuminate\Http\RedirectResponse`, `TaskManagementController.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `TaskStatus.php`, `Subtask`, `StoreTaskRequest`, `Illuminate\Http\Request`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Are the 11 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _103 weakly-connected nodes found - possible documentation gaps or missing edges._