# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 352 files · ~143,167 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1480 nodes · 3619 edges · 185 communities (139 shown, 46 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 230 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3f6bb325`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Builder
- TaskObserver
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- RichText
- LARAVEL_README.md
- AppServiceProvider.php
- Department.php
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
- StoreDepartmentRequest
- User
- AuditLog
- Illuminate\Database\Eloquent\Model
- ProjectStatus.php
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- Pest.php
- Closure
- _form.blade.php
- BootstrapEnvironment
- web.php
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- LinkPreview
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Http\Request
- CalendarController.php
- Illuminate\Support\Collection
- Document
- Illuminate\View\View
- config
- static
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- LoginRequest
- NotificationSetting
- require
- OrgMember
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- CommentPolicy
- psr-4
- Task
- Comment
- Illuminate\Validation\Validator
- UserManagementController
- AuditEventNotifier
- TaskPolicy
- UserPolicy
- TaskManagementController.php
- UpdateDepartmentRequest
- NotificationEventType.php
- StoreProjectRequest
- UpdateTaskPriorityColorsRequest
- StoreTaskRequest
- ProjectPolicy
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateTaskRequest
- .storePending
- Illuminate\Foundation\Http\FormRequest
- Subtask
- NotificationSettingPolicy
- keywords
- RolePolicy
- post-create-project-cmd

## God Nodes (most connected - your core abstractions)
1. `User` - 197 edges
2. `Organization` - 108 edges
3. `Task` - 93 edges
4. `OrgMember` - 78 edges
5. `Project` - 66 edges
6. `Role` - 51 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (185 total, 46 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.07
Nodes (30): AccessPermission, Department, Organization, Project, Role, UserSeeder, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics() (+22 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (8): ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (5): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "TaskObserver"
Cohesion: 0.24
Nodes (4): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), TaskObserver

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

### Community 11 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

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
Cohesion: 0.33
Nodes (5): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._description-field, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.06
Nodes (46): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+38 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.22
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 87 - "User"
Cohesion: 0.08
Nodes (12): isAssignableStaffForProject(), User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+4 more)

### Community 88 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 90 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (5): Permission, TaskPriorityColor, TaskStatusColor, PermissionSeeder, Illuminate\Database\Eloquent\Model

### Community 91 - "ProjectStatus.php"
Cohesion: 0.12
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Pest.php"
Cohesion: 0.06
Nodes (27): FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase (+19 more)

### Community 105 - "Closure"
Cohesion: 0.27
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.10
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Illuminate\Console\Command

### Community 108 - "web.php"
Cohesion: 0.32
Nodes (4): RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.16
Nodes (7): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, Symfony\Component\HttpFoundation\StreamedResponse

### Community 112 - "LinkPreview"
Cohesion: 0.14
Nodes (4): LinkPreview, LinkPreviewResult, LinkPreviewService, UrlSsrfGuard

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (7): LinkPreviewController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 115 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 116 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (7): resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 119 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (12): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, NotificationController, OrganizationManagementController (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): AccessControlController, DepartmentManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 139 - "NotificationSetting"
Cohesion: 0.24
Nodes (3): NotificationSettingsController, NotificationSetting, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.14
Nodes (9): TaskManagementController, Task, MentionedInCommentNotification, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), taskUpdatePayload() (+1 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.18
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **46 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `LoginRequest`, `RichText`, `NotificationSetting`, `OrgMember`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `Task`, `UserManagementController`, `AuditEventNotifier`, `TaskPolicy`, `UserPolicy`, `TaskManagementController.php`, `NotificationEventType.php`, `ProjectPolicy`, `Subtask`, `NotificationSettingPolicy`, `RolePolicy`, `AuditLog`, `Illuminate\Database\Eloquent\Model`, `ProjectStatus.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Pest.php`, `BootstrapEnvironment`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Document`, `Illuminate\View\View`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Builder`, `TaskObserver`, `RichText`, `OrgMember`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskPolicy`, `TaskManagementController.php`, `.storePending`, `Subtask`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Relations\HasMany`, `web.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\Request`, `CalendarController.php`, `Illuminate\Support\Collection`, `Document`, `Illuminate\View\View`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Task`, `Illuminate\Validation\Validator`, `UserManagementController`, `TaskManagementController.php`, `Illuminate\Foundation\Http\FormRequest`, `ImportTemplateBuilder`, `User`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ImportBatch`, `CalendarController.php`, `Illuminate\Support\Collection`, `Illuminate\View\View`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._