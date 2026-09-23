# Graph Report - ProjectManagementTool  (2026-09-23)

## Corpus Check
- 331 files · ~125,094 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1402 nodes · 3376 edges · 185 communities (143 shown, 42 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 220 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6611f8a4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Department
- TaskManagementController.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- NotificationEventType.php
- LARAVEL_README.md
- AppServiceProvider.php
- OrgMember
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
- Illuminate\Database\Eloquent\Model
- StoreProjectRequest
- Role
- Organization
- setup
- documents/create.blade.php
- FileCategory.php
- UpdateProjectRequest
- _form.blade.php
- BootstrapEnvironment
- CommentPolicy
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- AuditEventMailNotification.php
- RichText
- CalendarController.php
- Illuminate\Database\Eloquent\Relations\HasMany
- keywords
- Illuminate\Support\Collection
- Illuminate\View\View
- config
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Foundation\Http\FormRequest
- Department.php
- require
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- Comment
- Illuminate\Validation\Validator
- Closure
- Subtask
- UserManagementController
- NotificationSetting
- static
- LoginRequest
- AuditEventNotifier
- DocumentPolicy.php
- AuditLog
- Illuminate\Http\Request
- UpdateDepartmentRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- TaskManagementController
- OrganizationPolicy
- ProjectPolicy
- ValidatesTaskAssignment.php
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- AuditLogPolicy.php
- TagsImportBatch.php
- post-create-project-cmd

## God Nodes (most connected - your core abstractions)
1. `User` - 189 edges
2. `Organization` - 102 edges
3. `Task` - 79 edges
4. `OrgMember` - 70 edges
5. `Project` - 57 edges
6. `Role` - 48 edges
7. `Department` - 45 edges
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

## Communities (185 total, 42 thin omitted)

### Community 0 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.22
Nodes (5): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "Department"
Cohesion: 0.09
Nodes (12): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, DepartmentSeeder, UserSeeder, makeStaffOnCalendar(), makeStaffOnDashboard() (+4 more)

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

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "OrgMember"
Cohesion: 0.16
Nodes (4): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), joinOrg()

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._description-field, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.09
Nodes (36): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+28 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.23
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 86 - "Task"
Cohesion: 0.16
Nodes (8): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), taskUpdatePayload()

### Community 87 - "User"
Cohesion: 0.09
Nodes (11): isAssignableStaffForProject(), User, DepartmentPolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, makeProjectMember() (+3 more)

### Community 88 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.09
Nodes (4): TaskPriorityColor, TaskStatusColor, Illuminate\Contracts\Validation\Validator, Illuminate\Database\Eloquent\Model

### Community 91 - "Role"
Cohesion: 0.24
Nodes (3): RoleManagementController, Role, RolePolicy

### Community 92 - "Organization"
Cohesion: 0.13
Nodes (13): Organization, Project, makeTaskForAnalytics(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument() (+5 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.06
Nodes (29): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument (+21 more)

### Community 105 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.13
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, ImageDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (9): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon (+1 more)

### Community 112 - "AuditEventMailNotification.php"
Cohesion: 0.36
Nodes (4): AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage

### Community 113 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 116 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 118 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (7): resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 119 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (11): AccessControlController, AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, NotificationController, OrganizationManagementController (+3 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "Closure"
Cohesion: 0.32
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 156 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 158 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 159 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 166 - "AuditLog"
Cohesion: 0.19
Nodes (3): AuditLog, AuditEventDatabaseNotification, Illuminate\Notifications\Notification

### Community 167 - "Illuminate\Http\Request"
Cohesion: 0.18
Nodes (7): AuditTrailController, RichTextAudioController, RichTextImageController, TaskDocumentController, Document, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 182 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 183 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportValidator`, `.boardOrganizationIds`, `Department`, `Illuminate\Http\RedirectResponse`, `Department.php`, `NotificationEventType.php`, `Illuminate\Database\Eloquent\Builder`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `ImportCommitService.php`, `Subtask`, `UserManagementController`, `NotificationSetting`, `LoginRequest`, `AuditEventNotifier`, `DocumentPolicy.php`, `AuditLog`, `Illuminate\Http\Request`, `TaskManagementController`, `OrganizationPolicy`, `ProjectPolicy`, `AuditLogPolicy.php`, `Role`, `Organization`, `FileCategory.php`, `UpdateProjectRequest`, `BootstrapEnvironment`, `CommentPolicy`, `ImportBatch`, `RichText`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.172) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Department`, `Illuminate\Http\RedirectResponse`, `Illuminate\Foundation\Http\FormRequest`, `TaskManagementController.php`, `Department.php`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `Illuminate\Http\Request`, `TaskManagementController`, `OrganizationPolicy`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Model`, `ImportBatch`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportValidator`, `Department`, `TaskManagementController.php`, `Illuminate\Http\RedirectResponse`, `Department.php`, `Illuminate\Database\Eloquent\Builder`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportCommitService.php`, `Subtask`, `Illuminate\Http\Request`, `TaskManagementController`, `ValidatesTaskAssignment.php`, `TagsImportBatch.php`, `User`, `Illuminate\Database\Eloquent\Model`, `Organization`, `ImportBatch`, `RichText`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 7 inferred relationships involving `Project` (e.g. with `.storePending()` and `.storePending()`) actually correct?**
  _`Project` has 7 INFERRED edges - model-reasoned connections that need verification._