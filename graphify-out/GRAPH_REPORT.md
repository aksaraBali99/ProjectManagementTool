# Graph Report - ProjectManagementTool  (2026-08-21)

## Corpus Check
- 214 files · ~58,802 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 853 nodes · 1865 edges · 105 communities (91 shown, 14 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 112 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `163a5124`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
- Illuminate\View\View
- Task
- Closure
- Document
- OrganizationPolicy
- composer.json
- Organization
- scripts
- package.json
- Mermaid AI Skills
- .boardOrganizationIds
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
- NotificationSettingPolicy
- User
- Department
- AuditLog
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- CommentPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 135 edges
2. `Organization` - 78 edges
3. `Task` - 49 edges
4. `OrgMember` - 48 edges
5. `Role` - 41 edges
6. `Project` - 35 edges
7. `Department` - 32 edges
8. `AuditLog` - 29 edges
9. `Controller` - 24 edges
10. `Subtask` - 22 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (105 total, 14 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.06
Nodes (15): OrgMember, Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Eloquent\Builder (+7 more)

### Community 1 - "Illuminate\View\View"
Cohesion: 0.05
Nodes (22): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, CalendarController, Controller, DepartmentManagementController, DocumentController (+14 more)

### Community 2 - "Task"
Cohesion: 0.05
Nodes (17): CommentController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Comment, organization(), Subtask, Task (+9 more)

### Community 3 - "Closure"
Cohesion: 0.12
Nodes (12): EnsureBelongsToOrganization, EnsureUserIsActive, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure, UserFactory (+4 more)

### Community 4 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 7 - "Organization"
Cohesion: 0.09
Nodes (11): DashboardController, Collection, ProjectManagementController, TaskManagementController, Organization, Project, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Support\Collection (+3 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "package.json"
Cohesion: 0.08
Nodes (25): concurrently, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, frappe-gantt, intl-tel-input (+17 more)

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

### Community 87 - "User"
Cohesion: 0.11
Nodes (8): User, AuditLogPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 88 - "Department"
Cohesion: 0.06
Nodes (13): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, AccessPermission, Department, DepartmentPolicy, UserSeeder, Illuminate\Contracts\Validation\Validator (+5 more)

### Community 93 - "AuditLog"
Cohesion: 0.08
Nodes (10): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationEventType, NotificationSettingsResolver, NotificationEventType, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue (+2 more)

### Community 101 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (13): LoginRequest, UpdateDepartmentRequest, StoreOrganizationRequest, UpdateOrganizationRequest, StoreProjectRequest, UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant() (+5 more)

## Knowledge Gaps
- **100 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+95 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `Illuminate\View\View`, `Task`, `Document`, `Illuminate\Foundation\Http\FormRequest`, `OrganizationPolicy`, `Organization`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `NotificationSettingPolicy`, `Department`, `AuditLog`?**
  _High betweenness centrality (0.145) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `Illuminate\View\View`, `Document`, `Illuminate\Foundation\Http\FormRequest`, `OrganizationPolicy`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Department`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `Illuminate\View\View`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Department`, `AuditLog`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 10 inferred relationships involving `User` (e.g. with `.index()` and `.index()`) actually correct?**
  _`User` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Organization` (e.g. with `.index()` and `.create()`) actually correct?**
  _`Organization` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _100 weakly-connected nodes found - possible documentation gaps or missing edges._