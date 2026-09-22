# Graph Report - ProjectManagementTool  (2026-09-22)

## Corpus Check
- 322 files · ~114,588 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1356 nodes · 3238 edges · 176 communities (143 shown, 33 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 216 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d9b84237`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Illuminate\Support\Collection
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Project
- LARAVEL_README.md
- AppServiceProvider.php
- Comment
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- tasks/index.blade.php
- CLAUDE.md
- copilot-instructions.md
- rich-text-editor.js
- _password-input.blade.php
- ImportTemplateBuilder
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- Task
- User
- Role
- Illuminate\View\View
- AuditEventMailNotification.php
- UserManagementController
- setup
- documents/create.blade.php
- FileCategory.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- _form.blade.php
- RichText
- Illuminate\Database\Eloquent\Relations\BelongsTo
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- StoreTaskRequest
- CalendarController.php
- Subtask
- Illuminate\Http\JsonResponse
- auth.php
- NotificationSetting
- Illuminate\Http\RedirectResponse
- config
- Organization
- task-colors/edit.blade.php
- Document
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- require
- Illuminate\Database\Eloquent\Builder
- Department
- AuditLog
- psr-4
- BootstrapEnvironment
- ImportController.php
- Illuminate\Validation\Validator
- Illuminate\Database\Eloquent\Model
- StoreDepartmentRequest
- ImportCommitService.php
- Closure
- StoreProjectRequest
- TaskManagementController
- CompanyRoleRules
- Department.php
- UpdateProjectRequest
- post-create-project-cmd
- UpdateUserRequest
- autoload-dev
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php

## God Nodes (most connected - your core abstractions)
1. `User` - 185 edges
2. `Organization` - 98 edges
3. `Task` - 72 edges
4. `OrgMember` - 64 edges
5. `Project` - 52 edges
6. `Role` - 46 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (176 total, 33 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.27
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Support\Collection"
Cohesion: 0.18
Nodes (5): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 9 - "dependencies"
Cohesion: 0.05
Nodes (39): concurrently, @fontsource/inter, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies, chart.js (+31 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Project"
Cohesion: 0.20
Nodes (3): Project, ProjectPolicy, makeProjectMember()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Comment"
Cohesion: 0.18
Nodes (6): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), CommentPolicy

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.40
Nodes (4): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (23): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+15 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "Task"
Cohesion: 0.17
Nodes (6): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeTaskWithDescription(), taskUpdatePayload()

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, AuditLogPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 88 - "Role"
Cohesion: 0.12
Nodes (15): AccessPermission, Role, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeClientForDocumentList(), makeStaffForDocumentList() (+7 more)

### Community 90 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (10): AnalyticsController, AuditTrailController, GoogleAuthController, Controller, DepartmentManagementController, NotificationController, PermissionManagementController, RoleManagementController (+2 more)

### Community 91 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.07
Nodes (27): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument (+19 more)

### Community 107 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 108 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.38
Nodes (3): CodeLanguageClassSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 113 - "StoreTaskRequest"
Cohesion: 0.14
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 115 - "Subtask"
Cohesion: 0.19
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 116 - "Illuminate\Http\JsonResponse"
Cohesion: 0.20
Nodes (5): RichTextImageController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 117 - "auth.php"
Cohesion: 0.26
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 118 - "NotificationSetting"
Cohesion: 0.14
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 119 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.16
Nodes (5): AuthenticatedSessionController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Organization"
Cohesion: 0.11
Nodes (6): AccessControlController, resolveCurrentOrganization(), DocumentController, KanbanController, OrganizationManagementController, Organization

### Community 133 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Department"
Cohesion: 0.15
Nodes (4): Department, DepartmentPolicy, makeTaskForAnalytics(), makeTaskForStartDateTest()

### Community 145 - "AuditLog"
Cohesion: 0.13
Nodes (4): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.14
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingImages, Illuminate\Console\Command

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 155 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.23
Nodes (5): Permission, TaskPriorityColor, TaskStatusColor, PermissionSeeder, Illuminate\Database\Eloquent\Model

### Community 159 - "Closure"
Cohesion: 0.25
Nodes (5): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 165 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 168 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 170 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

## Knowledge Gaps
- **127 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+122 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **33 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `LoginRequest`, `Project`, `Illuminate\Database\Eloquent\Builder`, `Comment`, `Department`, `AuditLog`, `BootstrapEnvironment`, `ImportCommitService.php`, `Closure`, `TaskManagementController`, `Department.php`, `ImportTemplateBuilder`, `Task`, `Role`, `Illuminate\View\View`, `UserManagementController`, `FileCategory.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportBatch`, `Task.php`, `Subtask`, `Illuminate\Http\JsonResponse`, `NotificationSetting`, `Illuminate\Http\RedirectResponse`, `Organization`?**
  _High betweenness centrality (0.179) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `Department`, `Illuminate\Database\Eloquent\Model`, `StoreDepartmentRequest`, `ImportCommitService.php`, `TaskManagementController`, `CompanyRoleRules`, `Department.php`, `ImportTemplateBuilder`, `User`, `Role`, `Illuminate\View\View`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportBatch`, `Task.php`, `CalendarController.php`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `OrgMember`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Builder`, `Department`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `Department.php`, `Role`, `Illuminate\View\View`, `AuditEventMailNotification.php`, `FileCategory.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportBatch`, `Task.php`, `CalendarController.php`, `Subtask`, `Illuminate\Http\JsonResponse`, `Illuminate\Http\RedirectResponse`, `Organization`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _127 weakly-connected nodes found - possible documentation gaps or missing edges._