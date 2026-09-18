# Graph Report - ProjectManagementTool  (2026-09-18)

## Corpus Check
- 306 files · ~104,153 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1255 nodes · 3013 edges · 175 communities (134 shown, 41 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 199 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5e79b197`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
- User.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- Role
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
- Department
- User
- Role.php
- auth.php
- CommentPolicy
- AuditEventMailNotification.php
- setup
- documents/create.blade.php
- FileCategory.php
- Illuminate\Database\Eloquent\Model
- _form.blade.php
- OrganizationPolicy
- Illuminate\Http\JsonResponse
- UserManagementController
- HasAdminConfigurableColors.php
- ImportBatch
- StoreTaskRequest
- CalendarController.php
- NotificationSetting
- Project
- AuditLogPolicy.php
- StoreDepartmentRequest
- Illuminate\View\View
- config
- Organization
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- require
- Illuminate\Database\Eloquent\Builder
- Comment
- AuditLog
- psr-4
- BootstrapEnvironment
- Subtask
- Illuminate\Validation\Validator
- UpdateTaskStatusColorsRequest
- Task
- UpdateTaskRequest
- Closure
- UpdateProjectRequest
- Illuminate\Support\Collection
- static
- Department.php
- DocumentPolicy.php
- post-create-project-cmd
- UpdateUserRequest
- keywords
- UserPolicy
- StoreProjectRequest

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
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (175 total, 41 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (6): ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "User.php"
Cohesion: 0.11
Nodes (3): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate()

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

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
Cohesion: 0.40
Nodes (4): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._documents

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.23
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 86 - "Department"
Cohesion: 0.14
Nodes (8): AccessPermission, Department, makeStaffOnCalendar(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess(), makeTaskForStartDateTest()

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, DepartmentPolicy, ProjectPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, downloadTemplateSpreadsheet() (+2 more)

### Community 90 - "auth.php"
Cohesion: 0.22
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 92 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.09
Nodes (17): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile (+9 more)

### Community 105 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.24
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 108 - "Illuminate\Http\JsonResponse"
Cohesion: 0.18
Nodes (5): CommentController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 115 - "NotificationSetting"
Cohesion: 0.21
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 116 - "Project"
Cohesion: 0.21
Nodes (5): TaskManagementController, Project, makeTaskForAnalytics(), makeProjectMember(), makeClientOnProject()

### Community 119 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+4 more)

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 122 - "Organization"
Cohesion: 0.10
Nodes (11): resolveCurrentOrganization(), DocumentController, KanbanController, Document, Organization, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+3 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (6): AccessControlController, GoogleAuthController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.25
Nodes (8): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet

### Community 143 - "Comment"
Cohesion: 0.25
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 145 - "AuditLog"
Cohesion: 0.11
Nodes (6): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.22
Nodes (3): AbandonStaleImportBatches, BootstrapEnvironment, Illuminate\Console\Command

### Community 149 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Task"
Cohesion: 0.19
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 159 - "Closure"
Cohesion: 0.25
Nodes (5): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 164 - "Illuminate\Support\Collection"
Cohesion: 0.20
Nodes (5): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 165 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 168 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 170 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **115 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+110 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `LoginRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Builder`, `Role`, `AuditLog`, `BootstrapEnvironment`, `Subtask`, `Task`, `Closure`, `Illuminate\Support\Collection`, `Department.php`, `DocumentPolicy.php`, `UserPolicy`, `Department`, `Role.php`, `CommentPolicy`, `FileCategory.php`, `OrganizationPolicy`, `Illuminate\Http\JsonResponse`, `UserManagementController`, `ImportBatch`, `Priority.php`, `NotificationSetting`, `Project`, `AuditLogPolicy.php`, `Illuminate\View\View`, `Organization`?**
  _High betweenness centrality (0.203) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `Illuminate\Validation\Validator`, `Illuminate\Support\Collection`, `Department.php`, `ImportTemplateBuilder`, `Department`, `Illuminate\Database\Eloquent\Model`, `OrganizationPolicy`, `ImportBatch`, `Priority.php`, `CalendarController.php`, `Project`, `StoreDepartmentRequest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Department.php`, `Illuminate\Database\Eloquent\Model`, `Comment`, `ImportBatch`, `Task`, `BootstrapEnvironment`, `NotificationSetting`, `Subtask`, `Illuminate\View\View`, `AuditEventMailNotification.php`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _115 weakly-connected nodes found - possible documentation gaps or missing edges._