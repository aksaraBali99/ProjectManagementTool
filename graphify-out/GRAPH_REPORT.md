# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 174 files · ~39,467 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 687 nodes · 1327 edges · 92 communities (78 shown, 14 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 74 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6c8ec580`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Closure
- package.json
- User
- Role
- LARAVEL_README.md
- AppServiceProvider
- StoreUserRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- OrgMember
- UpdateUserRequest
- Organization
- ProjectManagementTool
- Illuminate\Foundation\Http\FormRequest
- Task
- projects/create.blade.php
- UpdateRoleRequest
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- UserFactory
- UpdateProjectRequest
- LoginRequest
- tasks/edit.blade.php
- tasks/index.blade.php
- StoreProjectRequest

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
- `makeStaffWithDepartmentAccess()` --calls--> `OrgMember`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/OrgMember.php
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

### Community 3 - "Closure"
Cohesion: 0.22
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 4 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 5 - "User"
Cohesion: 0.06
Nodes (11): Permission, User, CommentPolicy, DepartmentPolicy, OrganizationPolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+3 more)

### Community 6 - "Role"
Cohesion: 0.13
Nodes (3): RoleManagementController, Role, RolePolicy

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "StoreUserRequest"
Cohesion: 0.29
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (16): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, OrganizationManagementController, PermissionManagementController, ProjectManagementController (+8 more)

### Community 39 - "OrgMember"
Cohesion: 0.05
Nodes (17): AuditLog, Comment, organization(), NotificationSetting, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+9 more)

### Community 44 - "Organization"
Cohesion: 0.06
Nodes (11): DepartmentManagementController, StoreDepartmentRequest, UpdateDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, AccessPermission, Department, Organization (+3 more)

### Community 53 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 54 - "Task"
Cohesion: 0.07
Nodes (13): CommentController, DocumentController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Document, Subtask, Task (+5 more)

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 72 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role`, `OrgMember`, `Organization`, `LoginRequest`, `Illuminate\Http\RedirectResponse`, `Task`?**
  _High betweenness centrality (0.112) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `User`, `Role`, `OrgMember`, `StoreUserRequest`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Role` connect `Role` to `OrgMember`, `UpdateProjectRequest`, `StoreUserRequest`, `UpdateUserRequest`, `Organization`, `Illuminate\Http\RedirectResponse`, `StoreProjectRequest`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 17 inferred relationships involving `Role` (e.g. with `.toggle()` and `.rolesInDisplayOrder()`) actually correct?**
  _`Role` has 17 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._