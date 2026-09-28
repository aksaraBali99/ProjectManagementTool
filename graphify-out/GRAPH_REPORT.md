# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 383 files · ~178,856 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1606 nodes · 4134 edges · 189 communities (148 shown, 41 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 262 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `af6a1be9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- FileCategory.php
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Department.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Http\Request
- LARAVEL_README.md
- AppServiceProvider.php
- RichText
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
- StoreTaskRequest
- User
- TaskManagementController
- ProjectManagementController
- Document
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- FileStorageException
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- Illuminate\Http\JsonResponse
- FileStorageService
- Illuminate\Http\UploadedFile
- .__invoke
- LoginRequest
- config
- CommentPolicy
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- AuditLog
- OrganizationPolicy
- require
- Role.php
- Illuminate\Foundation\Http\FormRequest
- TagsImportBatch.php
- psr-4
- Task
- Subtask
- Illuminate\Validation\Validator
- OrganizationManagementController.php
- StoreProjectRequest
- static
- UserManagementController
- TaskPolicy
- Priority.php
- UserPolicy
- findKanbanCard
- UpdateDepartmentRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateProjectRequest
- PermissionManagementController.php
- Illuminate\Database\Eloquent\Builder
- UpdateTaskPriorityColorsRequest
- config
- post-create-project-cmd
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateTaskStatusColorsRequest
- UserManagementController.php
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- autoload-dev

## God Nodes (most connected - your core abstractions)
1. `User` - 228 edges
2. `Organization` - 128 edges
3. `OrgMember` - 107 edges
4. `Task` - 104 edges
5. `Project` - 77 edges
6. `Role` - 70 edges
7. `Department` - 60 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 35 edges
10. `Controller` - 33 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeEligibleStaffMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (189 total, 41 thin omitted)

### Community 0 - "FileCategory.php"
Cohesion: 0.22
Nodes (6): PastedMediaNamer, Closure, RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.13
Nodes (9): Permission, up(), DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder (+1 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReactionController, CommentReaction, organization(), PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (7): AuditTrailController, NotificationSettingsController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "RichText"
Cohesion: 0.12
Nodes (3): UpdateTaskRequest, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

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
Nodes (57): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+49 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, AuditLogPolicy, DepartmentPolicy, ProjectPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 91 - "Document"
Cohesion: 0.12
Nodes (8): Document, DocumentPolicy, DocumentUploadService, up(), DocumentAccessLevel, uploadDocumentForDownload(), makeDocumentForList(), makeDocument()

### Community 92 - "Organization"
Cohesion: 0.09
Nodes (39): AccessPermission, Department, Organization, OrgMember, Project, Role, Illuminate\Support\Facades\Notification, makeTaskForAnalytics() (+31 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (5): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, ImportRow, Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (11): AccessControlController, AnalyticsController, AuthenticatedSessionController, Controller, DepartmentManagementController, DocumentController, ImportController, NotificationController (+3 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.24
Nodes (7): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (7): ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.19
Nodes (6): LinkPreviewController, RichTextAudioController, RichTextImageController, RichTextVideoController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 115 - "FileStorageService"
Cohesion: 0.23
Nodes (3): FileStorageService, StoredFile, Symfony\Component\HttpFoundation\StreamedResponse

### Community 116 - "Illuminate\Http\UploadedFile"
Cohesion: 0.15
Nodes (14): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile (+6 more)

### Community 118 - ".__invoke"
Cohesion: 0.62
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (4): GoogleAuthController, OrganizationManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Role.php"
Cohesion: 0.08
Nodes (3): findCommentCardBody(), DOMElement, toggleStaffManageDocuments()

### Community 143 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): UploadImportRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.33
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.13
Nodes (11): RichTextDocumentController, Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 149 - "Subtask"
Cohesion: 0.15
Nodes (5): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 157 - "StoreProjectRequest"
Cohesion: 0.15
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 158 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 164 - "Priority.php"
Cohesion: 0.08
Nodes (8): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self, TaskPriorityColor, TaskStatusColor

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 181 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 182 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Department.php`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Illuminate\Http\Request`, `OrganizationPolicy`, `Role.php`, `Subtask`, `StoreProjectRequest`, `UserManagementController`, `TaskPolicy`, `UserPolicy`, `Illuminate\Database\Eloquent\Builder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserManagementController.php`, `TaskManagementController`, `ProjectManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `LoginRequest`, `CommentPolicy`?**
  _High betweenness centrality (0.137) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Department.php`, `Illuminate\Http\Request`, `OrganizationPolicy`, `Role.php`, `Illuminate\Validation\Validator`, `OrganizationManagementController.php`, `UserManagementController`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreTaskRequest`, `TaskManagementController`, `ProjectManagementController`, `Document`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `.__invoke`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `FileCategory.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Department.php`, `Role.php`, `RichText`, `Subtask`, `TaskPolicy`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\JsonResponse`, `.__invoke`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._