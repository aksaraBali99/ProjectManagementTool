# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 185 files · ~50,481 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 816 nodes · 1777 edges · 107 communities (90 shown, 17 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 110 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `11077f40`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Illuminate\View\View
- Task
- LoginRequest
- Role
- Illuminate\Http\RedirectResponse
- composer.json
- Comment
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
- _password-input.blade.php
- Controller
- User
- Organization
- Subtask
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Foundation\Http\FormRequest
- NotificationSetting
- AuditLog
- UserManagementController
- AuditEventNotifier
- OrganizationManagementController
- UpdateProjectRequest
- PermissionManagementController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 128 edges
2. `Organization` - 74 edges
3. `Task` - 48 edges
4. `OrgMember` - 45 edges
5. `Role` - 40 edges
6. `Project` - 35 edges
7. `Department` - 30 edges
8. `AuditLog` - 25 edges
9. `Controller` - 23 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (107 total, 17 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.07
Nodes (16): OrgMember, Permission, DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Seeder (+8 more)

### Community 1 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (9): DashboardController, Collection, DocumentController, KanbanController, NotificationController, ProjectManagementController, SettingsController, Illuminate\Support\Collection (+1 more)

### Community 2 - "Task"
Cohesion: 0.07
Nodes (13): CommentController, SubtaskController, TaskDocumentController, TaskManagementController, Document, Task, TaskObserver, DocumentPolicy (+5 more)

### Community 3 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 4 - "Role"
Cohesion: 0.15
Nodes (4): RoleManagementController, Role, RolePolicy, Illuminate\Database\Eloquent\Builder

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Comment"
Cohesion: 0.23
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

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

### Community 86 - "Controller"
Cohesion: 0.20
Nodes (5): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller

### Community 87 - "User"
Cohesion: 0.06
Nodes (13): Project, Collection, User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy (+5 more)

### Community 88 - "Organization"
Cohesion: 0.08
Nodes (11): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, Organization, DepartmentSeeder, UserSeeder, Illuminate\Database\Eloquent\Relations\HasMany (+3 more)

### Community 89 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (15): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, validateCompanyRoles() (+7 more)

### Community 92 - "NotificationSetting"
Cohesion: 0.24
Nodes (3): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy

### Community 93 - "AuditLog"
Cohesion: 0.17
Nodes (3): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **17 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\View\View`, `Task`, `LoginRequest`, `Role`, `UserManagementController`, `Comment`, `AuditEventNotifier`, `Controller`, `Organization`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `NotificationSetting`, `AuditLog`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Illuminate\View\View`, `Task`, `Role`, `Illuminate\Http\RedirectResponse`, `OrganizationManagementController`, `Controller`, `User`, `Illuminate\Foundation\Http\FormRequest`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Illuminate\View\View`, `Role`, `Illuminate\Http\RedirectResponse`, `Comment`, `User`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Foundation\Http\FormRequest`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Are the 10 inferred relationships involving `User` (e.g. with `.index()` and `.index()`) actually correct?**
  _`User` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Organization` (e.g. with `.index()` and `.create()`) actually correct?**
  _`Organization` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._