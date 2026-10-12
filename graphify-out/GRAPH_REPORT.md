# Graph Report - ProjectManagementTool  (2026-10-12)

## Corpus Check
- 442 files · ~250,450 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1889 nodes · 5348 edges · 197 communities (166 shown, 31 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 351 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `954c5209`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Role.php
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Role
- LARAVEL_README.md
- UpdateProjectRequest
- Illuminate\Database\Eloquent\Relations\HasMany
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
- Pest.php
- Illuminate\Support\Collection
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- FileCategory.php
- User
- Illuminate\View\View
- PerformanceBaseline
- RichText
- setup
- documents/create.blade.php
- app.js
- OrgMember
- _form.blade.php
- Department
- .attach
- CodeLanguageClassSanitizer
- static
- ImportBatch
- LinkPreview
- DocumentFolder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- Illuminate\Database\Eloquent\Model
- .url
- config
- Project
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Organization
- Comment
- require
- User.php
- Illuminate\Database\Seeder
- Department.php
- psr-4
- NotificationSetting
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- DocumentUploadService
- FileStorageException
- TaskManagementController
- keywords
- LoginRequest
- buildEmojiPicker
- UpdateTaskRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Document
- PerformanceSeeder
- TaskColorController.php
- AuditEventNotifier
- UserManagementController
- code-highlight.js
- RuntimeException
- Illuminate\Http\Request
- TaskDocumentDetachOwnershipTest.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- post-create-project-cmd
- fakeUploadDoc
- PastedMediaNamer
- Controller

## God Nodes (most connected - your core abstractions)
1. `User` - 331 edges
2. `Organization` - 206 edges
3. `OrgMember` - 161 edges
4. `Task` - 146 edges
5. `Role` - 110 edges
6. `Project` - 92 edges
7. `Department` - 84 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeReactionEligibleStaff()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentReactionTest.php → app/Models/AccessPermission.php
- `makeKanbanCardAssignee()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanCardLayoutTest.php → app/Models/AccessPermission.php
- `grantDept()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/AccessPermission.php
- `makeStaffWhoCanCreateTasksForLinkPreview()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/RichText/LinkPreviewTest.php → app/Models/AccessPermission.php
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (197 total, 31 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.07
Nodes (9): Collection, AppServiceProvider, CompanyRoleSyncer, AccessProfile, Collection, self, AccessProfileCache, BoardAccessDeniedReason (+1 more)

### Community 3 - "Task"
Cohesion: 0.10
Nodes (12): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+4 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.12
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

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

### Community 11 - "Role"
Cohesion: 0.08
Nodes (29): Permission, Role, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), grantManageDocumentsForEditTest(), makeClientForEditTest() (+21 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "UpdateProjectRequest"
Cohesion: 0.15
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

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
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "Pest.php"
Cohesion: 0.10
Nodes (22): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+14 more)

### Community 54 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (16): CalendarController, Carbon, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), DashboardController, Collection (+8 more)

### Community 86 - "FileCategory.php"
Cohesion: 0.11
Nodes (17): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory, UploadedFile (+9 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (19): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory (+11 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (7): ImportController, NotificationController, OrganizationManagementController, ProjectManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 90 - "PerformanceBaseline"
Cohesion: 0.22
Nodes (3): PerformanceBaseline, config(), prefix()

### Community 91 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "Department"
Cohesion: 0.08
Nodes (21): AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff(), makeEligibleStaff() (+13 more)

### Community 108 - ".attach"
Cohesion: 0.29
Nodes (3): FolderAlreadyLinkedException, self, TaskDocumentLinker

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "static"
Cohesion: 0.20
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "DocumentFolder"
Cohesion: 0.19
Nodes (4): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy

### Community 115 - "Closure"
Cohesion: 0.16
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.17
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Project"
Cohesion: 0.08
Nodes (17): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeReactionEligibleStaff(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), findKanbanCard() (+9 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 134 - "Organization"
Cohesion: 0.07
Nodes (25): Organization, makeStaffForDocumentCreate(), makeLinkOnlyDocumentForDeleteTest(), makeWarningTestFolder(), makeWarningTestTask(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+17 more)

### Community 139 - "Comment"
Cohesion: 0.17
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Database\Seeder"
Cohesion: 0.14
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "NotificationSetting"
Cohesion: 0.14
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 149 - "Subtask"
Cohesion: 0.15
Nodes (6): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.10
Nodes (7): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (7): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, StoreProjectRequest, StoreTaskRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "FileStorageException"
Cohesion: 0.25
Nodes (3): FileStorageException, self, Throwable

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "UpdateTaskRequest"
Cohesion: 0.10
Nodes (4): StoreDepartmentRequest, UpdateDepartmentRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Document"
Cohesion: 0.08
Nodes (9): DocumentController, Document, DocumentPolicy, DocumentDependencyService, up(), DocumentAccessLevel, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse (+1 more)

### Community 174 - "PerformanceSeeder"
Cohesion: 0.09
Nodes (5): AbandonStaleImportBatches, CleanupStalePendingMedia, SeedPerformanceData, PerformanceSeeder, Illuminate\Console\Command

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "RuntimeException"
Cohesion: 0.50
Nodes (3): DocumentAlreadyAttachedException, self, RuntimeException

### Community 184 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (11): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, TaskDocumentController (+3 more)

### Community 185 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 189 - "fakeUploadDoc"
Cohesion: 0.33
Nodes (4): fakeUploadDoc(), UploadedFile, fakeDocumentFile(), UploadedFile

### Community 192 - "Controller"
Cohesion: 0.19
Nodes (6): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, PermissionManagementController

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **31 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Organization`, `Role`, `Comment`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `UpdateProjectRequest`, `Department.php`, `NotificationSetting`, `Subtask`, `DocumentUploadService`, `LoginRequest`, `Document`, `AuditEventNotifier`, `Pest.php`, `UserManagementController`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `TaskDocumentDetachOwnershipTest.php`, `Controller`, `Illuminate\View\View`, `PerformanceBaseline`, `RichText`, `OrgMember`, `Department`, `static`, `ImportBatch`, `LinkPreview`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Project`?**
  _High betweenness centrality (0.175) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Http\RedirectResponse`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Seeder`, `Department.php`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `UpdateTaskRequest`, `Document`, `PerformanceSeeder`, `Pest.php`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `TaskDocumentDetachOwnershipTest.php`, `Controller`, `User`, `Illuminate\View\View`, `PerformanceBaseline`, `OrgMember`, `Department`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Illuminate\Database\Eloquent\Model`, `Project`?**
  _High betweenness centrality (0.081) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Organization`, `AuditLog`, `Role`, `Comment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `Subtask`, `DocumentUploadService`, `TaskManagementController`, `Document`, `Pest.php`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `FileCategory.php`, `User`, `Illuminate\View\View`, `RichText`, `Priority.php`, `OrgMember`, `Department`, `.attach`, `static`, `ImportBatch`, `LinkPreview`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Illuminate\Database\Eloquent\Model`, `Project`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 67 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 67 INFERRED edges - model-reasoned connections that need verification._