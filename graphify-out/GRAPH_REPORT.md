# Graph Report - ProjectManagementTool  (2026-08-18)

## Corpus Check
- 86 files · ~10,518 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 378 nodes · 521 edges · 54 communities (48 shown, 6 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 7 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6f1188ad`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\HasMany
- package.json
- User
- Illuminate\Database\Eloquent\Relations\BelongsTo
- LARAVEL_README.md
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- AppServiceProvider
- User.php
- Pest.php
- AuthenticatedSessionController.php
- CLAUDE.md
- copilot-instructions.md
- README.md
- deploy.sh

## God Nodes (most connected - your core abstractions)
1. `User` - 43 edges
2. `Organization` - 21 edges
3. `Task` - 16 edges
4. `Project` - 11 edges
5. `Role` - 11 edges
6. `OrgMember` - 10 edges
7. `require-dev` - 10 edges
8. `LoginRequest` - 9 edges
9. `Comment` - 9 edges
10. `scripts` - 9 edges

## Surprising Connections (you probably didn't know these)
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php
- `joinOrg()` --references--> `User`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/User.php
- `joinOrg()` --references--> `Role`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Role.php
- `joinOrg()` --references--> `OrgMember`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/OrgMember.php
- `AuthenticatedSessionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/AuthenticatedSessionController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (54 total, 6 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.12
Nodes (8): Department, Organization, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, UserSeeder, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Seeder

### Community 4 - "package.json"
Cohesion: 0.10
Nodes (20): concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite (+12 more)

### Community 5 - "User"
Cohesion: 0.09
Nodes (6): Comment, User, CommentPolicy, ProjectPolicy, TaskPolicy, Illuminate\Foundation\Auth\User

### Community 6 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (12): AccessPermission, AuditLog, organization(), Document, NotificationSetting, OrgMember, Subtask, Task (+4 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 8 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.13
Nodes (4): Project, Role, RoleSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 10 - "User.php"
Cohesion: 0.13
Nodes (8): LoginRequest, bootBelongsToOrganization(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Http\FormRequest, Illuminate\Notifications\Notifiable, static

### Community 16 - "AuthenticatedSessionController.php"
Cohesion: 0.15
Nodes (10): AuthenticatedSessionController, Controller, DashboardController, EnsureBelongsToOrganization, EnsureUserIsActive, Closure, Illuminate\Http\RedirectResponse, Illuminate\Http\Request (+2 more)

## Knowledge Gaps
- **79 isolated node(s):** `deploy.sh script`, `Composer\\Config::disableProcessTimeout`, `composer install`, `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump`, `Illuminate\\Foundation\\ComposerScripts::prePackageUninstall` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **6 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Why does `Organization` connect `Illuminate\Database\Eloquent\Relations\HasMany` to `User`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User.php`, `AuthenticatedSessionController.php`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Why does `LoginRequest` connect `User.php` to `AuthenticatedSessionController.php`?**
  _High betweenness centrality (0.022) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `.authenticate()` and `.run()`) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `deploy.sh script`, `Composer\\Config::disableProcessTimeout`, `composer install` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.046511627906976744 - nodes in this community are weakly interconnected._