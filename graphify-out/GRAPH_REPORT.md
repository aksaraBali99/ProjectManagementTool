# Graph Report - ProjectManagementTool  (2026-09-18)

## Corpus Check
- 300 files · ~101,861 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1222 nodes · 2945 edges · 172 communities (134 shown, 38 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 196 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `54bb590d`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
- AuditLog
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
- ProjectManagementController
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
- .__invoke
- User
- App\Models\User
- Illuminate\Http\JsonResponse
- CommentPolicy
- Illuminate\View\View
- setup
- documents/create.blade.php
- Subtask
- self
- _form.blade.php
- Role
- Task
- TaskManagementController
- Illuminate\Http\Request
- ImportBatch
- UpdateUserRequest
- Controller
- Comment
- UpdateTaskRequest
- UserManagementController
- Document
- Project
- config
- Organization
- StoreUserRequest
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Foundation\Http\FormRequest
- StoreTaskRequest
- require
- NotificationDeliveryTest.php
- App\Models\AuditLog
- psr-4
- BootstrapEnvironment
- test
- Illuminate\Validation\Validator
- Illuminate\Database\Eloquent\Model
- UserManagementController.php
- Closure
- UpdateDepartmentRequest
- UpdateTaskStatusColorsRequest
- MentionedInCommentNotification
- NotificationSettingPolicy
- Department.php
- UpdateTaskPriorityColorsRequest
- PermissionManagementController.php
- AuditLogPolicy.php
- extra

## God Nodes (most connected - your core abstractions)
1. `User` - 175 edges
2. `Organization` - 96 edges
3. `Task` - 66 edges
4. `OrgMember` - 59 edges
5. `Project` - 49 edges
6. `Role` - 45 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `ImportBatch` - 32 edges
10. `ImportCommitService` - 27 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (172 total, 38 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "AuditLog"
Cohesion: 0.12
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (6): AccessPermission, organization(), UserSeeder, Illuminate\Database\Eloquent\Relations\BelongsTo, makeStaffOnCalendar(), makeStaffOnKanban()

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

### Community 86 - ".__invoke"
Cohesion: 0.62
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 87 - "User"
Cohesion: 0.09
Nodes (6): User, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Foundation\Auth\User

### Community 88 - "App\Models\User"
Cohesion: 0.13
Nodes (5): OrgMember, App\Models\User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Notifications\Notifiable, makeStaffWithDepartmentAccess()

### Community 90 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): CommentController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 92 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (6): DepartmentManagementController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Subtask"
Cohesion: 0.18
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 105 - "self"
Cohesion: 0.19
Nodes (7): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), values(), self

### Community 107 - "Role"
Cohesion: 0.14
Nodes (6): Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffForDocumentCreate(), joinOrg(), makeClientOnProject()

### Community 108 - "Task"
Cohesion: 0.20
Nodes (4): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes

### Community 110 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (6): AuditTrailController, DashboardController, Collection, ImportController, Illuminate\Http\Request, Symfony\Component\HttpFoundation\StreamedResponse

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 114 - "Controller"
Cohesion: 0.28
Nodes (4): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller

### Community 115 - "Comment"
Cohesion: 0.26
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 118 - "Document"
Cohesion: 0.13
Nodes (9): TaskDocumentController, Document, DocumentPolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument() (+1 more)

### Community 119 - "Project"
Cohesion: 0.17
Nodes (7): Department, Project, makeTaskForAnalytics(), makeProjectMember(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeTaskForStartDateTest()

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 122 - "Organization"
Cohesion: 0.16
Nodes (7): AccessControlController, resolveCurrentOrganization(), DocumentController, KanbanController, Organization, Illuminate\Support\Collection, flattenCalendarCells()

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (4): NotificationSettingsController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 139 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 145 - "App\Models\AuditLog"
Cohesion: 0.17
Nodes (8): App\Enums\NotificationEventType, App\Models\AuditLog, App\Models\NotificationSetting, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, NotificationSetting

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
Cohesion: 0.19
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.31
Nodes (4): ImportRow, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 157 - "Closure"
Cohesion: 0.06
Nodes (17): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, StoreProjectRequest, UpdateProjectRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins() (+9 more)

### Community 173 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **114 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+109 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `AuditLog`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ProjectManagementController`, `ImportCommitService.php`, `App\Models\AuditLog`, `BootstrapEnvironment`, `UserManagementController.php`, `Closure`, `NotificationSettingPolicy`, `Department.php`, `AuditLogPolicy.php`, `ImportTemplateBuilder`, `App\Models\User`, `Illuminate\Http\JsonResponse`, `CommentPolicy`, `Subtask`, `Role`, `Task`, `TaskManagementController`, `Illuminate\Http\Request`, `ImportBatch`, `Controller`, `UserManagementController`, `Document`, `Project`, `Organization`?**
  _High betweenness centrality (0.162) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Database\Seeder`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `StoreTaskRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ProjectManagementController`, `ImportCommitService.php`, `Illuminate\Validation\Validator`, `Illuminate\Database\Eloquent\Model`, `Department.php`, `ImportTemplateBuilder`, `.__invoke`, `User`, `App\Models\User`, `Illuminate\View\View`, `self`, `Role`, `TaskManagementController`, `Illuminate\Http\Request`, `ImportBatch`, `Controller`, `Document`, `Project`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Department` connect `Project` to `Illuminate\Database\Seeder`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Department.php`, `StoreTaskRequest`, `TaskManagementController`, `ImportBatch`, `ImportTemplateBuilder`, `UpdateTaskRequest`, `UserManagementController`, `User`, `App\Models\User`, `Organization`, `Illuminate\Database\Eloquent\Model`, `Illuminate\View\View`, `Closure`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` to the rest of the system?**
  _114 weakly-connected nodes found - possible documentation gaps or missing edges._