# Graph Report - ProjectManagementTool  (2026-09-01)

## Corpus Check
- 298 files · ~99,193 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1205 nodes · 2906 edges · 166 communities (142 shown, 24 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 193 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `34c5507b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Role.php
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Department.php
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
- LoginRequest
- ImportBatch
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Model
- UserManagementController
- AuditLog
- Department
- Illuminate\Http\RedirectResponse
- Comment
- config
- Project
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Pest.php
- UpdateUserRequest
- Role
- require
- CalendarController.php
- StoreTaskRequest
- extra
- UpdateDepartmentRequest
- psr-4
- BootstrapEnvironment
- test
- StoreProjectRequest
- UpdateRoleRequest
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- UpdateTaskRequest
- UpdateProjectRequest
- TagsImportBatch.php

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
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (166 total, 24 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (6): ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - ".boardOrganizationIds"
Cohesion: 0.16
Nodes (3): Permission, Collection, BoardAccessDeniedReason

### Community 3 - "OrgMember"
Cohesion: 0.13
Nodes (5): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), makeStaffForDocumentList(), joinOrg()

### Community 5 - "Organization"
Cohesion: 0.12
Nodes (3): DocumentController, Organization, Illuminate\Database\Eloquent\Relations\HasMany

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
Cohesion: 0.23
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 86 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 87 - "User"
Cohesion: 0.07
Nodes (10): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+2 more)

### Community 90 - "Task"
Cohesion: 0.15
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "Document"
Cohesion: 0.18
Nodes (6): Document, DocumentPolicy, makeDocumentForList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController (+3 more)

### Community 108 - "Illuminate\Support\Collection"
Cohesion: 0.20
Nodes (6): resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection

### Community 109 - "Illuminate\Http\Request"
Cohesion: 0.18
Nodes (5): CommentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 110 - "LoginRequest"
Cohesion: 0.08
Nodes (14): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber (+6 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (9): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon (+1 more)

### Community 112 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 113 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.31
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 115 - "AuditLog"
Cohesion: 0.07
Nodes (12): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, NotificationEventType, NotificationSettingsResolver, NotificationEventType (+4 more)

### Community 116 - "Department"
Cohesion: 0.13
Nodes (5): DepartmentManagementController, StoreDepartmentRequest, Department, makeTaskForAnalytics(), makeTaskForStartDateTest()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): AccessControlController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.21
Nodes (3): Comment, CommentObserver, AuditEventNotifier

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 122 - "Project"
Cohesion: 0.20
Nodes (5): TaskManagementController, isAssignableStaffForProject(), Project, makeProjectMember(), makeTaskOnDashboard()

### Community 133 - "Pest.php"
Cohesion: 0.22
Nodes (7): Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, downloadTemplateSpreadsheet(), buildImportTestFile(), createOwner(), runImportValidation(), TestCase

### Community 139 - "Role"
Cohesion: 0.12
Nodes (11): AccessPermission, Role, RolePolicy, UserSeeder, Illuminate\Database\Eloquent\Builder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeClientForDocumentList() (+3 more)

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 143 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.20
Nodes (3): AbandonStaleImportBatches, BootstrapEnvironment, Illuminate\Console\Command

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

### Community 164 - "TagsImportBatch.php"
Cohesion: 0.83
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **24 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Department.php`, `Organization`, `Pest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `BootstrapEnvironment`, `User.php`, `Task`, `Document`, `Subtask`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `LoginRequest`, `ImportBatch`, `UserManagementController`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.181) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `CalendarController.php`, `DocumentController.php`, `ImportTemplateBuilder`, `Illuminate\Validation\Validator`, `User`, `User.php`, `Document`, `self`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `ImportBatch`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `UserManagementController`, `Department`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `Project`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `Department.php`, `Subtask`, `Illuminate\View\View`, `ImportBatch`, `Illuminate\Database\Eloquent\Model`, `BootstrapEnvironment`, `Comment`, `Task`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._