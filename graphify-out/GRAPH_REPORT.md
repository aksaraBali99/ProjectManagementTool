# Graph Report - ProjectManagementTool  (2026-08-18)

## Corpus Check
- 131 files · ~19,833 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 518 nodes · 863 edges · 77 communities (66 shown, 11 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 40 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9b62587d`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Illuminate\Database\Seeder
- package.json
- User
- Illuminate\Database\Eloquent\Relations\BelongsTo
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- ProjectManagementTool
- Organization
- EnsureBelongsToOrganization.php
- Comment
- Department
- Project
- Task
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- OrganizationPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 74 edges
2. `Organization` - 34 edges
3. `OrgMember` - 21 edges
4. `Department` - 19 edges
5. `Role` - 17 edges
6. `Task` - 17 edges
7. `Controller` - 12 edges
8. `Project` - 12 edges
9. `UserManagementController` - 10 edges
10. `require-dev` - 10 edges

## Surprising Connections (you probably didn't know these)
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php
- `joinOrg()` --references--> `Role`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Role.php
- `joinOrg()` --references--> `User`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/User.php
- `joinOrg()` --references--> `OrgMember`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/OrgMember.php
- `AccessControlController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/AccessControlController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (77 total, 11 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.05
Nodes (43): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+35 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "package.json"
Cohesion: 0.10
Nodes (20): concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite (+12 more)

### Community 5 - "User"
Cohesion: 0.12
Nodes (7): User, RolePolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 6 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (11): AccessPermission, AuditLog, organization(), Document, NotificationSetting, OrgMember, Subtask, UserSeeder (+3 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (11): LoginRequest, StoreDepartmentRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, StoreUserRequest, UpdateUserPasswordRequest (+3 more)

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.09
Nodes (13): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, DepartmentManagementController, OrganizationManagementController, RoleManagementController (+5 more)

### Community 54 - "EnsureBelongsToOrganization.php"
Cohesion: 0.25
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 56 - "Department"
Cohesion: 0.13
Nodes (7): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), Department, DepartmentPolicy, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.orgMemberships`, `Illuminate\Foundation\Http\FormRequest`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`, `Organization`, `Comment`, `Department`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.orgMemberships`, `Illuminate\Foundation\Http\FormRequest`, `OrganizationPolicy`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Why does `Department` connect `Department` to `Illuminate\Http\RedirectResponse`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.018) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._