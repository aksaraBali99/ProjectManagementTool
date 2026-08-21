# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 217 files · ~61,653 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 864 nodes · 1899 edges · 124 communities (99 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 116 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7c3c05b1`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Illuminate\View\View
- Illuminate\Http\RedirectResponse
- LoginRequest
- Department
- Task
- composer.json
- Project
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
- Role
- User
- DepartmentPolicy
- UserManagementController.php
- Illuminate\Foundation\Http\FormRequest
- ProjectStatus.php
- AuditLog
- StoreUserRequest
- Illuminate\Http\Request
- Organization
- Document
- web.php
- UpdateUserRequest
- UpdateDepartmentRequest
- StoreProjectRequest
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Database\Seeder
- CommentPolicy
- NotificationSettingPolicy
- .store
- PermissionManagementController.php
- Permission

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

## Communities (124 total, 25 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.08
Nodes (13): organization(), OrgMember, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Support\Facades\Notification, makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeClientForDocumentList() (+5 more)

### Community 1 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (9): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController (+1 more)

### Community 2 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.16
Nodes (4): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 3 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 4 - "Department"
Cohesion: 0.17
Nodes (3): DepartmentManagementController, StoreDepartmentRequest, Department

### Community 5 - "Task"
Cohesion: 0.10
Nodes (8): Subtask, Task, SubtaskObserver, TaskObserver, SubtaskPolicy, TaskPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\SoftDeletes

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Project"
Cohesion: 0.07
Nodes (12): DashboardController, Collection, ProjectManagementController, TaskManagementController, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Project (+4 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (27): concurrently, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js, frappe-gantt (+19 more)

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

### Community 86 - "Role"
Cohesion: 0.14
Nodes (8): AccessPermission, Role, RolePolicy, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 87 - "User"
Cohesion: 0.12
Nodes (7): User, AuditLogPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "AuditLog"
Cohesion: 0.06
Nodes (13): AuditLog, Comment, AuditEventDatabaseNotification, AuditEventMailNotification, CommentObserver, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+5 more)

### Community 101 - "StoreUserRequest"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 105 - "Illuminate\Http\Request"
Cohesion: 0.22
Nodes (5): CommentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 106 - "Organization"
Cohesion: 0.17
Nodes (3): AnalyticsController, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 107 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 114 - "Illuminate\Database\Seeder"
Cohesion: 0.22
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

## Knowledge Gaps
- **102 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+97 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\View\View`, `Illuminate\Http\RedirectResponse`, `LoginRequest`, `Task`, `Project`, `Organization`, `.boardOrganizationIds`, `web.php`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `NotificationSettingPolicy`, `Role`, `DepartmentPolicy`, `UserManagementController.php`, `AuditLog`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Illuminate\View\View`, `Illuminate\Http\RedirectResponse`, `Department`, `StoreUserRequest`, `Project`, `.boardOrganizationIds`, `web.php`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Seeder`, `.store`, `Role`, `User`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `Project`, `Illuminate\Http\Request`, `Organization`, `web.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.store`, `AuditLog`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 11 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _102 weakly-connected nodes found - possible documentation gaps or missing edges._