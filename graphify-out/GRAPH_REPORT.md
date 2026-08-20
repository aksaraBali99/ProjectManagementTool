# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 173 files · ~38,473 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 683 nodes · 1314 edges · 98 communities (78 shown, 20 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 70 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2fba0716`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- LoginRequest
- package.json
- User
- OrgMember
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- Role
- .orgMemberships
- HidesInactiveFromNonAdmins.php
- ProjectManagementTool
- Organization
- Task
- projects/create.blade.php
- ProjectStatus.php
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- StoreUserRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateUserRequest
- CommentPolicy
- OrganizationPolicy
- UpdateProjectRequest
- tasks/edit.blade.php
- tasks/index.blade.php
- UpdateDepartmentRequest
- UpdateOrganizationRequest
- StoreTaskRequest
- DocumentPolicy.php

## God Nodes (most connected - your core abstractions)
1. `User` - 91 edges
2. `Organization` - 46 edges
3. `OrgMember` - 37 edges
4. `Task` - 32 edges
5. `Role` - 28 edges
6. `Department` - 25 edges
7. `Project` - 25 edges
8. `Controller` - 19 edges
9. `Subtask` - 14 edges
10. `Document` - 13 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffWithDepartmentAccess()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --references--> `Department`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Department.php
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php
- `makeStaffWithDepartmentAccess()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Organization.php

## Import Cycles
- None detected.

## Communities (98 total, 20 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "LoginRequest"
Cohesion: 0.14
Nodes (8): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 4 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 5 - "User"
Cohesion: 0.16
Nodes (6): User, DepartmentPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 6 - "OrgMember"
Cohesion: 0.05
Nodes (19): UpdateTaskRequest, AccessPermission, AuditLog, Comment, organization(), NotificationSetting, OrgMember, DatabaseSeeder (+11 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): StoreOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.05
Nodes (18): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, DocumentController, OrganizationManagementController, PermissionManagementController (+10 more)

### Community 39 - "Role"
Cohesion: 0.20
Nodes (3): UserManagementController, Role, RolePolicy

### Community 44 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.22
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 53 - "Organization"
Cohesion: 0.11
Nodes (5): DepartmentManagementController, StoreDepartmentRequest, Department, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 54 - "Task"
Cohesion: 0.09
Nodes (10): CommentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, SubtaskPolicy, TaskPolicy (+2 more)

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 72 - "StoreUserRequest"
Cohesion: 0.29
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **20 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentPolicy.php`, `LoginRequest`, `OrgMember`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.orgMemberships`, `CommentPolicy`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`, `Organization`, `Task`?**
  _High betweenness centrality (0.110) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Role`, `StoreUserRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.orgMemberships`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.042) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`?**
  _High betweenness centrality (0.024) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._