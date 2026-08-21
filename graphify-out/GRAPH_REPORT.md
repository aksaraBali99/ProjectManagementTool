# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 206 files · ~55,946 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 822 nodes · 1793 edges · 101 communities (88 shown, 13 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 113 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `10d98c59`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Project
- Task
- Closure
- Illuminate\Database\Seeder
- Illuminate\Foundation\Http\FormRequest
- composer.json
- UserFactory
- scripts
- package.json
- Mermaid AI Skills
- ProjectManagementController.php
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
- Illuminate\View\View
- User
- Organization
- UpdateUserRequest
- LoginRequest
- StoreUserRequest
- UserManagementController.php
- AuditLog

## God Nodes (most connected - your core abstractions)
1. `User` - 128 edges
2. `Organization` - 74 edges
3. `Task` - 48 edges
4. `OrgMember` - 45 edges
5. `Role` - 40 edges
6. `Project` - 35 edges
7. `Department` - 30 edges
8. `AuditLog` - 25 edges
9. `Controller` - 23 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (101 total, 13 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.07
Nodes (10): organization(), OrgMember, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeStaffForDocumentList() (+2 more)

### Community 1 - "Project"
Cohesion: 0.09
Nodes (12): DashboardController, Collection, ProjectManagementController, TaskManagementController, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Project (+4 more)

### Community 2 - "Task"
Cohesion: 0.06
Nodes (15): CommentController, DocumentController, SubtaskController, TaskDocumentController, Document, Subtask, Task, SubtaskObserver (+7 more)

### Community 3 - "Closure"
Cohesion: 0.22
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 4 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): Permission, DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, Illuminate\Foundation\Http\FormRequest

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "UserFactory"
Cohesion: 0.32
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

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

### Community 86 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (18): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, KanbanController, NotificationController, NotificationSettingsController, OrganizationManagementController (+10 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (14): Collection, User, AuditLogPolicy, DepartmentPolicy, NotificationSettingPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+6 more)

### Community 88 - "Organization"
Cohesion: 0.07
Nodes (15): AccessControlController, DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, Organization, DepartmentSeeder, UserSeeder (+7 more)

### Community 91 - "StoreUserRequest"
Cohesion: 0.18
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 93 - "AuditLog"
Cohesion: 0.06
Nodes (11): AuditLog, Comment, AuditEventDatabaseNotification, AuditEventMailNotification, CommentObserver, CommentPolicy, AuditEventNotifier, Illuminate\Bus\Queueable (+3 more)

## Knowledge Gaps
- **99 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+94 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **13 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Project`, `Task`, `Illuminate\View\View`, `Organization`, `LoginRequest`, `UserManagementController.php`, `AuditLog`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Project`, `Task`, `Illuminate\Database\Seeder`, `ProjectManagementController.php`, `Illuminate\View\View`, `User`, `StoreUserRequest`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Project`, `Illuminate\View\View`, `User`, `AuditLog`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Are the 10 inferred relationships involving `User` (e.g. with `.index()` and `.index()`) actually correct?**
  _`User` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Organization` (e.g. with `.index()` and `.create()`) actually correct?**
  _`Organization` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.store()`) actually correct?**
  _`Task` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _99 weakly-connected nodes found - possible documentation gaps or missing edges._