# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 166 files · ~36,607 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 657 nodes · 1242 edges · 94 communities (80 shown, 14 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 66 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `493176ac`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Organization
- package.json
- User
- OrgMember
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Permission
- CLAUDE.md
- copilot-instructions.md
- ProjectManagementTool
- Illuminate\Http\RedirectResponse
- Task
- projects/create.blade.php
- UserFactory
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- Role
- CommentPolicy
- Illuminate\Database\Eloquent\Relations\BelongsTo
- UserPolicy
- OrganizationPolicy
- tasks/edit.blade.php
- tasks/index.blade.php
- ProjectPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 87 edges
2. `Organization` - 47 edges
3. `OrgMember` - 35 edges
4. `Task` - 32 edges
5. `Department` - 25 edges
6. `Project` - 24 edges
7. `Role` - 23 edges
8. `Controller` - 19 edges
9. `Subtask` - 14 edges
10. `Document` - 12 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffWithDepartmentAccess()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `OrgMember`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/OrgMember.php
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php
- `joinOrg()` --references--> `Role`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (94 total, 14 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "Organization"
Cohesion: 0.09
Nodes (7): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffWithDepartmentAccess()

### Community 4 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 5 - "User"
Cohesion: 0.15
Nodes (6): User, DepartmentPolicy, DocumentPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 6 - "OrgMember"
Cohesion: 0.08
Nodes (13): StoreTaskRequest, UpdateTaskRequest, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder (+5 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (13): LoginRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateProjectRequest, UpdateRoleRequest, StoreUserRequest, UpdateUserPasswordRequest (+5 more)

### Community 53 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (17): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, DocumentController, OrganizationManagementController, ProjectManagementController (+9 more)

### Community 54 - "Task"
Cohesion: 0.08
Nodes (15): CommentController, SubtaskController, TaskDocumentController, EnsureBelongsToOrganization, EnsureUserIsActive, Document, Subtask, Task (+7 more)

### Community 56 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 73 - "Role"
Cohesion: 0.15
Nodes (3): Role, RolePolicy, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 77 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (5): AuditLog, Comment, organization(), NotificationSetting, Illuminate\Database\Eloquent\Relations\BelongsTo

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `OrgMember`, `.orgMemberships`, `Role`, `Illuminate\Foundation\Http\FormRequest`, `CommentPolicy`, `UserPolicy`, `OrganizationPolicy`, `Permission`, `Illuminate\Http\RedirectResponse`, `Task`, `ProjectPolicy`?**
  _High betweenness centrality (0.105) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `.orgMemberships`, `Role`, `Illuminate\Foundation\Http\FormRequest`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `OrgMember`?**
  _High betweenness centrality (0.024) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._