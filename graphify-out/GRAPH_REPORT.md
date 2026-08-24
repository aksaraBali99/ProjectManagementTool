# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 226 files · ~68,800 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 917 nodes · 2003 edges · 131 communities (102 shown, 29 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 117 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `dadb1667`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Support\Collection
- .boardOrganizationIds
- Illuminate\View\View
- Comment
- Organization
- composer.json
- Subtask
- scripts
- package.json
- Mermaid AI Skills
- TaskStatus.php
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
- HidesInactiveFromNonAdmins.php
- AuditLog
- TaskManagementController
- DashboardController.php
- CommentPolicy
- Document
- Controller
- Role
- StoreProjectRequest
- CommentController.php
- UpdateDepartmentRequest
- UpdateProjectRequest
- OrganizationPolicy
- StoreDepartmentRequest
- SubtaskPolicy
- Illuminate\Http\RedirectResponse
- ValidatesTaskAssignment.php
- DocumentPolicy.php
- AuditLogPolicy.php
- DocumentController.php
- CalendarController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 143 edges
2. `Organization` - 87 edges
3. `Task` - 59 edges
4. `OrgMember` - 51 edges
5. `Role` - 42 edges
6. `Project` - 41 edges
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

## Communities (131 total, 29 thin omitted)

### Community 3 - "Illuminate\View\View"
Cohesion: 0.17
Nodes (5): DepartmentManagementController, NotificationController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 4 - "Comment"
Cohesion: 0.23
Nodes (3): CommentController, Comment, CommentObserver

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Subtask"
Cohesion: 0.27
Nodes (4): SubtaskController, Subtask, SubtaskObserver, Illuminate\Http\JsonResponse

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
Cohesion: 0.10
Nodes (7): User, DepartmentPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.07
Nodes (28): AccessPermission, Department, OrgMember, Project, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder (+20 more)

### Community 90 - "Task"
Cohesion: 0.19
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 93 - "AuditLog"
Cohesion: 0.08
Nodes (11): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, Illuminate\Bus\Queueable (+3 more)

### Community 107 - "Document"
Cohesion: 0.24
Nodes (4): TaskDocumentController, Document, makeDocumentForList(), makeDocument()

### Community 108 - "Controller"
Cohesion: 0.20
Nodes (5): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller

### Community 109 - "Role"
Cohesion: 0.05
Nodes (13): PermissionManagementController, UserManagementController, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, Permission (+5 more)

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (14): GoogleAuthController, NotificationSettingsController, EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, NotificationSetting, NotificationSettingPolicy, ValidClientUser (+6 more)

### Community 118 - "ValidatesTaskAssignment.php"
Cohesion: 0.15
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

## Knowledge Gaps
- **106 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+101 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **29 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Illuminate\View\View`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OrgMember`, `Task`, `AuditLog`, `DashboardController.php`, `CommentPolicy`, `Document`, `Controller`, `Role`, `OrganizationPolicy`, `SubtaskPolicy`, `Illuminate\Http\RedirectResponse`, `DocumentPolicy.php`, `AuditLogPolicy.php`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Illuminate\View\View`, `TaskManagementController`, `DashboardController.php`, `TaskStatus.php`, `Controller`, `Role`, `Document`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`, `ValidatesTaskAssignment.php`, `OrgMember`, `DocumentController.php`, `CalendarController.php`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\HasMany`, `Comment`, `TaskManagementController`, `Subtask`, `DashboardController.php`, `TaskStatus.php`, `Controller`, `Document`, `Role`, `CommentController.php`, `SubtaskPolicy`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OrgMember`, `CalendarController.php`?**
  _High betweenness centrality (0.039) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _106 weakly-connected nodes found - possible documentation gaps or missing edges._