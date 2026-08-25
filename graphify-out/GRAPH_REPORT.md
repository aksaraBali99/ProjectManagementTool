# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 286 files · ~94,075 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1162 nodes · 2865 edges · 154 communities (129 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 184 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `306c2932`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UpdateUserRequest
- ImportValidator
- .boardOrganizationIds
- AuditEventNotifier
- Role
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- UpdateTaskStatusColorsRequest
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
- Department
- setup
- Subtask
- Organization
- _form.blade.php
- Illuminate\View\View
- ImportController.php
- Illuminate\Http\JsonResponse
- LoginRequest
- ImportBatch
- Illuminate\Database\Seeder
- PermissionManagementController.php
- Illuminate\Http\Request
- AuditLog
- Document
- Illuminate\Http\RedirectResponse
- Comment
- ProjectManagementController
- config
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- NotificationSettingPolicy
- StoreTaskRequest
- StoreProjectRequest
- require
- SubtaskPolicy
- UpdateDepartmentRequest
- extra
- Project
- psr-4
- UpdateTaskPriorityColorsRequest
- test
- UpdateProjectRequest
- UpdateRoleRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 166 edges
2. `Organization` - 94 edges
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
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (154 total, 25 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.12
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "AuditEventNotifier"
Cohesion: 0.14
Nodes (4): AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 4 - "Role"
Cohesion: 0.13
Nodes (11): AccessControlController, RoleManagementController, AccessPermission, Role, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeClientForDocumentList() (+3 more)

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
Cohesion: 0.08
Nodes (9): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+1 more)

### Community 88 - "Role.php"
Cohesion: 0.06
Nodes (10): OrgMember, TaskPriorityColor, TaskStatusColor, Carbon\Carbon, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeStaffForDocumentList() (+2 more)

### Community 90 - "Task"
Cohesion: 0.13
Nodes (6): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "Department"
Cohesion: 0.21
Nodes (5): DepartmentManagementController, Department, makeTaskForAnalytics(), makeTaskOnDashboard(), makeTaskForStartDateTest()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.21
Nodes (6): SubtaskController, Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 105 - "Organization"
Cohesion: 0.14
Nodes (8): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), Organization, self

### Community 107 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (10): AnalyticsController, AuditTrailController, GoogleAuthController, CalendarController, Controller, NotificationController, OrganizationManagementController, SettingsController (+2 more)

### Community 109 - "Illuminate\Http\JsonResponse"
Cohesion: 0.16
Nodes (5): CommentController, TaskDocumentController, isAssignableStaffForProject(), UpdateTaskRequest, Illuminate\Http\JsonResponse

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): AbandonStaleImportBatches, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, Illuminate\Console\Command

### Community 112 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (7): resolveCurrentOrganization(), DashboardController, Collection, DocumentController, KanbanController, Illuminate\Http\Request, Illuminate\Support\Collection

### Community 115 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 116 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (6): AuthenticatedSessionController, NotificationSettingsController, TaskColorController, UserManagementController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.21
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 124 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 134 - "StoreTaskRequest"
Cohesion: 0.14
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 143 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 145 - "Project"
Cohesion: 0.18
Nodes (4): Project, ProjectPolicy, makeProjectMember(), makeClientForDocuments()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `AuditEventNotifier`, `Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `NotificationSettingPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `SubtaskPolicy`, `Project`, `ImportTemplateBuilder`, `Role.php`, `Task`, `Organization`, `Illuminate\View\View`, `Illuminate\Http\JsonResponse`, `LoginRequest`, `ImportBatch`, `Illuminate\Http\Request`, `AuditLog`, `Document`, `Illuminate\Http\RedirectResponse`, `Comment`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.139) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `StoreTaskRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `ImportTemplateBuilder`, `Illuminate\Validation\Validator`, `User`, `Role.php`, `Department`, `Illuminate\View\View`, `ImportBatch`, `Illuminate\Database\Seeder`, `Illuminate\Http\Request`, `Document`, `Illuminate\Http\RedirectResponse`, `ProjectManagementController`, `TaskManagementController`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Subtask`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `SubtaskPolicy`, `ImportBatch`, `Department`, `Illuminate\Http\Request`, `AuditLog`, `Role.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.042) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 18 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._