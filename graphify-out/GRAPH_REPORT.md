# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 180 files · ~46,908 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 720 nodes · 1477 edges · 100 communities (79 shown, 21 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 83 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `ba0e4791`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Project
- Task
- Closure
- .boardOrganizationIds
- OrganizationPolicy
- composer.json
- Department
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\View\View
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
- Organization
- Illuminate\Support\Collection
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Foundation\Http\FormRequest
- UserFactory
- CommentPolicy
- TaskPolicy
- TaskManagementController
- DepartmentPolicy
- .myTaskSection
- DocumentPolicy.php

## God Nodes (most connected - your core abstractions)
1. `User` - 106 edges
2. `Organization` - 59 edges
3. `Task` - 42 edges
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

## Communities (100 total, 21 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.06
Nodes (16): AccessPermission, OrgMember, Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder (+8 more)

### Community 1 - "Project"
Cohesion: 0.14
Nodes (7): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Project, Illuminate\Contracts\Validation\Validator, makeTaskOnDashboard(), makeClientOnProject()

### Community 2 - "Task"
Cohesion: 0.06
Nodes (16): CommentController, DocumentController, SubtaskController, TaskDocumentController, AuditLog, Comment, organization(), Document (+8 more)

### Community 3 - "Closure"
Cohesion: 0.22
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Department"
Cohesion: 0.18
Nodes (3): DepartmentManagementController, StoreDepartmentRequest, Department

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

### Community 86 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (14): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, OrganizationManagementController, PermissionManagementController, RoleManagementController, SettingsController (+6 more)

### Community 87 - "User"
Cohesion: 0.13
Nodes (6): User, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.07
Nodes (12): LoginRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest (+4 more)

### Community 92 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **21 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `.myTaskSection`, `Task`, `DepartmentPolicy`, `.boardOrganizationIds`, `DocumentPolicy.php`, `OrganizationPolicy`, `Project`, `Illuminate\View\View`, `Illuminate\Http\RedirectResponse`, `Organization`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Foundation\Http\FormRequest`, `CommentPolicy`, `TaskPolicy`?**
  _High betweenness centrality (0.122) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `.myTaskSection`, `Project`, `.boardOrganizationIds`, `OrganizationPolicy`, `Department`, `Illuminate\View\View`, `Illuminate\Http\RedirectResponse`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Foundation\Http\FormRequest`, `TaskManagementController`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `.myTaskSection`, `Project`, `Illuminate\View\View`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskPolicy`, `TaskManagementController`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._