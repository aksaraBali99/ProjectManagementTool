# Graph Report - ProjectManagementTool  (2026-10-30)

## Corpus Check
- 472 files · ~276,994 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2005 nodes · 5728 edges · 218 communities (174 shown, 44 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 390 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9d4236cd`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentPolicy
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
- Task.php
- LARAVEL_README.md
- Organization
- Project
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
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
- RichText
- User
- Illuminate\View\View
- TaskStatus
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Department
- .attach
- CodeLanguageClassSanitizer
- TaskObserver
- Role
- LinkPreviewController.php
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- auth.php
- HasAdminConfigurableColors.php
- FileStorageService
- BootstrapEnvironment
- config
- OrgMember
- task-colors/edit.blade.php
- static
- ImportBatch
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- Illuminate\Http\UploadedFile
- psr-4
- Illuminate\Database\Eloquent\Model
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Comment
- UserManagementController.php
- NotificationEventType.php
- FileCategory.php
- keywords
- ProjectManagementController
- buildEmojiPicker
- DocumentAccessLevel.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- DashboardController
- code-highlight.js
- PermissionManagementController.php
- Document
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- ValidatesTaskAssignment.php
- AuditEventNotifier
- BackfillTaskCompletedAtTest.php
- ImportCommitService.php
- BackfillTaskCompletedAt
- Illuminate\Testing\TestResponse
- config
- DocumentDependencyService.php
- RuntimeException
- UpdateOrganizationRequest
- StoreProjectRequest
- Controller
- CompanyRoleRules
- .url
- post-create-project-cmd
- TaskDocumentDetachOwnershipTest.php
- DocumentAlreadyAttachedException
- UpdateRoleRequest
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 346 edges
2. `Organization` - 218 edges
3. `OrgMember` - 177 edges
4. `Task` - 176 edges
5. `Role` - 114 edges
6. `Project` - 97 edges
7. `Department` - 87 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
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

## Communities (218 total, 44 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.07
Nodes (8): Collection, AppServiceProvider, AccessProfile, Collection, self, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Support\ServiceProvider

### Community 3 - "Task"
Cohesion: 0.08
Nodes (20): Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask(), makePlainKanbanTasks() (+12 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.13
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

### Community 11 - "Task.php"
Cohesion: 0.08
Nodes (3): otherTaskIndexes(), taskIndexColumns(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Organization"
Cohesion: 0.07
Nodes (27): Organization, makeAttachableDocumentSet(), makeStaffForDocumentCreate(), makeLinkOnlyDocumentForDeleteTest(), makeWarningTestFolder(), makeWarningTestTask(), makeClientForDocumentList(), makeDocumentForList() (+19 more)

### Community 14 - "Project"
Cohesion: 0.08
Nodes (18): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeReactionEligibleStaff(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), findKanbanCard() (+10 more)

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.09
Nodes (22): DOMDocument, Illuminate\Foundation\Testing\TestCase, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), runImportValidationAndCommit(), listIds(), buildImportTestFile() (+14 more)

### Community 86 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.06
Nodes (20): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+12 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (11): AccessControlController, AuditTrailController, ImportController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, TaskColorController (+3 more)

### Community 90 - "TaskStatus"
Cohesion: 0.20
Nodes (5): KanbanController, TaskStatus, UpdateTaskStatusColorsRequest, up(), Illuminate\Database\Eloquent\Collection

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Department"
Cohesion: 0.08
Nodes (25): AccessPermission, Department, Illuminate\Contracts\Validation\Validator, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+17 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "TaskObserver"
Cohesion: 0.24
Nodes (4): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), TaskObserver

### Community 111 - "Role"
Cohesion: 0.09
Nodes (30): Permission, Role, up(), revokeCommentPermissionFromStaff(), capExpectedSections(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), grantManageDocumentsForEditTest() (+22 more)

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.19
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 115 - "auth.php"
Cohesion: 0.18
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, Illuminate\Console\Command

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "static"
Cohesion: 0.20
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 134 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (4): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.32
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.22
Nodes (11): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), capSorted(), assertAttachableParity() (+3 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (6): StoreDepartmentRequest, UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Comment"
Cohesion: 0.12
Nodes (4): Comment, CommentObserver, CommentPolicy, addComment()

### Community 164 - "FileCategory.php"
Cohesion: 0.23
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "DocumentAccessLevel.php"
Cohesion: 0.20
Nodes (4): DocumentUploadService, FileCategory, up(), DocumentAccessLevel

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.14
Nodes (4): SubtaskController, Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.08
Nodes (11): CalendarController, Carbon, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PerformanceSeeder, PermissionSeeder, RoleSeeder (+3 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "Document"
Cohesion: 0.09
Nodes (14): CommentReactionController, DocumentController, DocumentFolderController, RichTextAudioController, RichTextDocumentController, RichTextVideoController, TaskDocumentController, TaskFolderController (+6 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 189 - "ValidatesTaskAssignment.php"
Cohesion: 0.15
Nodes (3): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 201 - "Illuminate\Testing\TestResponse"
Cohesion: 0.38
Nodes (5): Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard()

### Community 204 - "RuntimeException"
Cohesion: 0.50
Nodes (3): FolderAlreadyLinkedException, self, RuntimeException

### Community 206 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 208 - "Controller"
Cohesion: 0.21
Nodes (5): AnalyticsController, AuthenticatedSessionController, Controller, LinkPreviewController, RichTextImageController

### Community 212 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 213 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentPolicy`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `static`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Task.php`, `User.php`, `Project`, `Organization`, `Illuminate\Support\Collection`, `Comment`, `UserManagementController.php`, `NotificationEventType.php`, `ProjectManagementController`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Document`, `NotificationSetting`, `AuditEventNotifier`, `ImportCommitService.php`, `Illuminate\Testing\TestResponse`, `DocumentDependencyService.php`, `StoreProjectRequest`, `TaskDocumentDetachOwnershipTest.php`, `RichText`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `Department`, `Role`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `auth.php`, `BootstrapEnvironment`, `OrgMember`?**
  _High betweenness centrality (0.173) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `ImportBatch`, `Task.php`, `Illuminate\Http\RedirectResponse`, `User.php`, `Project`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `UserManagementController.php`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Document`, `BackfillTaskCompletedAtTest.php`, `CompanyRoleRules`, `TaskDocumentDetachOwnershipTest.php`, `User`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Role.php`, `Department`, `Role`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `auth.php`, `BootstrapEnvironment`, `OrgMember`?**
  _High betweenness centrality (0.077) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `static`, `ImportBatch`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Organization`, `Project`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `Comment`, `FileCategory.php`, `DocumentAccessLevel.php`, `Subtask`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `DashboardController`, `Pest.php`, `Document`, `ValidatesTaskAssignment.php`, `BackfillTaskCompletedAtTest.php`, `ImportCommitService.php`, `DocumentDependencyService.php`, `Controller`, `RichText`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Role.php`, `Department`, `.attach`, `TaskObserver`, `Role`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._