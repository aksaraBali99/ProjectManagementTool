# Graph Report - ProjectManagementTool  (2026-08-20)

## Corpus Check
- 174 files · ~39,313 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 687 nodes · 1333 edges · 95 communities (79 shown, 16 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 74 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7ac15602`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- Closure
- package.json
- User
- Comment
- LARAVEL_README.md
- AppServiceProvider
- Illuminate\Foundation\Http\FormRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- CLAUDE.md
- copilot-instructions.md
- OrgMember
- Illuminate\View\View
- Organization
- ProjectManagementTool
- Task
- projects/create.blade.php
- SubtaskPolicy
- users/create.blade.php
- users/edit.blade.php
- projects/edit.blade.php
- UserFactory
- DocumentPolicy
- Priority.php
- .orgMemberships
- DepartmentPolicy
- tasks/edit.blade.php
- tasks/index.blade.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- OrganizationPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Organization` - 48 edges
3. `OrgMember` - 33 edges
4. `Role` - 32 edges
5. `Task` - 32 edges
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
- `makeStaffWithDepartmentAccess()` --references--> `Department`  [EXTRACTED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Department.php
- `joinOrg()` --references--> `Organization`  [EXTRACTED]
  tests/Feature/RoleBasedAccessTest.php → app/Models/Organization.php

## Import Cycles
- None detected.

## Communities (95 total, 16 thin omitted)

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
Cohesion: 0.15
Nodes (6): User, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (10): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserPasswordRequest, UpdateUserRequest (+2 more)

### Community 16 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (13): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, OrganizationManagementController, PermissionManagementController, RoleManagementController, UserManagementController (+5 more)

### Community 39 - "OrgMember"
Cohesion: 0.06
Nodes (18): AccessPermission, AuditLog, organization(), NotificationSetting, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+10 more)

### Community 42 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (8): DashboardController, ProjectManagementController, SettingsController, TaskManagementController, Project, ProjectPolicy, Illuminate\Support\Collection, Illuminate\View\View

### Community 44 - "Organization"
Cohesion: 0.10
Nodes (5): DepartmentManagementController, StoreDepartmentRequest, Department, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 54 - "Task"
Cohesion: 0.12
Nodes (10): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, Illuminate\Database\Eloquent\SoftDeletes (+2 more)

### Community 67 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 68 - "users/edit.blade.php"
Cohesion: 0.50
Nodes (3): users._inline-validation, users._phone-input, users._unsaved-changes-guard

### Community 72 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 74 - "Priority.php"
Cohesion: 0.06
Nodes (6): StoreProjectRequest, UpdateProjectRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrganizationPolicy`, `Comment`, `OrgMember`, `DocumentPolicy`, `Illuminate\View\View`, `Organization`, `.orgMemberships`, `DepartmentPolicy`, `Illuminate\Http\RedirectResponse`, `DocumentAccessLevel.php`, `SubtaskPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrganizationPolicy`, `OrgMember`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\View\View`, `.orgMemberships`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Role` connect `Illuminate\Http\RedirectResponse` to `Illuminate\View\View`, `Illuminate\Foundation\Http\FormRequest`, `Priority.php`, `OrgMember`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._