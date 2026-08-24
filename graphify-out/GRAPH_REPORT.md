# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 234 files · ~73,149 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 944 nodes · 2088 edges · 133 communities (108 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 130 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `8ed5cb2a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UserManagementController
- Illuminate\Support\Collection
- .boardOrganizationIds
- Illuminate\View\View
- Comment
- Illuminate\Database\Eloquent\Relations\HasMany
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
- Priority.php
- NotificationSetting.php
- User
- OrgMember
- Document
- Illuminate\Foundation\Http\FormRequest
- NotificationEventType.php
- AuditLog
- NotificationSetting
- DashboardController.php
- _form.blade.php
- Project
- Organization
- Role
- LoginRequest
- AuditEventDatabaseNotification
- UpdateDepartmentRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreTaskRequest
- StoreDepartmentRequest
- OrganizationPolicy
- Task
- UpdateTaskRequest
- StoreProjectRequest
- UpdateProjectRequest
- DocumentAccessLevel.php
- CalendarController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 144 edges
2. `Organization` - 87 edges
3. `Task` - 59 edges
4. `OrgMember` - 53 edges
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

## Communities (133 total, 25 thin omitted)

### Community 0 - "UserManagementController"
Cohesion: 0.08
Nodes (8): UserManagementController, UpdateRoleRequest, UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, Illuminate\Validation\Validator

### Community 3 - "Illuminate\View\View"
Cohesion: 0.13
Nodes (4): KanbanController, NotificationController, SettingsController, Illuminate\View\View

### Community 4 - "Comment"
Cohesion: 0.21
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Subtask"
Cohesion: 0.19
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

### Community 11 - "TaskStatus.php"
Cohesion: 0.18
Nodes (5): allColors(), badgeBackground(), badgeText(), colorRow(), TaskStatusColor

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

### Community 50 - "Priority.php"
Cohesion: 0.15
Nodes (3): values(), values(), makeTaskOnDashboard()

### Community 86 - "NotificationSetting.php"
Cohesion: 0.21
Nodes (5): values(), AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage

### Community 87 - "User"
Cohesion: 0.09
Nodes (8): User, AuditLogPolicy, DepartmentPolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.09
Nodes (10): organization(), OrgMember, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeClientForDocuments(), makeStaffForDocuments() (+2 more)

### Community 90 - "Document"
Cohesion: 0.18
Nodes (6): Document, DocumentPolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeDocument()

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "NotificationEventType.php"
Cohesion: 0.27
Nodes (3): values(), NotificationSettingsResolver, NotificationEventType

### Community 93 - "AuditLog"
Cohesion: 0.17
Nodes (3): AuditLog, AuditEventNotifier, NotificationEventType

### Community 107 - "Project"
Cohesion: 0.22
Nodes (4): isAssignableStaffForProject(), Project, ProjectPolicy, makeProjectMember()

### Community 108 - "Organization"
Cohesion: 0.16
Nodes (5): values(), AnalyticsController, AuditTrailController, Organization, self

### Community 109 - "Role"
Cohesion: 0.06
Nodes (19): AccessPermission, Department, Permission, Role, RolePolicy, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+11 more)

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 111 - "AuditEventDatabaseNotification"
Cohesion: 0.22
Nodes (3): AuditEventDatabaseNotification, MentionedInCommentNotification, Illuminate\Notifications\Notification

### Community 117 - "Task"
Cohesion: 0.06
Nodes (21): AccessControlController, AuthenticatedSessionController, GoogleAuthController, CommentController, Controller, DepartmentManagementController, DocumentController, NotificationSettingsController (+13 more)

## Knowledge Gaps
- **107 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+102 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `UserManagementController`, `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Illuminate\View\View`, `Comment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `OrgMember`, `Document`, `NotificationEventType.php`, `AuditLog`, `NotificationSetting`, `DashboardController.php`, `Project`, `Organization`, `Role`, `LoginRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrganizationPolicy`, `Task`?**
  _High betweenness centrality (0.136) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `UserManagementController`, `Illuminate\Support\Collection`, `.boardOrganizationIds`, `Illuminate\View\View`, `Document`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DashboardController.php`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Priority.php`, `StoreDepartmentRequest`, `OrganizationPolicy`, `Task`, `OrgMember`, `CalendarController.php`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `DashboardController.php`, `Organization`, `Role`, `AuditEventDatabaseNotification`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Priority.php`, `User`, `OrgMember`, `CalendarController.php`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _107 weakly-connected nodes found - possible documentation gaps or missing edges._