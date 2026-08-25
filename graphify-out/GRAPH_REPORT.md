# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 280 files · ~91,928 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1152 nodes · 2800 edges · 154 communities (129 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 179 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `e28598ec`
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
- Illuminate\Database\Eloquent\Relations\BelongsTo
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
- StoreProjectRequest
- setup
- Comment
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- TaskManagementController
- AuditLog
- Closure
- ImportBatch
- Illuminate\Http\Request
- Illuminate\Database\Seeder
- Role
- NotificationSetting
- Document
- Illuminate\Http\RedirectResponse
- AuditEventNotifier
- Department
- config
- UserManagementController
- Subtask
- ImportController.php
- Illuminate\Support\Collection
- UpdateTaskStatusColorsRequest
- require
- CompanyRoleRules
- extra
- LoginRequest
- StoreDepartmentRequest
- psr-4
- CalendarController.php
- UpdateTaskPriorityColorsRequest
- test

## God Nodes (most connected - your core abstractions)
1. `User` - 166 edges
2. `Organization` - 92 edges
3. `Task` - 64 edges
4. `OrgMember` - 58 edges
5. `Project` - 46 edges
6. `Role` - 45 edges
7. `Department` - 44 edges
8. `ImportValidator` - 33 edges
9. `AuditLog` - 31 edges
10. `ImportBatch` - 31 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (154 total, 25 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.15
Nodes (4): ImportRow, DuplicateDetector, ImportValidationContext, ImportValidator

### Community 3 - "Project"
Cohesion: 0.10
Nodes (6): Project, ProjectPolicy, Illuminate\Database\Eloquent\Relations\BelongsToMany, makeTaskForAnalytics(), makeProjectMember(), makeTaskForStartDateTest()

### Community 4 - "Organization"
Cohesion: 0.11
Nodes (4): AnalyticsController, DepartmentManagementController, DocumentController, Organization

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

### Community 14 - "TaskStatus.php"
Cohesion: 0.09
Nodes (10): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), isAssignableStaffForProject(), StoreTaskRequest (+2 more)

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
Nodes (11): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet (+3 more)

### Community 86 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 87 - "User"
Cohesion: 0.08
Nodes (10): User, AuditLogPolicy, DocumentPolicy, NotificationSettingPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+2 more)

### Community 88 - "Role.php"
Cohesion: 0.06
Nodes (17): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), OrgMember, TaskPriorityColor (+9 more)

### Community 90 - "Task"
Cohesion: 0.14
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Comment"
Cohesion: 0.25
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 107 - "Illuminate\View\View"
Cohesion: 0.15
Nodes (6): KanbanController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "AuditLog"
Cohesion: 0.13
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 110 - "Closure"
Cohesion: 0.13
Nodes (8): EnsureBelongsToOrganization, EnsureUserIsActive, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (7): ImportBatch, CompanyRoleSyncer, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, ImportCommitSummary, ImportIdCodec

### Community 112 - "Illuminate\Http\Request"
Cohesion: 0.20
Nodes (5): AuditTrailController, DashboardController, Collection, PermissionManagementController, Illuminate\Http\Request

### Community 113 - "Illuminate\Database\Seeder"
Cohesion: 0.15
Nodes (7): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 114 - "Role"
Cohesion: 0.18
Nodes (6): Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeClientForDocumentList(), makeClientForDocuments(), makeClientOnProject()

### Community 116 - "Document"
Cohesion: 0.15
Nodes (7): CommentController, SubtaskController, TaskDocumentController, Document, Illuminate\Http\JsonResponse, makeDocumentForList(), makeDocument()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (6): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, TaskColorController, Illuminate\Http\RedirectResponse

### Community 118 - "AuditEventNotifier"
Cohesion: 0.18
Nodes (4): AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 119 - "Department"
Cohesion: 0.13
Nodes (8): AccessPermission, Department, DepartmentPolicy, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 124 - "Subtask"
Cohesion: 0.16
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 141 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 143 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

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

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Project`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `LoginRequest`, `ImportTemplateBuilder`, `Role.php`, `Task`, `CommentPolicy`, `AuditLog`, `ImportBatch`, `Illuminate\Http\Request`, `Role`, `NotificationSetting`, `Document`, `Illuminate\Http\RedirectResponse`, `AuditEventNotifier`, `Department`, `UserManagementController`, `Subtask`?**
  _High betweenness centrality (0.124) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Project`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `CompanyRoleRules`, `StoreDepartmentRequest`, `CalendarController.php`, `ImportTemplateBuilder`, `User`, `Role.php`, `Illuminate\View\View`, `TaskManagementController`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Seeder`, `Role`, `Document`, `Illuminate\Http\RedirectResponse`, `Department`, `UserManagementController`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportBatch`, `Illuminate\Http\Request`, `AuditEventNotifier`, `Role.php`, `Task`, `Subtask`?**
  _High betweenness centrality (0.037) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 18 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._