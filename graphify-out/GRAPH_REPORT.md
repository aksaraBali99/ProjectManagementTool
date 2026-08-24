# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 228 files · ~71,388 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 923 nodes · 2024 edges · 126 communities (103 shown, 23 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 119 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0510a0f0`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- StoreUserRequest
- Illuminate\Support\Collection
- UpdateUserRequest
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
- UpdateRoleRequest
- AuditLog
- TaskManagementController
- .myTaskSection
- _form.blade.php
- Illuminate\Http\JsonResponse
- Controller
- Role
- LoginRequest
- MentionedInCommentNotification
- UpdateDepartmentRequest
- StoreDepartmentRequest
- Illuminate\Http\RedirectResponse
- ValidatesTaskAssignment.php
- DocumentController.php
- CalendarController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 144 edges
2. `Organization` - 87 edges
3. `Task` - 59 edges
4. `OrgMember` - 52 edges
5. `Role` - 42 edges
6. `Project` - 41 edges
7. `Department` - 39 edges
8. `AuditLog` - 29 edges
9. `Controller` - 25 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (126 total, 23 thin omitted)

### Community 0 - "StoreUserRequest"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 3 - "Illuminate\View\View"
Cohesion: 0.15
Nodes (6): DepartmentManagementController, NotificationController, PermissionManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 5 - "Organization"
Cohesion: 0.10
Nodes (3): OrganizationManagementController, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Subtask"
Cohesion: 0.25
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
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.50
Nodes (3): tasks._comments, tasks._subtasks, tasks._documents

### Community 47 - "app.js"
Cohesion: 0.20
Nodes (12): buildDisplayRows(), CALENDAR_VIEW_DAYS, CHART_PALETTE, DAY_ABBREVIATIONS, DAY_VIEW_MODE_WITH_WEEKDAY, formatLocalDate(), formatPopupDate(), getCalendarViewStart() (+4 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (14): Permission, Collection, User, AuditLogPolicy, CommentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy (+6 more)

### Community 88 - "OrgMember"
Cohesion: 0.08
Nodes (18): Document, OrgMember, Project, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Notification, makeTaskForAnalytics(), makeProjectMember(), makeTaskOnDashboard() (+10 more)

### Community 90 - "Task"
Cohesion: 0.17
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "AuditLog"
Cohesion: 0.07
Nodes (13): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+5 more)

### Community 107 - "Illuminate\Http\JsonResponse"
Cohesion: 0.20
Nodes (4): CommentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 108 - "Controller"
Cohesion: 0.20
Nodes (5): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller

### Community 109 - "Role"
Cohesion: 0.06
Nodes (17): AccessPermission, Department, Role, DepartmentPolicy, RolePolicy, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+9 more)

### Community 110 - "LoginRequest"
Cohesion: 0.05
Nodes (15): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, StoreProjectRequest, UpdateProjectRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser (+7 more)

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (5): GoogleAuthController, NotificationSettingsController, UserManagementController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 118 - "ValidatesTaskAssignment.php"
Cohesion: 0.17
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

## Knowledge Gaps
- **107 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+102 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **23 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `UpdateUserRequest`, `Illuminate\View\View`, `Organization`, `Subtask`, `.myTaskSection`, `Illuminate\Http\JsonResponse`, `Controller`, `Role`, `LoginRequest`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `OrgMember`, `Task`, `AuditLog`?**
  _High betweenness centrality (0.139) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `StoreUserRequest`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `TaskManagementController`, `.myTaskSection`, `TaskStatus.php`, `Controller`, `Role`, `StoreDepartmentRequest`, `Illuminate\Http\RedirectResponse`, `User`, `OrgMember`, `DocumentController.php`, `CalendarController.php`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Comment`, `TaskManagementController`, `Subtask`, `.myTaskSection`, `Illuminate\Http\JsonResponse`, `Controller`, `TaskStatus.php`, `Role`, `MentionedInCommentNotification`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `User`, `OrgMember`, `CalendarController.php`?**
  _High betweenness centrality (0.039) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _107 weakly-connected nodes found - possible documentation gaps or missing edges._