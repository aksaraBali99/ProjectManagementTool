# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 238 files · ~74,577 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 960 nodes · 2118 edges · 137 communities (105 shown, 32 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 125 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `30b06765`
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
- Priority.php
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
- ProjectStatus.php
- AuditLog
- User
- OrgMember
- Task
- Illuminate\Foundation\Http\FormRequest
- NotificationEventType.php
- AuditEventNotifier
- NotificationSetting
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- UserPolicy
- RolePolicy
- LoginRequest
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateTaskPriorityColorsRequest
- StoreDepartmentRequest
- UpdateTaskStatusColorsRequest
- Illuminate\Http\RedirectResponse
- Document
- StoreProjectRequest
- DocumentController.php
- Controller
- Illuminate\Http\Request
- Illuminate\Support\Collection
- CalendarController.php
- .myTaskSection

## God Nodes (most connected - your core abstractions)
1. `User` - 144 edges
2. `Organization` - 87 edges
3. `Task` - 59 edges
4. `OrgMember` - 54 edges
5. `Role` - 42 edges
6. `Project` - 41 edges
7. `Department` - 39 edges
8. `AuditLog` - 29 edges
9. `Controller` - 26 edges
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

## Communities (137 total, 32 thin omitted)

### Community 0 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 4 - "Organization"
Cohesion: 0.13
Nodes (3): DepartmentManagementController, KanbanController, Organization

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Subtask"
Cohesion: 0.18
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

### Community 11 - "Priority.php"
Cohesion: 0.07
Nodes (16): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), allColors(), badgeBackground() (+8 more)

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

### Community 86 - "AuditLog"
Cohesion: 0.17
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 87 - "User"
Cohesion: 0.10
Nodes (8): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.08
Nodes (22): OrgMember, Permission, Project, TaskPriorityColor, TaskStatusColor, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+14 more)

### Community 90 - "Task"
Cohesion: 0.07
Nodes (13): CommentController, SubtaskController, TaskManagementController, isAssignableStaffForProject(), Comment, Task, MentionedInCommentNotification, CommentObserver (+5 more)

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 107 - "Illuminate\View\View"
Cohesion: 0.18
Nodes (5): NotificationController, OrganizationManagementController, SettingsController, TaskColorController, Illuminate\View\View

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (14): AccessControlController, NotificationSettingsController, RoleManagementController, UserManagementController, AccessPermission, Department, Role, Illuminate\Http\RedirectResponse (+6 more)

### Community 118 - "Document"
Cohesion: 0.18
Nodes (5): TaskDocumentController, Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 122 - "Controller"
Cohesion: 0.22
Nodes (5): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, PermissionManagementController

## Knowledge Gaps
- **107 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+102 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **32 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `.boardOrganizationIds`, `Illuminate\Http\Request`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.myTaskSection`, `Subtask`, `AuditLog`, `OrgMember`, `Task`, `NotificationEventType.php`, `AuditEventNotifier`, `NotificationSetting`, `CommentPolicy`, `Illuminate\View\View`, `UserPolicy`, `RolePolicy`, `LoginRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Document`, `Controller`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Validation\Validator`, `.boardOrganizationIds`, `Illuminate\Http\Request`, `Illuminate\Support\Collection`, `CalendarController.php`, `.myTaskSection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Priority.php`, `User`, `OrgMember`, `Task`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreDepartmentRequest`, `Illuminate\Http\RedirectResponse`, `Document`, `DocumentController.php`, `Controller`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Organization`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\HasMany`, `CalendarController.php`, `.myTaskSection`, `Subtask`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Document`, `OrgMember`, `Controller`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _107 weakly-connected nodes found - possible documentation gaps or missing edges._