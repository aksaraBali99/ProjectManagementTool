# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 283 files · ~93,506 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1162 nodes · 2834 edges · 161 communities (135 shown, 26 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 180 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3c52a5cb`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UpdateUserRequest
- ImportValidator
- .boardOrganizationIds
- Project
- Organization
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- Illuminate\Http\JsonResponse
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
- StoreProjectRequest
- setup
- Subtask
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- TaskManagementController
- Illuminate\Database\Eloquent\Model
- static
- ImportBatch
- Illuminate\Http\Request
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Builder
- AuditLog
- Document
- Illuminate\Http\RedirectResponse
- Comment
- Priority.php
- config
- UserManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Support\Collection
- TaskStatus.php
- require
- Closure
- extra
- LoginRequest
- StoreTaskRequest
- psr-4
- CalendarController.php
- UpdateTaskPriorityColorsRequest
- test
- UserPolicy
- UpdateDepartmentRequest
- OrgMember
- ImportValidator.php
- UpdateTaskStatusColorsRequest
- TagsImportBatch.php

## God Nodes (most connected - your core abstractions)
1. `User` - 166 edges
2. `Organization` - 92 edges
3. `Task` - 64 edges
4. `OrgMember` - 58 edges
5. `Project` - 46 edges
6. `Role` - 45 edges
7. `Department` - 44 edges
8. `ImportValidator` - 37 edges
9. `ImportBatch` - 32 edges
10. `AuditLog` - 31 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php
- `makeClientOnProject()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (161 total, 26 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.12
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Project"
Cohesion: 0.18
Nodes (4): Project, ProjectPolicy, makeProjectMember(), makeClientOnProject()

### Community 4 - "Organization"
Cohesion: 0.08
Nodes (15): AccessControlController, AnalyticsController, DepartmentManagementController, AccessPermission, Department, Organization, Role, makeTaskForAnalytics() (+7 more)

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

### Community 14 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): CommentController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

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
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 87 - "User"
Cohesion: 0.10
Nodes (8): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 90 - "Task"
Cohesion: 0.19
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateProjectRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 107 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (8): ImportController, KanbanController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View, Symfony\Component\HttpFoundation\StreamedResponse

### Community 109 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (4): allColors(), TaskPriorityColor, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Notification

### Community 110 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): AbandonStaleImportBatches, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, ImportCommitSummary, Illuminate\Console\Command

### Community 112 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (9): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, Collection, DocumentController, PermissionManagementController (+1 more)

### Community 113 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 115 - "AuditLog"
Cohesion: 0.06
Nodes (13): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, MentionedInCommentNotification, NotificationSettingPolicy, NotificationEventType, NotificationSettingsResolver (+5 more)

### Community 116 - "Document"
Cohesion: 0.12
Nodes (5): TaskDocumentController, Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.20
Nodes (3): Comment, CommentObserver, AuditEventNotifier

### Community 119 - "Priority.php"
Cohesion: 0.10
Nodes (6): badgeBackground(), badgeText(), colorRow(), self, values(), makeTaskOnDashboard()

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 124 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 139 - "TaskStatus.php"
Cohesion: 0.22
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), TaskStatusColor

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 141 - "Closure"
Cohesion: 0.19
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 143 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 145 - "StoreTaskRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

### Community 154 - "OrgMember"
Cohesion: 0.24
Nodes (5): OrgMember, makeStaffForDocumentCreate(), makeStaffForDocumentList(), makeStaffForDocuments(), joinOrg()

### Community 157 - "TagsImportBatch.php"
Cohesion: 0.83
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **26 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Project`, `Organization`, `Department.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `LoginRequest`, `UserPolicy`, `OrgMember`, `ImportTemplateBuilder`, `Role.php`, `Task`, `Subtask`, `CommentPolicy`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Builder`, `AuditLog`, `Document`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Project`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreTaskRequest`, `CalendarController.php`, `OrgMember`, `ImportTemplateBuilder`, `Illuminate\Validation\Validator`, `User`, `Role.php`, `Illuminate\View\View`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`, `ImportBatch`, `Illuminate\Http\Request`, `Document`, `Illuminate\Http\RedirectResponse`, `Priority.php`, `UserManagementController`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Organization`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `CalendarController.php`, `Role.php`, `Subtask`, `Illuminate\View\View`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Builder`, `AuditLog`, `Document`, `Illuminate\Http\RedirectResponse`, `Comment`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 18 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._