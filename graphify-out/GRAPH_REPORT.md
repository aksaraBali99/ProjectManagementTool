# Graph Report - ProjectManagementTool  (2026-09-01)

## Corpus Check
- 298 files · ~100,502 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1206 nodes · 2911 edges · 172 communities (142 shown, 30 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 193 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `84712b4d`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Department.php
- Illuminate\Database\Eloquent\Relations\HasMany
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
- Illuminate\Validation\Validator
- User
- Task
- Illuminate\Foundation\Http\FormRequest
- Document
- setup
- Subtask
- self
- _form.blade.php
- Illuminate\View\View
- Illuminate\Support\Collection
- Illuminate\Http\Request
- Closure
- ImportBatch
- PermissionSeeder.php
- Illuminate\Database\Eloquent\Model
- UserManagementController
- AuditLog
- Department
- Illuminate\Http\RedirectResponse
- Comment
- config
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Organization
- UpdateUserRequest
- Role
- require
- CalendarController.php
- StoreTaskRequest
- keywords
- AuditEventMailNotification.php
- psr-4
- BootstrapEnvironment
- test
- CompanyRoleRules
- StoreProjectRequest
- UpdateRoleRequest
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- UpdateTaskRequest
- UpdateProjectRequest
- TagsImportBatch.php
- Project
- NotificationSetting
- StoreDepartmentRequest
- LoginRequest
- CommentPolicy
- SubtaskPolicy

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

## Communities (172 total, 30 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "OrgMember"
Cohesion: 0.16
Nodes (4): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), joinOrg()

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (29): concurrently, @fontsource/inter, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js (+21 more)

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

### Community 47 - "app.js"
Cohesion: 0.20
Nodes (12): buildDisplayRows(), CALENDAR_VIEW_DAYS, CHART_PALETTE, DAY_ABBREVIATIONS, DAY_VIEW_MODE_WITH_WEEKDAY, formatLocalDate(), formatPopupDate(), getCalendarViewStart() (+4 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.14
Nodes (13): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet (+5 more)

### Community 86 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 87 - "User"
Cohesion: 0.08
Nodes (9): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+1 more)

### Community 90 - "Task"
Cohesion: 0.17
Nodes (5): TaskDocumentController, Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "Document"
Cohesion: 0.15
Nodes (8): Document, DocumentPolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.16
Nodes (3): SubtaskController, Subtask, SubtaskObserver

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Illuminate\View\View"
Cohesion: 0.15
Nodes (6): ImportController, NotificationController, RoleManagementController, SettingsController, Illuminate\View\View, Symfony\Component\HttpFoundation\StreamedResponse

### Community 108 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (6): resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection

### Community 109 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (4): AuditTrailController, DocumentController, PermissionManagementController, Illuminate\Http\Request

### Community 110 - "Closure"
Cohesion: 0.20
Nodes (8): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "PermissionSeeder.php"
Cohesion: 0.18
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 113 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.24
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 115 - "AuditLog"
Cohesion: 0.12
Nodes (6): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (7): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.22
Nodes (4): CommentController, Comment, CommentObserver, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 124 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 133 - "Organization"
Cohesion: 0.14
Nodes (4): AnalyticsController, DepartmentManagementController, OrganizationManagementController, Organization

### Community 139 - "Role"
Cohesion: 0.13
Nodes (8): AccessPermission, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 143 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 145 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.22
Nodes (3): AbandonStaleImportBatches, BootstrapEnvironment, Illuminate\Console\Command

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

### Community 151 - "CompanyRoleRules"
Cohesion: 0.18
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 164 - "TagsImportBatch.php"
Cohesion: 0.83
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 166 - "Project"
Cohesion: 0.22
Nodes (6): isAssignableStaffForProject(), Project, makeProjectMember(), makeTaskOnDashboard(), makeClientOnProject(), makeTaskForStartDateTest()

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Department.php`, `Organization`, `UpdateUserRequest`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `ImportCommitService.php`, `BootstrapEnvironment`, `Project`, `NotificationSetting`, `LoginRequest`, `CommentPolicy`, `SubtaskPolicy`, `ImportTemplateBuilder`, `User.php`, `Task`, `Document`, `Subtask`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `ImportBatch`, `PermissionSeeder.php`, `UserManagementController`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Comment`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.183) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Role.php`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `CalendarController.php`, `CompanyRoleRules`, `Project`, `StoreDepartmentRequest`, `ImportTemplateBuilder`, `User`, `User.php`, `Document`, `self`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `ImportBatch`, `Illuminate\Database\Eloquent\Model`, `UserManagementController`, `Department`, `Illuminate\Http\RedirectResponse`, `TaskManagementController`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role.php`, `ImportValidator`, `OrgMember`, `Department.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `CalendarController.php`, `AuditEventMailNotification.php`, `Project`, `SubtaskPolicy`, `Subtask`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `ImportBatch`, `PermissionSeeder.php`, `Illuminate\Database\Eloquent\Model`, `Department`, `Comment`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._