# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 180 files · ~46,514 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 719 nodes · 1474 edges · 102 communities (84 shown, 18 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 83 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f92a5def`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Project
- Illuminate\Database\Eloquent\Relations\BelongsTo
- LoginRequest
- .boardOrganizationIds
- Illuminate\Foundation\Http\FormRequest
- composer.json
- Organization
- scripts
- package.json
- Mermaid AI Skills
- Role
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
- Illuminate\Http\RedirectResponse
- User
- Task
- Subtask
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateUserRequest
- UserManagementController.php
- Comment
- Illuminate\Http\Request
- Document.php
- TaskPolicy
- Controller
- DepartmentPolicy
- DocumentPolicy.php
- UpdateProjectRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 105 edges
2. `Organization` - 59 edges
3. `Task` - 41 edges
4. `Role` - 34 edges
5. `OrgMember` - 33 edges
6. `Project` - 31 edges
7. `Department` - 30 edges
8. `Controller` - 20 edges
9. `Subtask` - 16 edges
10. `Document` - 13 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffOnKanban()` --calls--> `Role`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/Role.php
- `makeClientOnProject()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffWithDepartmentAccess()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (102 total, 18 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.07
Nodes (14): OrgMember, Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder (+6 more)

### Community 1 - "Project"
Cohesion: 0.14
Nodes (5): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Project, makeClientOnProject()

### Community 2 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (4): AuditLog, organization(), NotificationSetting, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 5 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, StoreProjectRequest, Illuminate\Foundation\Http\FormRequest

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Organization"
Cohesion: 0.06
Nodes (16): AccessControlController, DashboardController, Collection, DepartmentManagementController, KanbanController, ProjectManagementController, StoreDepartmentRequest, AccessPermission (+8 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Role"
Cohesion: 0.15
Nodes (4): RoleManagementController, UpdateRoleRequest, Role, RolePolicy

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

### Community 86 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.28
Nodes (3): GoogleAuthController, OrganizationManagementController, Illuminate\Http\RedirectResponse

### Community 87 - "User"
Cohesion: 0.12
Nodes (7): User, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "Task"
Cohesion: 0.27
Nodes (3): TaskManagementController, Task, Illuminate\Database\Eloquent\SoftDeletes

### Community 89 - "Subtask"
Cohesion: 0.27
Nodes (3): SubtaskController, Subtask, SubtaskPolicy

### Community 91 - "UpdateUserRequest"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, Illuminate\Validation\Validator

### Community 94 - "Illuminate\Http\Request"
Cohesion: 0.27
Nodes (5): CommentController, TaskDocumentController, Document, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 97 - "Controller"
Cohesion: 0.21
Nodes (4): AuthenticatedSessionController, Controller, PermissionManagementController, SettingsController

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **18 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Project`, `TaskPolicy`, `LoginRequest`, `.boardOrganizationIds`, `DepartmentPolicy`, `DocumentPolicy.php`, `Organization`, `Role`, `Illuminate\Http\RedirectResponse`, `Task`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserManagementController.php`, `Comment`?**
  _High betweenness centrality (0.121) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Project`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `User`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UpdateUserRequest`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `TaskPolicy`, `Organization`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._