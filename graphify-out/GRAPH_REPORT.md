# Graph Report - ProjectManagementTool  (2026-09-18)

## Corpus Check
- 300 files · ~102,611 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1222 nodes · 2951 edges · 167 communities (131 shown, 36 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 198 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b01652dc`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Organization
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- Project
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
- ImportTemplateBuilder
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- AccessPermission
- User
- OrgMember
- Illuminate\Http\JsonResponse
- CommentPolicy
- LoginRequest
- setup
- documents/create.blade.php
- Subtask
- self
- _form.blade.php
- Role
- Task
- DepartmentPolicy
- Illuminate\Http\Request
- ImportBatch
- Task.php
- UpdateUserRequest
- StoreTaskRequest
- Comment
- TaskManagementController
- UserPolicy
- Document
- Department
- config
- Illuminate\Support\Collection
- TagsImportBatch.php
- task-colors/edit.blade.php
- Illuminate\View\View
- Illuminate\Foundation\Http\FormRequest
- DepartmentManagementController.php
- require
- UpdateRoleRequest
- AuditLog
- psr-4
- BootstrapEnvironment
- test
- Illuminate\Validation\Validator
- UpdateTaskStatusColorsRequest
- StoreProjectRequest
- StoreOrganizationRequest
- UpdateProjectRequest
- UpdateTaskPriorityColorsRequest
- extra

## God Nodes (most connected - your core abstractions)
1. `User` - 183 edges
2. `Organization` - 96 edges
3. `Task` - 66 edges
4. `OrgMember` - 60 edges
5. `Project` - 49 edges
6. `Department` - 45 edges
7. `Role` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (167 total, 36 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.22
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Organization"
Cohesion: 0.13
Nodes (5): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList()

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (27): concurrently, @fontsource/inter, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js, @fontsource/inter (+19 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.17
Nodes (3): Permission, PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Project"
Cohesion: 0.22
Nodes (6): Project, makeTaskForAnalytics(), makeProjectMember(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeTaskForStartDateTest()

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.40
Nodes (4): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._documents

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.14
Nodes (13): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet (+5 more)

### Community 86 - "AccessPermission"
Cohesion: 0.20
Nodes (5): AccessPermission, UserSeeder, makeStaffOnCalendar(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, AuditLogPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 88 - "OrgMember"
Cohesion: 0.14
Nodes (6): OrgMember, CompanyRoleSyncer, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeStaffForDocuments(), joinOrg()

### Community 90 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): CommentController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Role"
Cohesion: 0.12
Nodes (6): AnalyticsController, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeClientForDocuments(), makeClientOnProject()

### Community 108 - "Task"
Cohesion: 0.13
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 110 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (18): AuditTrailController, DashboardController, Collection, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins() (+10 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (9): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon (+1 more)

### Community 112 - "Task.php"
Cohesion: 0.10
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 118 - "Document"
Cohesion: 0.32
Nodes (3): TaskDocumentController, Document, makeDocument()

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 122 - "Illuminate\Support\Collection"
Cohesion: 0.31
Nodes (4): resolveCurrentOrganization(), KanbanController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 124 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 133 - "Illuminate\View\View"
Cohesion: 0.05
Nodes (19): AccessControlController, AuthenticatedSessionController, GoogleAuthController, CalendarController, Controller, DocumentController, NotificationController, NotificationSettingsController (+11 more)

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 145 - "AuditLog"
Cohesion: 0.07
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.22
Nodes (3): AbandonStaleImportBatches, BootstrapEnvironment, Illuminate\Console\Command

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 173 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **114 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+109 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **36 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\View\View`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `ImportCommitService.php`, `AuditLog`, `BootstrapEnvironment`, `Department.php`, `ImportTemplateBuilder`, `AccessPermission`, `OrgMember`, `Illuminate\Http\JsonResponse`, `CommentPolicy`, `LoginRequest`, `Subtask`, `Role`, `Task`, `DepartmentPolicy`, `Illuminate\Http\Request`, `ImportBatch`, `Task.php`, `TaskManagementController`, `UserPolicy`, `Document`, `Illuminate\Support\Collection`?**
  _High betweenness centrality (0.199) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Database\Seeder`, `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\View\View`, `DepartmentManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `ImportCommitService.php`, `Illuminate\Validation\Validator`, `ImportTemplateBuilder`, `AccessPermission`, `User`, `OrgMember`, `self`, `Role`, `Illuminate\Http\Request`, `ImportBatch`, `Task.php`, `TaskManagementController`, `Document`, `Department`, `Illuminate\Support\Collection`?**
  _High betweenness centrality (0.053) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `User.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `Department.php`, `OrgMember`, `Illuminate\Http\JsonResponse`, `Subtask`, `Role`, `Illuminate\Http\Request`, `ImportBatch`, `Task.php`, `Comment`, `TaskManagementController`, `Document`, `Illuminate\Support\Collection`, `TagsImportBatch.php`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _114 weakly-connected nodes found - possible documentation gaps or missing edges._