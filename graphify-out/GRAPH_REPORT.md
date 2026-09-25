# Graph Report - ProjectManagementTool  (2026-09-25)

## Corpus Check
- 365 files · ~158,476 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1529 nodes · 3826 edges · 186 communities (150 shown, 36 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 237 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2bef9ac6`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Department
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- web.php
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
- RichText
- User
- Project
- NotificationSetting
- Illuminate\Database\Eloquent\Model
- OrgMember
- setup
- documents/create.blade.php
- Role
- auth.php
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Illuminate\Http\JsonResponse
- Illuminate\Http\Request
- Pest.php
- AuditLog
- FileCategory.php
- TaskManagementController
- config
- NotificationEventType.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- AuditEventMailNotification.php
- require
- Document
- Comment
- psr-4
- Task
- Illuminate\Http\UploadedFile
- Illuminate\Validation\Validator
- Organization
- HasAdminConfigurableColors.php
- Closure
- CompanyRoleRules
- UserManagementController
- FileStorageException
- TaskObserver
- Subtask
- LoginRequest
- FileStorageService
- UpdateRoleRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- post-create-project-cmd
- Illuminate\Foundation\Http\FormRequest
- StoreProjectRequest
- CalendarController.php
- config
- keywords
- .storePending
- .storePending
- Illuminate\Database\Eloquent\Relations\BelongsToMany

## God Nodes (most connected - your core abstractions)
1. `User` - 205 edges
2. `Organization` - 113 edges
3. `Task` - 100 edges
4. `OrgMember` - 89 edges
5. `Project` - 68 edges
6. `Role` - 56 edges
7. `Department` - 47 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientWithProjectAccessForDownload()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentDownloadTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (186 total, 36 thin omitted)

### Community 0 - "Department"
Cohesion: 0.09
Nodes (7): DepartmentManagementController, StoreDepartmentRequest, Department, DepartmentPolicy, makeStaffOnDashboard(), makeTaskOnDashboard(), makeTaskForStartDateTest()

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (4): organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.05
Nodes (54): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+46 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.17
Nodes (8): ImportController, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "RichText"
Cohesion: 0.09
Nodes (5): StoreTaskRequest, UpdateTaskRequest, RichText, Illuminate\Contracts\Validation\Validator, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.06
Nodes (13): User, AuditLogPolicy, CommentPolicy, DocumentPolicy, OrganizationPolicy, SubtaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Builder (+5 more)

### Community 88 - "Project"
Cohesion: 0.14
Nodes (7): isAssignableStaffForProject(), Project, ProjectPolicy, makeTaskForAnalytics(), makeProjectMember(), makeClientWithProjectAccessForDownload(), makeClientWithProjectAccessForLinkPreview()

### Community 90 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 91 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.09
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 92 - "OrgMember"
Cohesion: 0.16
Nodes (6): OrgMember, makeStaffForDocumentCreate(), makeClientWithProjectAccessForAudio(), makeClientWithProjectAccessForDescription(), joinOrg(), makeClientOnProject()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Role"
Cohesion: 0.10
Nodes (12): AnalyticsController, AccessPermission, Role, RolePolicy, makeStaffOnCalendar(), toggleStaffManageDocuments(), makeStaffForKanbanAssignee(), makeStaffOnKanban() (+4 more)

### Community 105 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.14
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController, RoleManagementController (+3 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.19
Nodes (6): staffOptionsByProject(), resolveCurrentOrganization(), KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportBatch, CompanyRoleSyncer, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Http\JsonResponse"
Cohesion: 0.22
Nodes (4): CommentController, RichTextAudioController, SubtaskController, Illuminate\Http\JsonResponse

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.20
Nodes (5): DashboardController, Collection, LinkPreviewController, RichTextDocumentController, Illuminate\Http\Request

### Community 115 - "Pest.php"
Cohesion: 0.13
Nodes (13): DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase, buildImportTestFile(), createOwner(), richTextEditorContent(), richTextEditorNode() (+5 more)

### Community 116 - "AuditLog"
Cohesion: 0.21
Nodes (3): AuditLog, AuditEventNotifier, NotificationEventType

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.16
Nodes (3): AccessControlController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 134 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Document"
Cohesion: 0.19
Nodes (5): DocumentController, TaskDocumentController, Document, Symfony\Component\HttpFoundation\StreamedResponse, uploadDocumentForDownload()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.12
Nodes (10): Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 149 - "Illuminate\Http\UploadedFile"
Cohesion: 0.14
Nodes (15): Illuminate\Http\UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 155 - "Organization"
Cohesion: 0.08
Nodes (8): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "Closure"
Cohesion: 0.18
Nodes (6): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 158 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 160 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 164 - "TaskObserver"
Cohesion: 0.24
Nodes (4): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), TaskObserver

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 178 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **36 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Department`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Project.php`, `web.php`, `User.php`, `Department.php`, `Document`, `Task`, `Organization`, `Closure`, `UserManagementController`, `LoginRequest`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `NotificationSetting`, `OrgMember`, `Role`, `BootstrapEnvironment`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Illuminate\Http\JsonResponse`, `Illuminate\Http\Request`, `Pest.php`, `AuditLog`, `NotificationEventType.php`?**
  _High betweenness centrality (0.130) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Department`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `web.php`, `User.php`, `Department.php`, `Document`, `CompanyRoleRules`, `Illuminate\Foundation\Http\FormRequest`, `CalendarController.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `Project`, `Illuminate\Database\Eloquent\Model`, `OrgMember`, `Role`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Department`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `web.php`, `User.php`, `Department.php`, `Document`, `Organization`, `TaskObserver`, `CalendarController.php`, `.storePending`, `.storePending`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `User`, `Project`, `Illuminate\Database\Eloquent\Model`, `Role`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Illuminate\Http\JsonResponse`, `Illuminate\Http\Request`, `FileCategory.php`, `TaskManagementController`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._