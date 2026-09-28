# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 391 files · ~186,164 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1648 nodes · 4313 edges · 191 communities (153 shown, 38 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 279 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `870de303`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- FileCategory.php
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Role.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Http\Request
- LARAVEL_README.md
- AppServiceProvider.php
- ValidatesTaskAssignment.php
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
- BootstrapEnvironment
- User
- TaskManagementController
- AuditLog
- TaskManagementController.php
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- FileStorageException
- _form.blade.php
- NotificationSetting.php
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- Illuminate\Http\JsonResponse
- Illuminate\Http\UploadedFile
- CalendarController.php
- Illuminate\Database\Eloquent\Builder
- LoginRequest
- config
- CommentPolicy
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- UserPolicy
- NotificationSetting
- require
- ImportController.php
- AuditEventNotifier
- psr-4
- Task
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Document
- static
- UserManagementController
- TaskStatus.php
- Priority.php
- DocumentFolder
- TaskColorController.php
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Closure
- Permission.php
- Permission
- TaskObserver
- PastedMedia
- config
- .storePending
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- .storePending
- Illuminate\Database\Eloquent\Model
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- keywords
- UpdateProjectRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 250 edges
2. `Organization` - 137 edges
3. `OrgMember` - 115 edges
4. `Task` - 108 edges
5. `Project` - 77 edges
6. `Role` - 77 edges
7. `Department` - 62 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 38 edges
10. `Document` - 38 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnKanban()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `up()` --calls--> `Permission`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Permission.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (191 total, 38 thin omitted)

### Community 0 - "FileCategory.php"
Cohesion: 0.19
Nodes (7): RuntimeException, fileFor(), FileCategory, UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.15
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReactionController, CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Role.php"
Cohesion: 0.13
Nodes (3): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.11
Nodes (18): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout (+10 more)

### Community 9 - "dependencies"
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (6): DocumentController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ValidatesTaskAssignment.php"
Cohesion: 0.09
Nodes (5): StoreDepartmentRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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

### Community 86 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 87 - "User"
Cohesion: 0.08
Nodes (11): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+3 more)

### Community 90 - "AuditLog"
Cohesion: 0.18
Nodes (3): AuditLog, NotificationSettingsResolver, NotificationEventType

### Community 91 - "TaskManagementController.php"
Cohesion: 0.23
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

### Community 92 - "Organization"
Cohesion: 0.08
Nodes (47): AccessPermission, Department, Organization, OrgMember, Project, Role, makeTaskForAnalytics(), makeStaffOnCalendar() (+39 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "NotificationSetting.php"
Cohesion: 0.16
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 108 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (8): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, NotificationController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.14
Nodes (9): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells() (+1 more)

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.12
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.23
Nodes (5): LinkPreviewController, RichTextDocumentController, RichTextVideoController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (13): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakeDocumentFile() (+5 more)

### Community 116 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.18
Nodes (5): AccessControlController, GoogleAuthController, DepartmentManagementController, OrganizationManagementController, Illuminate\Http\RedirectResponse

### Community 139 - "NotificationSetting"
Cohesion: 0.18
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "AuditEventNotifier"
Cohesion: 0.17
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.11
Nodes (11): Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+3 more)

### Community 149 - "Subtask"
Cohesion: 0.15
Nodes (4): SubtaskController, Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Document"
Cohesion: 0.20
Nodes (6): Document, DocumentPolicy, uploadDocumentForDownload(), makeDocumentForList(), makeDocumentSetForParity(), makeDocument()

### Community 158 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 160 - "TaskStatus.php"
Cohesion: 0.19
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 166 - "DocumentFolder"
Cohesion: 0.29
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 168 - "TaskColorController.php"
Cohesion: 0.28
Nodes (3): TaskColorController, TaskPriorityColor, TaskStatusColor

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Closure"
Cohesion: 0.16
Nodes (6): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 181 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Role.php`, `Illuminate\Http\RedirectResponse`, `UserPolicy`, `NotificationSetting`, `User.php`, `ValidatesTaskAssignment.php`, `AuditEventNotifier`, `Task`, `Subtask`, `Document`, `static`, `UserManagementController`, `DocumentFolder`, `Closure`, `Permission.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `BootstrapEnvironment`, `TaskManagementController`, `AuditLog`, `TaskManagementController.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Database\Eloquent\Builder`, `LoginRequest`, `CommentPolicy`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `FileCategory.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Role.php`, `Illuminate\Http\Request`, `User.php`, `ValidatesTaskAssignment.php`, `AuditEventNotifier`, `Subtask`, `Document`, `static`, `TaskObserver`, `PastedMedia`, `.storePending`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.storePending`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `TaskManagementController.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\JsonResponse`, `CalendarController.php`, `Illuminate\Database\Eloquent\Builder`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Role.php`, `Illuminate\Http\Request`, `User.php`, `ValidatesTaskAssignment.php`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `Document`, `UserManagementController`, `Permission.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `User`, `TaskManagementController`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `CalendarController.php`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._