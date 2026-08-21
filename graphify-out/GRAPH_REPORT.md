# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 214 files · ~58,987 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 855 nodes · 1868 edges · 107 communities (92 shown, 15 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 113 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `163a5124`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- HidesInactiveFromNonAdmins.php
- Task
- Closure
- Document
- LoginRequest
- composer.json
- Organization
- scripts
- package.json
- Mermaid AI Skills
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
- app.js
- _password-input.blade.php
- Illuminate\View\View
- User
- StoreTaskRequest
- StoreUserRequest
- AuditLog
- Illuminate\Foundation\Http\FormRequest
- UpdateUserRequest
- UserManagementController.php
- StoreProjectRequest
- UpdateRoleRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 134 edges
2. `Organization` - 77 edges
3. `Task` - 51 edges
4. `OrgMember` - 47 edges
5. `Role` - 41 edges
6. `Project` - 37 edges
7. `Department` - 32 edges
8. `AuditLog` - 29 edges
9. `Controller` - 24 edges
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

## Communities (107 total, 15 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.06
Nodes (15): OrgMember, Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder (+7 more)

### Community 1 - "HidesInactiveFromNonAdmins.php"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 2 - "Task"
Cohesion: 0.05
Nodes (16): CommentController, SubtaskController, TaskManagementController, isAssignableStaffForProject(), Comment, organization(), Subtask, Task (+8 more)

### Community 3 - "Closure"
Cohesion: 0.19
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 4 - "Document"
Cohesion: 0.12
Nodes (5): TaskDocumentController, Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Organization"
Cohesion: 0.06
Nodes (18): DashboardController, Collection, ProjectManagementController, UpdateProjectRequest, AccessPermission, Department, Organization, Project (+10 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (25): concurrently, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js, intl-tel-input, devDependencies (+17 more)

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
Cohesion: 0.05
Nodes (24): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, DocumentController (+16 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (13): Collection, User, AuditLogPolicy, CommentPolicy, NotificationSettingPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy (+5 more)

### Community 91 - "StoreTaskRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 92 - "StoreUserRequest"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 93 - "AuditLog"
Cohesion: 0.07
Nodes (11): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, Illuminate\Bus\Queueable (+3 more)

### Community 101 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, Illuminate\Foundation\Http\FormRequest

## Knowledge Gaps
- **101 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+96 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **15 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Task`, `Document`, `LoginRequest`, `Organization`, `UserManagementController.php`, `Illuminate\View\View`, `AuditLog`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Task`, `Document`, `Illuminate\View\View`, `User`, `StoreTaskRequest`, `StoreUserRequest`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Document`, `Organization`, `Illuminate\View\View`, `User`, `AuditLog`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 11 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _101 weakly-connected nodes found - possible documentation gaps or missing edges._