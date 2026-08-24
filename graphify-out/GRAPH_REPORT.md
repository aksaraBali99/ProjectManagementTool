# Graph Report - ProjectManagementTool  (2026-08-24)

## Corpus Check
- 238 files · ~74,312 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 960 nodes · 2124 edges · 132 communities (103 shown, 29 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 121 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4ddd754e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Validation\Validator
- Illuminate\Database\Eloquent\Relations\BelongsTo
- .boardOrganizationIds
- UpdateUserRequest
- Comment
- Organization
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
- DepartmentPolicy
- UserPolicy
- RolePolicy
- LoginRequest
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateTaskPriorityColorsRequest
- StoreDepartmentRequest
- UpdateTaskStatusColorsRequest
- Illuminate\View\View
- StoreProjectRequest
- AuditLogPolicy.php

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
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `makeStaffOnKanban()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeTaskOnDashboard()` --references--> `Department`  [EXTRACTED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Department.php

## Import Cycles
- None detected.

## Communities (132 total, 29 thin omitted)

### Community 0 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 5 - "Organization"
Cohesion: 0.07
Nodes (6): AnalyticsController, CalendarController, KanbanController, Organization, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Support\Carbon

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

### Community 11 - "Priority.php"
Cohesion: 0.07
Nodes (18): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), allColors(), badgeBackground() (+10 more)

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
Cohesion: 0.13
Nodes (8): AuditTrailController, AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 87 - "User"
Cohesion: 0.10
Nodes (7): User, OrganizationPolicy, ProjectPolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "OrgMember"
Cohesion: 0.06
Nodes (32): AccessControlController, RoleManagementController, isAssignableStaffForProject(), AccessPermission, Department, OrgMember, Project, Role (+24 more)

### Community 90 - "Task"
Cohesion: 0.07
Nodes (14): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Task, MentionedInCommentNotification, TaskObserver (+6 more)

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 117 - "Illuminate\View\View"
Cohesion: 0.05
Nodes (18): AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, Collection, DepartmentManagementController, NotificationController, NotificationSettingsController (+10 more)

## Knowledge Gaps
- **107 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+102 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **29 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `.boardOrganizationIds`, `Organization`, `Subtask`, `AuditLog`, `OrgMember`, `Task`, `NotificationEventType.php`, `AuditEventNotifier`, `NotificationSetting`, `CommentPolicy`, `DepartmentPolicy`, `UserPolicy`, `RolePolicy`, `LoginRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `.accessPermissions`, `AuditLogPolicy.php`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Validation\Validator`, `.boardOrganizationIds`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreDepartmentRequest`, `Illuminate\View\View`, `AuditLog`, `User`, `OrgMember`, `Task`, `Illuminate\Foundation\Http\FormRequest`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Organization`, `Subtask`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `AuditLog`, `User`, `OrgMember`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Are the 12 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _107 weakly-connected nodes found - possible documentation gaps or missing edges._