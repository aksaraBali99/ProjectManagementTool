# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 185 files · ~49,576 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 735 nodes · 1558 edges · 100 communities (82 shown, 18 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 91 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `73704f06`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- StoreTaskRequest
- Task
- LoginRequest
- .boardOrganizationIds
- Illuminate\Foundation\Http\FormRequest
- composer.json
- Department
- scripts
- package.json
- Mermaid AI Skills
- ProjectStatus.php
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
- Role
- User
- Organization
- StoreDepartmentRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateUserRequest
- UserPolicy
- Comment
- UpdateProjectRequest
- RolePolicy
- Document

## God Nodes (most connected - your core abstractions)
1. `User` - 117 edges
2. `Organization` - 73 edges
3. `Task` - 42 edges
4. `OrgMember` - 41 edges
5. `Role` - 39 edges
6. `Project` - 35 edges
7. `Department` - 30 edges
8. `Controller` - 20 edges
9. `Document` - 18 edges
10. `Subtask` - 16 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (100 total, 18 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.06
Nodes (22): AccessPermission, organization(), NotificationSetting, OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder (+14 more)

### Community 1 - "StoreTaskRequest"
Cohesion: 0.18
Nodes (3): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 2 - "Task"
Cohesion: 0.06
Nodes (14): CommentController, DashboardController, Collection, SubtaskController, TaskDocumentController, TaskManagementController, AuditLog, Subtask (+6 more)

### Community 3 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 5 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (23): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, intl-tel-input, devDependencies, concurrently (+15 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

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

### Community 86 - "Role"
Cohesion: 0.09
Nodes (10): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, PermissionManagementController, RoleManagementController, UserManagementController, Permission (+2 more)

### Community 87 - "User"
Cohesion: 0.11
Nodes (7): User, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "Organization"
Cohesion: 0.07
Nodes (13): DocumentController, KanbanController, OrganizationManagementController, ProjectManagementController, SettingsController, Organization, Project, Illuminate\Database\Eloquent\Relations\HasMany (+5 more)

### Community 91 - "UpdateUserRequest"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, Illuminate\Validation\Validator

### Community 99 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **18 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Task`, `LoginRequest`, `.boardOrganizationIds`, `Document`, `Role`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `Comment`, `RolePolicy`?**
  _High betweenness centrality (0.125) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Task`, `Document`, `.boardOrganizationIds`, `Department`, `Role`, `User`, `StoreDepartmentRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UpdateUserRequest`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Role` connect `Role` to `OrgMember`, `ProjectStatus.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UpdateUserRequest`, `UpdateProjectRequest`, `RolePolicy`?**
  _High betweenness centrality (0.026) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.index()` and `.callback()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Organization` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`Organization` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._