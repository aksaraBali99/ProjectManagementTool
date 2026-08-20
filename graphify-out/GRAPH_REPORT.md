# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 173 files · ~39,099 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 686 nodes · 1333 edges · 93 communities (77 shown, 16 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 74 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `625342e2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Closure
- package.json
- User
- CommentPolicy
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- OrgMember
- ProjectPolicy
- Organization
- ProjectManagementTool
- SubtaskPolicy
- Task
- projects/create.blade.php
- Illuminate\View\View
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- UserFactory
- UserPolicy
- .orgMemberships
- tasks/edit.blade.php
- tasks/index.blade.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- OrganizationPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Organization` - 48 edges
3. `OrgMember` - 37 edges
4. `Role` - 32 edges
5. `Task` - 32 edges
6. `Project` - 27 edges
7. `Department` - 25 edges
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
- `makeClientOnProject()` --calls--> `OrgMember`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/OrgMember.php
- `makeStaffWithDepartmentAccess()` --calls--> `OrgMember`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/OrgMember.php

## Import Cycles
- None detected.

## Communities (93 total, 16 thin omitted)

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
Cohesion: 0.16
Nodes (6): User, DepartmentPolicy, DocumentPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.07
Nodes (12): LoginRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest (+4 more)

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.07
Nodes (13): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, PermissionManagementController, RoleManagementController, UserManagementController (+5 more)

### Community 39 - "OrgMember"
Cohesion: 0.07
Nodes (7): StoreTaskRequest, UpdateTaskRequest, OrgMember, Illuminate\Contracts\Validation\Validator, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Model, joinOrg()

### Community 44 - "Organization"
Cohesion: 0.06
Nodes (13): DepartmentManagementController, OrganizationManagementController, StoreDepartmentRequest, Department, Organization, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+5 more)

### Community 54 - "Task"
Cohesion: 0.06
Nodes (16): CommentController, SubtaskController, TaskDocumentController, AccessPermission, AuditLog, Comment, organization(), Document (+8 more)

### Community 56 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (9): DashboardController, ProjectManagementController, SettingsController, TaskManagementController, StoreProjectRequest, UpdateProjectRequest, Project, Illuminate\Support\Collection (+1 more)

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
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrganizationPolicy`, `CommentPolicy`, `OrgMember`, `UserPolicy`, `Illuminate\Foundation\Http\FormRequest`, `ProjectPolicy`, `Organization`, `.orgMemberships`, `Illuminate\Http\RedirectResponse`, `SubtaskPolicy`, `Task`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrganizationPolicy`, `OrgMember`, `Illuminate\Foundation\Http\FormRequest`, `.orgMemberships`, `Illuminate\Http\RedirectResponse`, `Task`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Role` connect `Illuminate\Http\RedirectResponse` to `OrgMember`, `Illuminate\Foundation\Http\FormRequest`, `Organization`, `Task`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._