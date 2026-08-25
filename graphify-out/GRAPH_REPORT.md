# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 254 files · ~79,384 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1033 nodes · 2318 edges · 139 communities (114 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 134 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9fae3b9a`
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
- NotificationEventType.php
- scripts
- package.json
- Mermaid AI Skills
- StoreProjectRequest
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
- ProjectStatus.php
- NotificationSetting
- Comment
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- TaskManagementController
- Role
- Closure
- UserManagementController
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- ImportRow.php
- Department
- HidesInactiveFromNonAdmins.php
- Illuminate\Http\RedirectResponse
- AuditEventNotifier
- Priority.php
- LoginRequest
- Illuminate\Support\Collection

## God Nodes (most connected - your core abstractions)
1. `User` - 146 edges
2. `Organization` - 88 edges
3. `Task` - 59 edges
4. `OrgMember` - 54 edges
5. `Role` - 43 edges
6. `Project` - 41 edges
7. `Department` - 40 edges
8. `AuditLog` - 30 edges
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

## Communities (139 total, 25 thin omitted)

### Community 0 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 4 - "Organization"
Cohesion: 0.11
Nodes (5): CalendarController, DocumentController, KanbanController, Organization, Illuminate\Support\Carbon

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (29): concurrently, @fontsource/inter, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js (+21 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "StoreProjectRequest"
Cohesion: 0.20
Nodes (4): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, Illuminate\Contracts\Validation\ValidationRule

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
Cohesion: 0.15
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 87 - "User"
Cohesion: 0.08
Nodes (9): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+1 more)

### Community 88 - "OrgMember"
Cohesion: 0.07
Nodes (23): OrgMember, Permission, Project, TaskPriorityColor, TaskStatusColor, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+15 more)

### Community 90 - "Task"
Cohesion: 0.06
Nodes (15): SubtaskController, TaskDocumentController, Document, Subtask, Task, MentionedInCommentNotification, SubtaskObserver, TaskObserver (+7 more)

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 101 - "Comment"
Cohesion: 0.24
Nodes (3): CommentController, Comment, CommentObserver

### Community 107 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (11): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController (+3 more)

### Community 109 - "Role"
Cohesion: 0.12
Nodes (9): RoleManagementController, AccessPermission, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban() (+1 more)

### Community 110 - "Closure"
Cohesion: 0.27
Nodes (4): EnsureBelongsToOrganization, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 115 - "Department"
Cohesion: 0.15
Nodes (4): DepartmentManagementController, StoreDepartmentRequest, Department, makeTaskForAnalytics()

### Community 116 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.17
Nodes (4): AccessControlController, NotificationSettingsController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 119 - "Priority.php"
Cohesion: 0.07
Nodes (17): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), allColors(), badgeBackground() (+9 more)

### Community 134 - "Illuminate\Support\Collection"
Cohesion: 0.23
Nodes (4): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `NotificationEventType.php`, `ImportTemplateBuilder`, `AuditLog`, `OrgMember`, `Task`, `NotificationSetting`, `Comment`, `CommentPolicy`, `Illuminate\View\View`, `Role`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `AuditEventNotifier`, `LoginRequest`?**
  _High betweenness centrality (0.104) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Validation\Validator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `TaskManagementController`, `Role`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportTemplateBuilder`, `Department`, `Priority.php`, `Illuminate\Http\RedirectResponse`, `User`, `web.php`, `Task`, `OrgMember`?**
  _High betweenness centrality (0.073) - this node is a cross-community bridge._
- **Why does `Role` connect `Role` to `Illuminate\Validation\Validator`, `UpdateUserRequest`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `StoreProjectRequest`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportTemplateBuilder`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectStatus.php`, `NotificationSetting`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 14 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 14 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._