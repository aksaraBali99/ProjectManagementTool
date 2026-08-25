# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 285 files · ~93,945 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1160 nodes · 2844 edges · 154 communities (131 shown, 23 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 184 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f6ebc9df`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UpdateUserRequest
- ImportValidator
- .boardOrganizationIds
- AuditLog
- Role
- Organization
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- TaskStatus.php
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
- Role.php
- Task
- Illuminate\Foundation\Http\FormRequest
- AuditLog.php
- setup
- Subtask
- self
- _form.blade.php
- Illuminate\View\View
- ImportController.php
- Illuminate\Http\JsonResponse
- static
- ImportBatch
- Closure
- SubtaskPolicy
- Illuminate\Http\Request
- AuditEventMailNotification.php
- Document
- Illuminate\Http\RedirectResponse
- Comment
- config
- UserManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- NotificationSettingPolicy
- Project
- ProjectStatus.php
- require
- EnsureBelongsToOrganization.php
- extra
- LoginRequest
- StoreTaskRequest
- psr-4
- CalendarController.php
- Illuminate\Database\Eloquent\Model
- test
- ImportCommitService.php
- UpdateRoleRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 166 edges
2. `Organization` - 92 edges
3. `Task` - 64 edges
4. `OrgMember` - 58 edges
5. `Project` - 46 edges
6. `Role` - 45 edges
7. `Department` - 44 edges
8. `ImportValidator` - 38 edges
9. `ImportBatch` - 32 edges
10. `AuditLog` - 31 edges

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

## Communities (154 total, 23 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.12
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - ".boardOrganizationIds"
Cohesion: 0.15
Nodes (3): Permission, Collection, BoardAccessDeniedReason

### Community 3 - "AuditLog"
Cohesion: 0.14
Nodes (5): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 4 - "Role"
Cohesion: 0.13
Nodes (8): AccessPermission, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 5 - "Organization"
Cohesion: 0.10
Nodes (6): DepartmentManagementController, Department, Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeTaskForAnalytics(), makeTaskForStartDateTest()

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
Cohesion: 0.15
Nodes (12): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet (+4 more)

### Community 86 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 87 - "User"
Cohesion: 0.09
Nodes (9): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+1 more)

### Community 88 - "Role.php"
Cohesion: 0.08
Nodes (16): OrgMember, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder (+8 more)

### Community 90 - "Task"
Cohesion: 0.13
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "AuditLog.php"
Cohesion: 0.29
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, KanbanController, NotificationController, OrganizationManagementController (+3 more)

### Community 109 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (3): CommentController, SubtaskController, Illuminate\Http\JsonResponse

### Community 110 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.16
Nodes (8): AbandonStaleImportBatches, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, Illuminate\Console\Command

### Community 112 - "Closure"
Cohesion: 0.43
Nodes (4): ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (6): AccessControlController, AuditTrailController, DashboardController, Collection, PermissionManagementController, Illuminate\Http\Request

### Community 115 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 116 - "Document"
Cohesion: 0.18
Nodes (5): TaskDocumentController, Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (4): NotificationSettingsController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.24
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 134 - "Project"
Cohesion: 0.15
Nodes (7): ProjectManagementController, TaskManagementController, isAssignableStaffForProject(), Project, Illuminate\Support\Collection, makeProjectMember(), makeTaskOnDashboard()

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 141 - "EnsureBelongsToOrganization.php"
Cohesion: 0.29
Nodes (3): EnsureBelongsToOrganization, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 143 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 145 - "StoreTaskRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 148 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (5): UpdateTaskPriorityColorsRequest, ImportRow, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **23 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `AuditLog`, `Role`, `Organization`, `Project`, `NotificationSettingPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `LoginRequest`, `ImportCommitService.php`, `ImportTemplateBuilder`, `Role.php`, `Task`, `Illuminate\View\View`, `Illuminate\Http\JsonResponse`, `ImportBatch`, `SubtaskPolicy`, `Illuminate\Http\Request`, `Document`, `Illuminate\Http\RedirectResponse`, `Comment`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Role`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskStatus.php`, `StoreTaskRequest`, `CalendarController.php`, `Illuminate\Database\Eloquent\Model`, `ImportTemplateBuilder`, `Illuminate\Validation\Validator`, `User`, `Role.php`, `self`, `Illuminate\View\View`, `ImportBatch`, `Illuminate\Http\Request`, `Document`, `Illuminate\Http\RedirectResponse`, `UserManagementController`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Role`, `Subtask`, `Project`, `Organization`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `AuditLog.php`, `ImportBatch`, `SubtaskPolicy`, `Illuminate\Http\Request`, `CalendarController.php`, `Document`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 18 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._