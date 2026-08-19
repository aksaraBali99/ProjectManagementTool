# Graph Report - ProjectManagementTool  (2026-08-19)

## Corpus Check
- 138 files · ~23,358 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 546 nodes · 935 edges · 74 communities (69 shown, 5 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 43 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f865ff67`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Role
- package.json
- User
- Illuminate\Database\Eloquent\Relations\BelongsTo
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Illuminate\View\View
- CLAUDE.md
- copilot-instructions.md
- ProjectManagementTool
- Organization
- LoginRequest
- HidesInactiveFromNonAdmins.php

## God Nodes (most connected - your core abstractions)
1. `User` - 75 edges
2. `Organization` - 40 edges
3. `OrgMember` - 22 edges
4. `Department` - 19 edges
5. `Task` - 18 edges
6. `Project` - 17 edges
7. `Role` - 17 edges
8. `Controller` - 13 edges
9. `ProjectManagementController` - 11 edges
10. `UserManagementController` - 10 edges

## Surprising Connections (you probably didn't know these)
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php
- `joinOrg()` --references--> `Role`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Role.php
- `joinOrg()` --references--> `User`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/User.php
- `joinOrg()` --references--> `OrgMember`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/OrgMember.php
- `ProjectManagementController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/ProjectManagementController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (74 total, 5 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.05
Nodes (43): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+35 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "Role"
Cohesion: 0.12
Nodes (8): Role, RolePolicy, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "package.json"
Cohesion: 0.10
Nodes (20): concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite (+12 more)

### Community 5 - "User"
Cohesion: 0.06
Nodes (11): Comment, User, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, TaskPolicy, UserPolicy (+3 more)

### Community 6 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.07
Nodes (13): AccessPermission, AuditLog, organization(), Document, NotificationSetting, OrgMember, Subtask, Task (+5 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (11): StoreDepartmentRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateProjectRequest, UpdateRoleRequest, StoreUserRequest, UpdateUserPasswordRequest (+3 more)

### Community 16 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (14): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, DepartmentManagementController, OrganizationManagementController, RoleManagementController (+6 more)

### Community 53 - "Organization"
Cohesion: 0.10
Nodes (6): ProjectManagementController, StoreProjectRequest, Organization, Project, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Support\Collection

### Community 54 - "LoginRequest"
Cohesion: 0.15
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 56 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **5 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\View\View`, `Organization`, `LoginRequest`?**
  _High betweenness centrality (0.108) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Role`, `User`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `Department` connect `Illuminate\View\View` to `HidesInactiveFromNonAdmins.php`, `Role`, `User`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.017) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._