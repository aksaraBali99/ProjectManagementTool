# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 223 files · ~66,875 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 905 nodes · 1963 edges · 131 communities (104 shown, 27 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 116 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c43ebb43`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- NotificationEventType.php
- Illuminate\Support\Collection
- .boardOrganizationIds
- Controller
- Comment
- Organization
- composer.json
- Subtask
- scripts
- package.json
- Mermaid AI Skills
- AuditEventNotifier
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
- OrgMember
- Task
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- AuditLog
- TaskManagementController
- Illuminate\Http\Request
- CommentPolicy
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Role
- StoreProjectRequest
- DepartmentPolicy
- UpdateDepartmentRequest
- UpdateProjectRequest
- NotificationSettingPolicy
- StoreDepartmentRequest
- StoreUserRequest
- Illuminate\Http\RedirectResponse
- TaskStatus.php
- UpdateUserRequest
- Illuminate\Database\Eloquent\Builder
- CalendarController.php
- UpdateRoleRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 140 edges
2. `Organization` - 87 edges
3. `Task` - 55 edges
4. `OrgMember` - 50 edges
5. `Role` - 42 edges
6. `Project` - 39 edges
7. `Department` - 38 edges
8. `AuditLog` - 29 edges
9. `Controller` - 25 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (131 total, 27 thin omitted)

### Community 3 - "Controller"
Cohesion: 0.11
Nodes (7): AnalyticsController, AuthenticatedSessionController, Controller, NotificationController, PermissionManagementController, RoleManagementController, SettingsController

### Community 5 - "Organization"
Cohesion: 0.12
Nodes (7): AccessControlController, DepartmentManagementController, DocumentController, KanbanController, OrganizationManagementController, Organization, Illuminate\View\View

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

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

### Community 47 - "app.js"
Cohesion: 0.20
Nodes (12): buildDisplayRows(), CALENDAR_VIEW_DAYS, CHART_PALETTE, DAY_ABBREVIATIONS, DAY_VIEW_MODE_WITH_WEEKDAY, formatLocalDate(), formatPopupDate(), getCalendarViewStart() (+4 more)

### Community 87 - "User"
Cohesion: 0.09
Nodes (8): User, AuditLogPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.07
Nodes (26): AccessPermission, Department, OrgMember, Project, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder (+18 more)

### Community 90 - "Task"
Cohesion: 0.06
Nodes (13): CommentController, SubtaskController, TaskDocumentController, Document, Task, TaskObserver, DocumentPolicy, TaskPolicy (+5 more)

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 93 - "AuditLog"
Cohesion: 0.16
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 105 - "Illuminate\Http\Request"
Cohesion: 0.27
Nodes (4): AuditTrailController, DashboardController, Collection, Illuminate\Http\Request

### Community 107 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.14
Nodes (3): Permission, PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 109 - "Role"
Cohesion: 0.20
Nodes (3): UserManagementController, Role, RolePolicy

### Community 116 - "StoreUserRequest"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (4): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 118 - "TaskStatus.php"
Cohesion: 0.11
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

## Knowledge Gaps
- **106 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+101 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **27 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `NotificationEventType.php`, `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Controller`, `Organization`, `Subtask`, `AuditEventNotifier`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OrgMember`, `Task`, `LoginRequest`, `AuditLog`, `Illuminate\Http\Request`, `CommentPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `DepartmentPolicy`, `NotificationSettingPolicy`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`?**
  _High betweenness centrality (0.139) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Controller`, `TaskManagementController`, `Task`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `StoreDepartmentRequest`, `StoreUserRequest`, `Illuminate\Http\RedirectResponse`, `User`, `OrgMember`, `CalendarController.php`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Controller`, `Comment`, `Organization`, `TaskManagementController`, `Subtask`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OrgMember`, `CalendarController.php`, `Illuminate\Database\Eloquent\Builder`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 11 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _106 weakly-connected nodes found - possible documentation gaps or missing edges._