# Graph Report - ProjectManagementTool  (2026-09-22)

## Corpus Check
- 323 files · ~118,097 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1375 nodes · 3283 edges · 180 communities (145 shown, 35 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 217 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `faee0e80`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
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
- Illuminate\Http\Request
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
- Department
- User
- Role.php
- OrgMember
- UserManagementController
- setup
- documents/create.blade.php
- FileCategory.php
- NotificationSetting
- _form.blade.php
- ValidatesTaskAssignment.php
- Comment
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- RichText
- CalendarController.php
- UpdateProjectRequest
- Subtask
- NotificationEventType.php
- StoreDepartmentRequest
- Illuminate\View\View
- config
- Organization
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- require
- Role
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- AuditLog
- psr-4
- BootstrapEnvironment
- Illuminate\Http\JsonResponse
- Illuminate\Validation\Validator
- UpdateTaskStatusColorsRequest
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ValidPhoneNumber.php
- AuditEventNotifier
- TaskManagementController.php
- static
- Task.php
- Document
- post-create-project-cmd
- UpdateUserRequest
- UpdateDepartmentRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- StoreProjectRequest
- UpdateTaskPriorityColorsRequest
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 185 edges
2. `Organization` - 98 edges
3. `Task` - 74 edges
4. `OrgMember` - 65 edges
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
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (180 total, 35 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Support\Collection"
Cohesion: 0.22
Nodes (5): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 9 - "dependencies"
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Project"
Cohesion: 0.18
Nodes (4): Project, ProjectPolicy, makeTaskForAnalytics(), makeProjectMember()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Illuminate\Http\Request"
Cohesion: 0.33
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

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
Cohesion: 0.10
Nodes (33): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+25 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "Department"
Cohesion: 0.16
Nodes (3): Department, DepartmentPolicy, makeTaskForStartDateTest()

### Community 87 - "User"
Cohesion: 0.10
Nodes (9): User, AuditLogPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask() (+1 more)

### Community 90 - "OrgMember"
Cohesion: 0.17
Nodes (7): OrgMember, CompanyRoleSyncer, makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeClientWithProjectAccess(), joinOrg(), makeClientOnProject()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.07
Nodes (27): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument (+19 more)

### Community 105 - "NotificationSetting"
Cohesion: 0.18
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 107 - "ValidatesTaskAssignment.php"
Cohesion: 0.12
Nodes (5): SubtaskController, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 108 - "Comment"
Cohesion: 0.22
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.38
Nodes (3): CodeLanguageClassSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (9): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon (+1 more)

### Community 113 - "RichText"
Cohesion: 0.15
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 116 - "Subtask"
Cohesion: 0.18
Nodes (6): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver, SubtaskPolicy

### Community 119 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (12): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Organization"
Cohesion: 0.14
Nodes (5): AccessControlController, resolveCurrentOrganization(), DocumentController, KanbanController, Organization

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Role"
Cohesion: 0.11
Nodes (8): AccessPermission, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeStaffWithDepartmentAccess()

### Community 145 - "AuditLog"
Cohesion: 0.15
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.18
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingImages, Illuminate\Console\Command

### Community 149 - "Illuminate\Http\JsonResponse"
Cohesion: 0.31
Nodes (3): RichTextImageController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Task"
Cohesion: 0.13
Nodes (8): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), makeTaskWithDescription(), taskUpdatePayload()

### Community 157 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 159 - "ValidPhoneNumber.php"
Cohesion: 0.25
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 165 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 166 - "Task.php"
Cohesion: 0.10
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 167 - "Document"
Cohesion: 0.15
Nodes (8): Document, DocumentPolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 168 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 179 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\Support\Collection`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `LoginRequest`, `Project`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditLog`, `BootstrapEnvironment`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ValidPhoneNumber.php`, `AuditEventNotifier`, `TaskManagementController.php`, `Task.php`, `Document`, `ImportTemplateBuilder`, `Department`, `Role.php`, `OrgMember`, `UserManagementController`, `FileCategory.php`, `NotificationSetting`, `ValidatesTaskAssignment.php`, `Comment`, `ImportBatch`, `RichText`, `Subtask`, `NotificationEventType.php`, `Illuminate\View\View`, `Organization`?**
  _High betweenness centrality (0.177) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `Illuminate\Support\Collection`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Project`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `TaskManagementController.php`, `Task.php`, `Document`, `ImportTemplateBuilder`, `Department`, `User`, `Role.php`, `OrgMember`, `UserManagementController`, `ImportBatch`, `CalendarController.php`, `StoreDepartmentRequest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Support\Collection`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Project`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `TaskManagementController.php`, `Task.php`, `Department`, `Role.php`, `OrgMember`, `FileCategory.php`, `ValidatesTaskAssignment.php`, `ImportBatch`, `ImportValidator.php`, `RichText`, `CalendarController.php`, `Subtask`, `Illuminate\View\View`, `Organization`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _132 weakly-connected nodes found - possible documentation gaps or missing edges._