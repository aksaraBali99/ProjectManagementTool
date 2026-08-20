# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 170 files · ~36,906 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 672 nodes · 1283 edges · 83 communities (74 shown, 9 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 70 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `be04c1fc`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
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
- ProjectManagementTool
- Organization
- Task
- projects/create.blade.php
- LoginRequest
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- tasks/edit.blade.php
- tasks/index.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 87 edges
2. `Organization` - 47 edges
3. `OrgMember` - 35 edges
4. `Task` - 32 edges
5. `Role` - 28 edges
6. `Department` - 25 edges
7. `Project` - 24 edges
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

## Communities (83 total, 9 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 4 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 5 - "User"
Cohesion: 0.05
Nodes (12): Comment, Permission, User, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy (+4 more)

### Community 6 - "OrgMember"
Cohesion: 0.06
Nodes (12): StoreTaskRequest, UpdateTaskRequest, AccessPermission, AuditLog, organization(), NotificationSetting, OrgMember, Illuminate\Contracts\Validation\Validator (+4 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (12): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, StoreProjectRequest, UpdateProjectRequest, StoreUserRequest, UpdateUserPasswordRequest, UpdateUserRequest (+4 more)

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (19): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, OrganizationManagementController, PermissionManagementController, ProjectManagementController (+11 more)

### Community 53 - "Organization"
Cohesion: 0.07
Nodes (12): DepartmentManagementController, StoreDepartmentRequest, Department, Organization, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder (+4 more)

### Community 54 - "Task"
Cohesion: 0.07
Nodes (13): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, DocumentPolicy (+5 more)

### Community 56 - "LoginRequest"
Cohesion: 0.10
Nodes (11): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), Closure, UserFactory, Illuminate\Database\Eloquent\Builder (+3 more)

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **9 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `Organization`, `Task`, `LoginRequest`?**
  _High betweenness centrality (0.106) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Http\RedirectResponse`, `Illuminate\Foundation\Http\FormRequest`, `User`, `OrgMember`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\RedirectResponse`, `User`, `OrgMember`?**
  _High betweenness centrality (0.025) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._