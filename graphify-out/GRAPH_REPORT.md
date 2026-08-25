# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 245 files · ~78,172 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1004 nodes · 2274 edges · 137 communities (109 shown, 28 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 133 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7bb3bd1f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Validation\Validator
- Illuminate\Database\Eloquent\Relations\BelongsTo
- .boardOrganizationIds
- UpdateUserRequest
- Organization
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- Subtask
- scripts
- package.json
- Mermaid AI Skills
- StoreTaskRequest
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
- ImportTemplateBuilder
- AuditLog
- User
- OrgMember
- Task
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Http\JsonResponse
- Project
- Comment
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- TaskManagementController
- Role
- LoginRequest
- UserManagementController
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateDepartmentRequest
- StoreDepartmentRequest
- UpdateTaskStatusColorsRequest
- Illuminate\Http\RedirectResponse
- Document
- UpdateTaskRequest
- AuditLogPolicy.php
- Illuminate\Http\Request
- Illuminate\Support\Collection
- CalendarController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 146 edges
2. `Organization` - 88 edges
3. `Task` - 59 edges
4. `OrgMember` - 54 edges
5. `Role` - 43 edges
6. `Project` - 41 edges
7. `Department` - 40 edges
8. `AuditLog` - 29 edges
9. `Controller` - 27 edges
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

## Communities (137 total, 28 thin omitted)

### Community 0 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 4 - "Organization"
Cohesion: 0.12
Nodes (4): AccessControlController, DocumentController, OrganizationManagementController, Organization

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

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

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.16
Nodes (9): ImportController, ImportIdCodec, ImportSheetSchema, ImportTemplateBuilder, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, Symfony\Component\HttpFoundation\StreamedResponse, downloadTemplateSpreadsheet() (+1 more)

### Community 86 - "AuditLog"
Cohesion: 0.07
Nodes (13): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+5 more)

### Community 87 - "User"
Cohesion: 0.09
Nodes (8): User, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.06
Nodes (21): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), allColors(), badgeBackground() (+13 more)

### Community 90 - "Task"
Cohesion: 0.14
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): CommentController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 93 - "Project"
Cohesion: 0.16
Nodes (6): Project, ProjectPolicy, makeProjectMember(), makeClientForDocumentList(), makeClientForDocuments(), makeClientOnProject()

### Community 107 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (10): AnalyticsController, AuthenticatedSessionController, Controller, DepartmentManagementController, KanbanController, NotificationController, RoleManagementController, SettingsController (+2 more)

### Community 109 - "Role"
Cohesion: 0.05
Nodes (22): StoreProjectRequest, UpdateProjectRequest, AccessPermission, Department, Permission, Role, RolePolicy, DatabaseSeeder (+14 more)

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (3): GoogleAuthController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 118 - "Document"
Cohesion: 0.28
Nodes (4): TaskDocumentController, Document, makeDocumentForList(), makeDocument()

### Community 133 - "Illuminate\Http\Request"
Cohesion: 0.22
Nodes (5): AuditTrailController, DashboardController, Collection, PermissionManagementController, Illuminate\Http\Request

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **28 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `.boardOrganizationIds`, `UpdateUserRequest`, `Organization`, `Illuminate\Http\Request`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `ImportTemplateBuilder`, `AuditLog`, `OrgMember`, `Task`, `Illuminate\Http\JsonResponse`, `Project`, `CommentPolicy`, `Illuminate\View\View`, `Role`, `LoginRequest`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Document`, `AuditLogPolicy.php`?**
  _High betweenness centrality (0.129) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Validation\Validator`, `.boardOrganizationIds`, `Illuminate\Http\Request`, `Illuminate\Support\Collection`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `TaskManagementController`, `Role`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportTemplateBuilder`, `StoreDepartmentRequest`, `Illuminate\Http\RedirectResponse`, `Document`, `User`, `OrgMember`, `Project`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\Request`, `Comment`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `Illuminate\View\View`, `TaskManagementController`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Document`, `OrgMember`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 14 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 14 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._