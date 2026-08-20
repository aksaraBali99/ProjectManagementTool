# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 171 files · ~37,396 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 674 nodes · 1289 edges · 87 communities (77 shown, 10 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 69 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `16f6b158`
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
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserFactory
- ProjectManagementTool
- Organization
- Task
- projects/create.blade.php
- EnsureBelongsToOrganization.php
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- tasks/edit.blade.php
- tasks/index.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 87 edges
2. `Organization` - 46 edges
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

## Communities (87 total, 10 thin omitted)

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
Nodes (12): Comment, User, CommentPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, TaskPolicy (+4 more)

### Community 6 - "OrgMember"
Cohesion: 0.05
Nodes (13): StoreTaskRequest, UpdateTaskRequest, AccessPermission, AuditLog, organization(), NotificationSetting, OrgMember, Illuminate\Contracts\Validation\Validator (+5 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (10): UpdateDepartmentRequest, StoreProjectRequest, UpdateProjectRequest, UpdateRoleRequest, validateCompanyRoles(), StoreUserRequest, UpdateUserPasswordRequest, UpdateUserRequest (+2 more)

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (17): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, PermissionManagementController, ProjectManagementController, RoleManagementController (+9 more)

### Community 39 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.14
Nodes (3): Permission, PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 44 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 53 - "Organization"
Cohesion: 0.05
Nodes (14): DepartmentManagementController, OrganizationManagementController, StoreDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, Department, Organization, DatabaseSeeder (+6 more)

### Community 54 - "Task"
Cohesion: 0.14
Nodes (11): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, SubtaskPolicy (+3 more)

### Community 56 - "EnsureBelongsToOrganization.php"
Cohesion: 0.26
Nodes (6): EnsureBelongsToOrganization, EnsureUserIsActive, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **10 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LoginRequest`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Organization`, `Task`?**
  _High betweenness centrality (0.107) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `User`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `User`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\RedirectResponse`, `Organization`?**
  _High betweenness centrality (0.025) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._