# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 486 files · ~294,521 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2073 nodes · 5939 edges · 216 communities (182 shown, 34 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 405 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `bde61f93`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Support\Collection
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventNotifier
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- TaskStatus.php
- LARAVEL_README.md
- ImportBatch
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- OrganizationManagementController.php
- CLAUDE.md
- copilot-instructions.md
- rich-text-editor.js
- _password-input.blade.php
- ImportTemplateBuilder
- Pest.php
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- Organization
- User
- Illuminate\View\View
- Closure
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Illuminate\Http\UploadedFile
- Project
- CodeLanguageClassSanitizer
- Role
- OrgMember
- LinkPreviewService.php
- Illuminate\Database\Eloquent\Builder
- DocumentAccessLevel.php
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- static
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- Document
- UpdateTaskRequest
- Illuminate\Http\RedirectResponse
- require
- Comment
- FileCategory.php
- psr-4
- AppServiceProvider.php
- AccessProfile
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Model
- NotificationSetting
- UserManagementController
- BootstrapEnvironment
- keywords
- LinkPreviewController.php
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- TaskDocumentLinker.php
- DashboardController
- code-highlight.js
- CommentMentionHighlightTest.php
- DocumentFolder
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- TagsImportBatch.php
- RichText
- TaskDocumentDetachOwnershipTest.php
- TaskIndexesTest.php
- StoreProjectRequest
- ValidatesTaskAssignment.php
- UpdateTaskStatusColorsRequest
- Illuminate\Database\Eloquent\Casts\Attribute
- post-create-project-cmd
- UpdateProjectRequest
- .__invoke
- AuditLog
- BackfillTaskCompletedAtTest.php
- FileStorageServiceTest.php
- config

## God Nodes (most connected - your core abstractions)
1. `User` - 353 edges
2. `Organization` - 241 edges
3. `Task` - 192 edges
4. `OrgMember` - 181 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `setUpCompletedWindowCompany()` --calls--> `AccessPermission`  [INFERRED]
  tests/Pest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `giveCompanyATask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Organizations/CompanyCodeFormTest.php → app/Models/Department.php

## Import Cycles
- None detected.

## Communities (216 total, 34 thin omitted)

### Community 0 - "Illuminate\Support\Collection"
Cohesion: 0.19
Nodes (10): AccessControlController, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), isSafeReturnUrl(), resolveReturnTo(), Illuminate\Support\Collection (+2 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (6): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.15
Nodes (4): Collection, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.05
Nodes (27): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+19 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReactionController, CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventNotifier"
Cohesion: 0.18
Nodes (4): AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

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

### Community 11 - "TaskStatus.php"
Cohesion: 0.08
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ImportBatch"
Cohesion: 0.13
Nodes (7): AbandonStaleImportBatches, ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "OrganizationManagementController.php"
Cohesion: 0.13
Nodes (6): OrganizationManagementController, taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.06
Nodes (29): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), countRequestQueries() (+21 more)

### Community 86 - "Organization"
Cohesion: 0.04
Nodes (18): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeStaffForFolderTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest(), makeClientForDocuments() (+10 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (20): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+12 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (6): AuditTrailController, NotificationController, ProjectManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 90 - "Closure"
Cohesion: 0.15
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.25
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (18): DocumentUploadService, FileCategory, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+10 more)

### Community 108 - "Project"
Cohesion: 0.07
Nodes (21): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeClientWithProjectAccessForDownload(), makeWarningTestTask(), makeClientForUploadTest() (+13 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Role"
Cohesion: 0.08
Nodes (45): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), makeStaffOnCalendar(), makeEligibleStaffMember() (+37 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.11
Nodes (5): LinkPreview, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.11
Nodes (9): completedWindowStartFor(), showsAllCompleted(), KanbanController, TaskStatus, up(), DateTimeInterface, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Collection (+1 more)

### Community 114 - "DocumentAccessLevel.php"
Cohesion: 0.16
Nodes (3): up(), Symfony\Component\HttpFoundation\StreamedResponse, makeDocumentForIconTest()

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (11): DocumentController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, TaskDocumentController, DocumentAccessLevel (+3 more)

### Community 118 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "Document"
Cohesion: 0.11
Nodes (15): Document, DocumentPolicy, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest() (+7 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (8): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Comment"
Cohesion: 0.15
Nodes (4): CommentController, Comment, CommentPolicy, addComment()

### Community 145 - "FileCategory.php"
Cohesion: 0.19
Nodes (3): FileStorageService, PastedMediaNamer, StoredFile

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 158 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 164 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (6): BackfillTaskCompletedAt, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, Illuminate\Console\Command

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.30
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.06
Nodes (13): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up(), DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+5 more)

### Community 178 - "TaskDocumentLinker.php"
Cohesion: 0.18
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "DocumentFolder"
Cohesion: 0.10
Nodes (10): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy, DocumentDependencyService, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder() (+2 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "TagsImportBatch.php"
Cohesion: 0.33
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 191 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 200 - "ValidatesTaskAssignment.php"
Cohesion: 0.14
Nodes (4): StoreDepartmentRequest, isAssignableStaffForProject(), StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 202 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 204 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 206 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 212 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 213 - "AuditLog"
Cohesion: 0.12
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

### Community 214 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 215 - "FileStorageServiceTest.php"
Cohesion: 0.50
Nodes (3): fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **134 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+129 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **34 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `AuditEventNotifier`, `Illuminate\Http\RedirectResponse`, `TaskStatus.php`, `Organization.php`, `ImportBatch`, `Comment`, `Task.php`, `Illuminate\Database\Eloquent\Model`, `NotificationSetting`, `UserManagementController`, `BootstrapEnvironment`, `Subtask`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `DocumentFolder`, `TaskDocumentDetachOwnershipTest.php`, `UpdateProjectRequest`, `AuditLog`, `Organization`, `Illuminate\View\View`, `Closure`, `TaskManagementController`, `Role.php`, `Illuminate\Http\UploadedFile`, `Project`, `Role`, `OrgMember`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`?**
  _High betweenness centrality (0.127) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Document`, `Illuminate\Http\RedirectResponse`, `TaskStatus.php`, `Organization.php`, `ImportBatch`, `Task.php`, `Illuminate\Validation\Validator`, `Illuminate\Database\Eloquent\Model`, `UserManagementController`, `BootstrapEnvironment`, `OrganizationManagementController.php`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `DocumentFolder`, `TaskDocumentDetachOwnershipTest.php`, `ValidatesTaskAssignment.php`, `.__invoke`, `BackfillTaskCompletedAtTest.php`, `User`, `Illuminate\View\View`, `Closure`, `TaskManagementController`, `Role.php`, `Project`, `Role`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `DocumentAccessLevel.php`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.099) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Support\Collection`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `Illuminate\Http\RedirectResponse`, `TaskStatus.php`, `Task.php`, `ImportBatch`, `Comment`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`, `LinkPreviewController.php`, `Subtask`, `TaskDocumentLinker.php`, `DashboardController`, `Pest.php`, `DocumentFolder`, `RichText`, `ValidatesTaskAssignment.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `.__invoke`, `AuditLog`, `Organization`, `BackfillTaskCompletedAtTest.php`, `Illuminate\View\View`, `config`, `Closure`, `TaskManagementController`, `Role.php`, `Illuminate\Http\UploadedFile`, `Project`, `Role`, `OrgMember`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `DocumentAccessLevel.php`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 30 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 30 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._