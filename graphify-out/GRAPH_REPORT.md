# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 211 files · ~57,738 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 844 nodes · 1834 edges · 122 communities (97 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 110 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3cdbcdef`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Illuminate\Http\RedirectResponse
- Illuminate\Http\Request
- LoginRequest
- Illuminate\View\View
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
- _password-input.blade.php
- TaskManagementController
- User
- Organization
- Controller
- UpdateProjectRequest
- Illuminate\Foundation\Http\FormRequest
- StoreProjectRequest
- AuditLog
- Role
- Subtask
- Document
- StoreUserRequest
- UpdateDepartmentRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- StoreTaskRequest
- Comment
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateUserRequest
- CommentPolicy
- DepartmentPolicy
- UserPolicy
- UpdateRoleRequest
- RolePolicy
- Illuminate\Database\Eloquent\Builder
- AuditLogPolicy.php

## God Nodes (most connected - your core abstractions)
1. `User` - 133 edges
2. `Organization` - 74 edges
3. `Task` - 48 edges
4. `OrgMember` - 46 edges
5. `Role` - 40 edges
6. `Project` - 35 edges
7. `Department` - 30 edges
8. `AuditLog` - 29 edges
9. `Controller` - 23 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffOnKanban()` --calls--> `Role`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (122 total, 25 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.07
Nodes (15): OrgMember, Permission, DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Seeder (+7 more)

### Community 1 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.21
Nodes (3): AccessControlController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 2 - "Illuminate\Http\Request"
Cohesion: 0.25
Nodes (4): CommentController, SubtaskController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 3 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 4 - "Illuminate\View\View"
Cohesion: 0.19
Nodes (5): NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 5 - "Task"
Cohesion: 0.16
Nodes (5): KanbanController, Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Project"
Cohesion: 0.24
Nodes (3): ProjectManagementController, Project, Illuminate\Support\Collection

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

### Community 87 - "User"
Cohesion: 0.14
Nodes (6): User, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "Organization"
Cohesion: 0.07
Nodes (14): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, Organization, DepartmentSeeder, UserSeeder, Illuminate\Contracts\Validation\Validator (+6 more)

### Community 89 - "Controller"
Cohesion: 0.18
Nodes (6): AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, Collection, PermissionManagementController

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditTrailController, AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType (+6 more)

### Community 101 - "Role"
Cohesion: 0.24
Nodes (4): UserManagementController, Role, makeClientForDocumentList(), makeClientForDocuments()

### Community 106 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 107 - "Document"
Cohesion: 0.19
Nodes (4): DocumentController, TaskDocumentController, Document, DocumentPolicy

### Community 108 - "StoreUserRequest"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 111 - "StoreTaskRequest"
Cohesion: 0.17
Nodes (3): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `LoginRequest`, `Task`, `Project`, `.boardOrganizationIds`, `Organization`, `Controller`, `AuditLog`, `Role`, `Subtask`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `DepartmentPolicy`, `UserPolicy`, `RolePolicy`, `Illuminate\Database\Eloquent\Builder`, `AuditLogPolicy.php`?**
  _High betweenness centrality (0.146) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `Illuminate\Http\Request`, `Illuminate\View\View`, `Task`, `Role`, `Project`, `DocumentController.php`, `Document`, `StoreUserRequest`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `User`, `Controller`, `Illuminate\Foundation\Http\FormRequest`, `AuditLog`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `Illuminate\Http\Request`, `Subtask`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `Illuminate\Database\Eloquent\Builder`, `Controller`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 10 inferred relationships involving `User` (e.g. with `.index()` and `.index()`) actually correct?**
  _`User` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Organization` (e.g. with `.index()` and `.create()`) actually correct?**
  _`Organization` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._