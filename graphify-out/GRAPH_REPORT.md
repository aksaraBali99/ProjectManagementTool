# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 285 files · ~93,673 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1157 nodes · 2838 edges · 157 communities (132 shown, 25 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 184 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `736178b9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UpdateUserRequest
- ImportValidator
- .boardOrganizationIds
- NotificationEventType.php
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
- Illuminate\Validation\Validator
- User
- Task
- Illuminate\Foundation\Http\FormRequest
- keywords
- setup
- Subtask
- CommentPolicy
- _form.blade.php
- Illuminate\View\View
- TaskManagementController.php
- Illuminate\Database\Eloquent\Model
- static
- ImportBatch
- Illuminate\Database\Seeder
- AuditLog
- Document
- Illuminate\Http\RedirectResponse
- Comment
- config
- UserManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Support\Collection
- AuditEventNotifier
- require
- Closure
- LoginRequest
- ValidatesTaskAssignment.php
- psr-4
- CalendarController.php
- UpdateTaskPriorityColorsRequest
- test
- StoreDepartmentRequest
- OrgMember
- ImportCommitService.php
- UpdateTaskStatusColorsRequest
- Illuminate\Http\Request
- UpdateRoleRequest
- NotificationSettingPolicy

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

## Communities (157 total, 25 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (6): ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Role"
Cohesion: 0.12
Nodes (6): PermissionManagementController, RoleManagementController, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeClientForDocuments()

### Community 5 - "Organization"
Cohesion: 0.06
Nodes (18): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), AccessPermission, Department (+10 more)

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

### Community 14 - "Project"
Cohesion: 0.15
Nodes (6): isAssignableStaffForProject(), Project, ProjectPolicy, makeProjectMember(), makeClientForDocumentList(), makeClientOnProject()

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
Nodes (8): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 90 - "Task"
Cohesion: 0.13
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 92 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.20
Nodes (4): SubtaskController, Subtask, SubtaskPolicy, Illuminate\Http\JsonResponse

### Community 107 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (10): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, KanbanController, NotificationController, OrganizationManagementController (+2 more)

### Community 109 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.21
Nodes (4): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Notification

### Community 110 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.13
Nodes (9): AbandonStaleImportBatches, ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, ImportCommitSummary, Illuminate\Console\Command (+1 more)

### Community 113 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 115 - "AuditLog"
Cohesion: 0.15
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 116 - "Document"
Cohesion: 0.18
Nodes (5): TaskDocumentController, Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (6): AccessControlController, GoogleAuthController, NotificationSettingsController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 118 - "Comment"
Cohesion: 0.24
Nodes (3): CommentController, Comment, CommentObserver

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 134 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (4): ProjectManagementController, StoreProjectRequest, UpdateProjectRequest, Illuminate\Support\Collection

### Community 139 - "AuditEventNotifier"
Cohesion: 0.16
Nodes (6): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver, AuditEventNotifier, NotificationEventType

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 141 - "Closure"
Cohesion: 0.22
Nodes (7): EnsureBelongsToOrganization, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, Closure, Illuminate\Contracts\Validation\ValidationRule, Symfony\Component\HttpFoundation\Response

### Community 145 - "ValidatesTaskAssignment.php"
Cohesion: 0.17
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

### Community 154 - "OrgMember"
Cohesion: 0.16
Nodes (5): OrgMember, makeStaffForDocumentCreate(), makeStaffForDocumentList(), makeStaffForDocuments(), joinOrg()

### Community 161 - "Illuminate\Http\Request"
Cohesion: 0.22
Nodes (4): DashboardController, Collection, DocumentController, Illuminate\Http\Request

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **25 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `NotificationEventType.php`, `Role`, `Organization`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditEventNotifier`, `Project`, `LoginRequest`, `OrgMember`, `ImportCommitService.php`, `Illuminate\Http\Request`, `NotificationSettingPolicy`, `ImportTemplateBuilder`, `Role.php`, `Task`, `Subtask`, `CommentPolicy`, `Illuminate\View\View`, `ImportBatch`, `AuditLog`, `Document`, `Illuminate\Http\RedirectResponse`, `Comment`, `Task.php`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Role`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `CalendarController.php`, `StoreDepartmentRequest`, `OrgMember`, `ImportCommitService.php`, `Illuminate\Http\Request`, `ImportTemplateBuilder`, `Illuminate\Validation\Validator`, `User`, `Role.php`, `Illuminate\View\View`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Model`, `ImportBatch`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `UserManagementController`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\Request`, `ImportValidator`, `Role`, `Subtask`, `Organization`, `Illuminate\View\View`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `AuditEventNotifier`, `ImportBatch`, `CalendarController.php`, `Document`, `Comment`, `Task.php`, `Role.php`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 18 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._