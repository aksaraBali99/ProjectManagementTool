# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 489 files · ~298,073 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2086 nodes · 5994 edges · 211 communities (177 shown, 34 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 403 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `02d025f8`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Support\Collection
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- NotificationEventType.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- TaskStatus.php
- LARAVEL_README.md
- KanbanChangesTest.php
- ImportController.php
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- TaskStatus
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
- Project
- User
- Illuminate\View\View
- auth.php
- TaskManagementController
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Illuminate\Http\UploadedFile
- ProjectManagementController
- CodeLanguageClassSanitizer
- Organization
- OrgMember
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- LinkPreviewController.php
- Document
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreDepartmentRequest
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- AuditEventNotifier
- post-create-project-cmd
- Illuminate\Http\RedirectResponse
- require
- FileStorageService
- psr-4
- UpdateTaskPriorityColorsRequest
- AccessProfile
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UpdateProjectRequest
- Illuminate\Database\Eloquent\Model
- UserManagementController.php
- ImportBatch
- keywords
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- DocumentFolder
- DashboardController
- code-highlight.js
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- RichText
- Role
- Project.php
- StoreProjectRequest
- ValidatesTaskAssignment.php
- UpdateTaskStatusColorsRequest
- Illuminate\Database\Eloquent\Casts\Attribute
- ValidClientUser.php
- AuditLog
- BackfillTaskCompletedAtTest.php
- FileCategory.php
- config

## God Nodes (most connected - your core abstractions)
1. `User` - 357 edges
2. `Organization` - 243 edges
3. `Task` - 200 edges
4. `OrgMember` - 183 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 51 edges
10. `DocumentFolder` - 45 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `giveCompanyATask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Organizations/CompanyCodeFormTest.php → app/Models/Department.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (211 total, 34 thin omitted)

### Community 0 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (14): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+6 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): CalendarController, Carbon, DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Illuminate\Support\Carbon

### Community 2 - "AccessProfileCache"
Cohesion: 0.12
Nodes (6): Collection, AppServiceProvider, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Support\ServiceProvider

### Community 3 - "Task"
Cohesion: 0.06
Nodes (23): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.07
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ImportController.php"
Cohesion: 0.22
Nodes (3): ImportController, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "TaskStatus"
Cohesion: 0.23
Nodes (6): AnalyticsController, KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (27): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+19 more)

### Community 86 - "Project"
Cohesion: 0.07
Nodes (22): Project, ProjectPolicy, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeClientWithProjectAccessForDownload(), makeWarningTestTask() (+14 more)

### Community 87 - "User"
Cohesion: 0.04
Nodes (26): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, NotificationSettingPolicy, OrganizationPolicy, RolePolicy, TaskPolicy (+18 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (11): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, NotificationController, OrganizationManagementController, RoleManagementController (+3 more)

### Community 90 - "auth.php"
Cohesion: 0.18
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Illuminate\Http\UploadedFile"
Cohesion: 0.10
Nodes (20): DocumentUploadService, FileCategory, up(), DocumentAccessLevel, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile() (+12 more)

### Community 108 - "ProjectManagementController"
Cohesion: 0.23
Nodes (3): isSafeReturnUrl(), resolveReturnTo(), ProjectManagementController

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.04
Nodes (29): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, DepartmentSeeder, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate() (+21 more)

### Community 111 - "OrgMember"
Cohesion: 0.11
Nodes (3): OrgMember, findKanbanCard(), DOMElement

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 115 - "Document"
Cohesion: 0.08
Nodes (18): CommentReactionController, DocumentController, DocumentFolderController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController (+10 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 134 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (5): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.22
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 164 - "ImportBatch"
Cohesion: 0.06
Nodes (13): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, ImportBatch, EmployeeIdGenerator, ImportCommitResolution (+5 more)

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
Cohesion: 0.15
Nodes (7): Subtask, SubtaskObserver, SubtaskPolicy, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (17): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), CompanyCode, UserFactory, isSqlite(), numberTasks(), resolveCodes() (+9 more)

### Community 178 - "DocumentFolder"
Cohesion: 0.10
Nodes (14): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, DocumentFolder, DocumentFolderPolicy, TaskDocumentLinker, Illuminate\Database\QueryException (+6 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.13
Nodes (7): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), CommentPolicy, addComment()

### Community 189 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 191 - "Role"
Cohesion: 0.08
Nodes (46): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), PermissionSeeder, makeStaffOnCalendar() (+38 more)

### Community 192 - "Project.php"
Cohesion: 0.10
Nodes (4): findCommentCardBody(), DOMElement, otherTaskIndexes(), taskIndexColumns()

### Community 200 - "ValidatesTaskAssignment.php"
Cohesion: 0.13
Nodes (4): SubtaskController, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 202 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 206 - "ValidClientUser.php"
Cohesion: 0.22
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 213 - "AuditLog"
Cohesion: 0.11
Nodes (9): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, latestEntry() (+1 more)

### Community 214 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 215 - "FileCategory.php"
Cohesion: 0.23
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **134 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+129 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **34 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventNotifier`, `NotificationEventType.php`, `Illuminate\Http\RedirectResponse`, `TaskStatus.php`, `Role.php`, `KanbanChangesTest.php`, `UserManagementController.php`, `ImportBatch`, `TaskStatus`, `Subtask`, `PerformanceSeeder`, `LoginRequest`, `DocumentFolder`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Comment`, `RichText`, `Role`, `ValidClientUser.php`, `AuditLog`, `Project`, `Illuminate\View\View`, `auth.php`, `TaskManagementController`, `Department.php`, `Illuminate\Http\UploadedFile`, `ProjectManagementController`, `Organization`, `OrgMember`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `LinkPreviewController.php`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `TaskStatus.php`, `Illuminate\Http\RedirectResponse`, `Role.php`, `Illuminate\Validation\Validator`, `Illuminate\Database\Eloquent\Model`, `UserManagementController.php`, `ImportBatch`, `TaskStatus`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DocumentFolder`, `DashboardController`, `Role`, `Project`, `User`, `Illuminate\View\View`, `BackfillTaskCompletedAtTest.php`, `auth.php`, `TaskManagementController`, `Department.php`, `ProjectManagementController`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Support\Collection`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `TaskStatus.php`, `KanbanChangesTest.php`, `Role.php`, `Illuminate\Database\Eloquent\Model`, `ImportBatch`, `TaskStatus`, `Subtask`, `PerformanceSeeder`, `DocumentFolder`, `DashboardController`, `Pest.php`, `Comment`, `RichText`, `Role`, `ValidatesTaskAssignment.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `AuditLog`, `Project`, `FileCategory.php`, `Illuminate\View\View`, `User`, `BackfillTaskCompletedAtTest.php`, `TaskManagementController`, `Department.php`, `Illuminate\Http\UploadedFile`, `ProjectManagementController`, `Organization`, `OrgMember`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `LinkPreviewController.php`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.070) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._