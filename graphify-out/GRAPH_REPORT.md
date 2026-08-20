# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 174 files · ~40,143 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 690 nodes · 1336 edges · 92 communities (78 shown, 14 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 74 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2e0951b8`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- TaskManagementController.php
- package.json
- User
- LoginRequest
- LARAVEL_README.md
- AppServiceProvider
- .orgMemberships
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- OrgMember
- OrganizationPolicy
- Organization
- ProjectManagementTool
- Role
- Task
- projects/create.blade.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- HidesInactiveFromNonAdmins.php
- Comment
- TaskPolicy
- UserPolicy
- tasks/edit.blade.php
- tasks/index.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Organization` - 48 edges
3. `Role` - 32 edges
4. `Task` - 32 edges
5. `OrgMember` - 29 edges
6. `Project` - 29 edges
7. `Department` - 25 edges
8. `Controller` - 19 edges
9. `Subtask` - 14 edges
10. `Document` - 13 edges

## Surprising Connections (you probably didn't know these)
- `makeClientOnProject()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffWithDepartmentAccess()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --references--> `Department`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Department.php
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php

## Import Cycles
- None detected.

## Communities (92 total, 14 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "TaskManagementController.php"
Cohesion: 0.10
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 4 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 5 - "User"
Cohesion: 0.16
Nodes (6): User, ProjectPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 6 - "LoginRequest"
Cohesion: 0.14
Nodes (8): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (17): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, OrganizationManagementController, PermissionManagementController, ProjectManagementController (+9 more)

### Community 39 - "OrgMember"
Cohesion: 0.06
Nodes (17): AccessPermission, AuditLog, organization(), NotificationSetting, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+9 more)

### Community 44 - "Organization"
Cohesion: 0.10
Nodes (5): DepartmentManagementController, Department, Organization, DepartmentPolicy, Illuminate\Database\Eloquent\Relations\HasMany

### Community 53 - "Role"
Cohesion: 0.05
Nodes (15): UserManagementController, StoreDepartmentRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateProjectRequest, UpdateRoleRequest, validateCompanyRoles() (+7 more)

### Community 54 - "Task"
Cohesion: 0.08
Nodes (12): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, DocumentPolicy (+4 more)

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 72 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.22
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 83 - "tasks/edit.blade.php"
Cohesion: 0.50
Nodes (3): tasks._comments, tasks._subtasks, tasks._documents

## Knowledge Gaps
- **96 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+91 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LoginRequest`, `OrgMember`, `Comment`, `.orgMemberships`, `OrganizationPolicy`, `Organization`, `TaskPolicy`, `UserPolicy`, `Illuminate\Http\RedirectResponse`, `Role`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.112) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `TaskManagementController.php`, `OrgMember`, `.orgMemberships`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Role` connect `Role` to `User`, `OrgMember`, `HidesInactiveFromNonAdmins.php`, `.orgMemberships`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 17 inferred relationships involving `Role` (e.g. with `.toggle()` and `.rolesInDisplayOrder()`) actually correct?**
  _`Role` has 17 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _96 weakly-connected nodes found - possible documentation gaps or missing edges._