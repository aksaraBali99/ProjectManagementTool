# Graph Report - ProjectManagementTool  (2026-09-01)

## Corpus Check
- 298 files · ~98,703 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1197 nodes · 2906 edges · 174 communities (139 shown, 35 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 195 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `50bde88e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Role.php
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Organization
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
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
- Task
- User
- TaskObserver
- Illuminate\Foundation\Http\FormRequest
- DocumentPolicy
- setup
- Subtask
- self
- _form.blade.php
- Illuminate\View\View
- Illuminate\Support\Collection
- Document
- Closure
- ImportBatch
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Model
- UserManagementController
- NotificationEventType.php
- Illuminate\Http\Request
- Illuminate\Http\RedirectResponse
- Comment
- config
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ImportController.php
- AuditEventNotifier
- Role
- require
- .__invoke
- StoreTaskRequest
- TaskPolicy
- AuditLog
- psr-4
- BootstrapEnvironment
- test
- Illuminate\Validation\Validator
- StoreProjectRequest
- OrganizationPolicy
- UserPolicy
- UpdateDepartmentRequest
- UpdateTaskRequest
- StoreOrganizationRequest
- Illuminate\Database\Eloquent\Builder
- Project
- NotificationSetting
- Department
- LoginRequest
- CommentPolicy
- SubtaskPolicy
- AuditLogPolicy.php
- extra

## God Nodes (most connected - your core abstractions)
1. `User` - 173 edges
2. `Organization` - 96 edges
3. `Task` - 66 edges
4. `OrgMember` - 60 edges
5. `Project` - 49 edges
6. `Role` - 47 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 32 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (174 total, 35 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "OrgMember"
Cohesion: 0.19
Nodes (3): OrgMember, CompanyRoleSyncer, joinOrg()

### Community 5 - "Organization"
Cohesion: 0.10
Nodes (6): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList()

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

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.50
Nodes (3): tasks._comments, tasks._subtasks, tasks._documents

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.14
Nodes (13): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet (+5 more)

### Community 86 - "Task"
Cohesion: 0.14
Nodes (7): CommentController, SubtaskController, TaskDocumentController, Task, MentionedInCommentNotification, Illuminate\Database\Eloquent\SoftDeletes, Illuminate\Http\JsonResponse

### Community 87 - "User"
Cohesion: 0.12
Nodes (6): User, DepartmentPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (4): UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Contracts\Validation\Validator, Illuminate\Foundation\Http\FormRequest

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (10): AnalyticsController, AuthenticatedSessionController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController, RoleManagementController, SettingsController (+2 more)

### Community 108 - "Illuminate\Support\Collection"
Cohesion: 0.19
Nodes (6): AccessControlController, resolveCurrentOrganization(), KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 110 - "Closure"
Cohesion: 0.19
Nodes (8): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 111 - "ImportBatch"
Cohesion: 0.21
Nodes (5): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "Illuminate\Database\Seeder"
Cohesion: 0.15
Nodes (7): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 113 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.27
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 116 - "Illuminate\Http\Request"
Cohesion: 0.31
Nodes (4): AuditTrailController, DashboardController, Collection, Illuminate\Http\Request

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (4): GoogleAuthController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.29
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 124 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 133 - "ImportController.php"
Cohesion: 0.25
Nodes (3): ImportController, UploadImportRequest, Symfony\Component\HttpFoundation\StreamedResponse

### Community 139 - "Role"
Cohesion: 0.15
Nodes (9): AccessPermission, Role, RolePolicy, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeClientOnProject() (+1 more)

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 141 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 145 - "AuditLog"
Cohesion: 0.13
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.07
Nodes (13): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins() (+5 more)

### Community 166 - "Project"
Cohesion: 0.16
Nodes (9): isAssignableStaffForProject(), Project, makeTaskForAnalytics(), makeProjectMember(), makeTaskOnDashboard(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments() (+1 more)

### Community 168 - "Department"
Cohesion: 0.17
Nodes (3): DepartmentManagementController, StoreDepartmentRequest, Department

### Community 173 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **104 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+99 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Task.php`, `Organization`, `AuditEventNotifier`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `TaskPolicy`, `AuditLog`, `BootstrapEnvironment`, `OrganizationPolicy`, `UserPolicy`, `Illuminate\Database\Eloquent\Builder`, `Project`, `NotificationSetting`, `LoginRequest`, `CommentPolicy`, `SubtaskPolicy`, `AuditLogPolicy.php`, `ImportTemplateBuilder`, `Task`, `User.php`, `DocumentPolicy`, `Subtask`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Document`, `ImportBatch`, `UserManagementController`, `NotificationEventType.php`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.194) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Task.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `.__invoke`, `Illuminate\Validation\Validator`, `OrganizationPolicy`, `Project`, `Department`, `ImportTemplateBuilder`, `Task`, `User.php`, `self`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Document`, `ImportBatch`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `UserManagementController`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `TaskManagementController`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role.php`, `ImportValidator`, `OrgMember`, `Task.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.__invoke`, `TaskPolicy`, `AuditLog`, `Illuminate\Database\Eloquent\Builder`, `Project`, `SubtaskPolicy`, `User.php`, `TaskObserver`, `Subtask`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Document`, `ImportBatch`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _104 weakly-connected nodes found - possible documentation gaps or missing edges._