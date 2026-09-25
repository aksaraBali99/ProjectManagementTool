# Graph Report - ProjectManagementTool  (2026-09-25)

## Corpus Check
- 370 files · ~164,561 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1545 nodes · 3920 edges · 181 communities (147 shown, 34 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 248 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `36a3cb95`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- StoreDepartmentRequest
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Model
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- FileCategory.php
- LARAVEL_README.md
- AppServiceProvider.php
- Role.php
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
- ValidatesTaskAssignment.php
- User
- Project
- NotificationSetting
- Document
- OrgMember
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Http\Request
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- TaskManagementController.php
- Comment
- Illuminate\Http\JsonResponse
- Illuminate\Http\UploadedFile
- CalendarController.php
- AuditEventNotifier
- TaskManagementController
- config
- NotificationEventType.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- AuditLog
- require
- DepartmentManagementController.php
- TagsImportBatch.php
- psr-4
- Task
- Illuminate\Database\Eloquent\Builder
- Illuminate\Validation\Validator
- Organization
- HasAdminConfigurableColors.php
- UpdateProjectRequest
- static
- UserManagementController
- StoreProjectRequest
- UpdateTaskStatusColorsRequest
- Subtask
- LoginRequest
- UpdateTaskPriorityColorsRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- post-create-project-cmd
- Illuminate\Foundation\Http\FormRequest
- keywords
- Illuminate\Database\Eloquent\Relations\BelongsToMany

## God Nodes (most connected - your core abstractions)
1. `User` - 213 edges
2. `Organization` - 120 edges
3. `Task` - 102 edges
4. `OrgMember` - 98 edges
5. `Project` - 71 edges
6. `Role` - 61 edges
7. `Department` - 56 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeEligibleStaffMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php
- `makeIneligibleProjectMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php
- `makeProjectMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (181 total, 34 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (7): organization(), ImportRow, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "FileCategory.php"
Cohesion: 0.20
Nodes (6): PastedMediaNamer, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse, fileFor(), FileCategory, UploadedFile

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
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "ValidatesTaskAssignment.php"
Cohesion: 0.15
Nodes (3): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 87 - "User"
Cohesion: 0.07
Nodes (11): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, SubtaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+3 more)

### Community 88 - "Project"
Cohesion: 0.10
Nodes (15): Project, Role, ProjectPolicy, RolePolicy, makeTaskForAnalytics(), makeClientWithProjectAccessForDownload(), toggleStaffManageDocuments(), makeClientWithProjectAccessForAudio() (+7 more)

### Community 90 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 91 - "Document"
Cohesion: 0.19
Nodes (4): TaskDocumentController, Document, DocumentPolicy, uploadDocumentForDownload()

### Community 92 - "OrgMember"
Cohesion: 0.11
Nodes (16): AccessPermission, Department, OrgMember, UserSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember() (+8 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "Illuminate\Http\Request"
Cohesion: 0.33
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.11
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, DocumentController, NotificationController (+5 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (8): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 111 - "ImportBatch"
Cohesion: 0.13
Nodes (9): ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver (+1 more)

### Community 112 - "TaskManagementController.php"
Cohesion: 0.08
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.13
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (6): LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, Illuminate\Http\JsonResponse

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.06
Nodes (30): FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase (+22 more)

### Community 116 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.10
Nodes (11): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "Organization"
Cohesion: 0.09
Nodes (10): AnalyticsController, Organization, OrganizationPolicy, makeStaffForDocumentCreate(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments() (+2 more)

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 158 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 165 - "Subtask"
Cohesion: 0.23
Nodes (3): SubtaskController, Subtask, SubtaskObserver

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **34 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Task.php`, `User.php`, `Role.php`, `Task`, `Illuminate\Database\Eloquent\Builder`, `Organization`, `UpdateProjectRequest`, `UserManagementController`, `LoginRequest`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ValidatesTaskAssignment.php`, `Project`, `NotificationSetting`, `Document`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `TaskManagementController.php`, `Comment`, `Illuminate\Http\UploadedFile`, `AuditEventNotifier`, `TaskManagementController`, `NotificationEventType.php`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `StoreDepartmentRequest`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Task.php`, `FileCategory.php`, `User.php`, `Role.php`, `DepartmentManagementController.php`, `Illuminate\Validation\Validator`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `TaskManagementController.php`, `CalendarController.php`, `TaskManagementController`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Task.php`, `FileCategory.php`, `Role.php`, `Illuminate\Database\Eloquent\Builder`, `Organization`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ValidatesTaskAssignment.php`, `User`, `Project`, `Document`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `TaskManagementController.php`, `Comment`, `Illuminate\Http\JsonResponse`, `CalendarController.php`, `TaskManagementController`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._