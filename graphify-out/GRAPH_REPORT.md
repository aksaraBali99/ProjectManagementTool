# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 175 files · ~41,959 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 698 nodes · 1362 edges · 86 communities (77 shown, 9 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 77 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6d15a277`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Illuminate\Http\RedirectResponse
- User
- Priority.php
- Task
- Illuminate\Foundation\Http\FormRequest
- composer.json
- Organization
- scripts
- package.json
- Mermaid AI Skills
- UserFactory
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

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Organization` - 49 edges
3. `Task` - 35 edges
4. `Role` - 32 edges
5. `OrgMember` - 29 edges
6. `Project` - 29 edges
7. `Department` - 25 edges
8. `Controller` - 19 edges
9. `Subtask` - 16 edges
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

## Communities (86 total, 9 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.06
Nodes (19): AccessPermission, AuditLog, organization(), NotificationSetting, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+11 more)

### Community 1 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (17): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, OrganizationManagementController, PermissionManagementController, ProjectManagementController (+9 more)

### Community 2 - "User"
Cohesion: 0.06
Nodes (11): Permission, User, CommentPolicy, DepartmentPolicy, DocumentPolicy, ProjectPolicy, RolePolicy, UserPolicy (+3 more)

### Community 3 - "Priority.php"
Cohesion: 0.05
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, StoreProjectRequest, UpdateProjectRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, ValidClientUser (+5 more)

### Community 4 - "Task"
Cohesion: 0.08
Nodes (13): CommentController, DocumentController, SubtaskController, TaskDocumentController, Comment, Document, Subtask, Task (+5 more)

### Community 5 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.07
Nodes (12): LoginRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest (+4 more)

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Organization"
Cohesion: 0.08
Nodes (6): DepartmentManagementController, StoreDepartmentRequest, Department, Organization, OrganizationPolicy, Illuminate\Database\Eloquent\Relations\HasMany

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

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

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **9 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `Task`, `Illuminate\Foundation\Http\FormRequest`, `Organization`?**
  _High betweenness centrality (0.110) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Illuminate\Http\RedirectResponse`, `User`, `Illuminate\Foundation\Http\FormRequest`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Why does `Role` connect `Illuminate\Http\RedirectResponse` to `OrgMember`, `User`, `Priority.php`, `Illuminate\Foundation\Http\FormRequest`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `OrgMember` be split into smaller, more focused modules?**
  _Cohesion score 0.059917920656634746 - nodes in this community are weakly interconnected._