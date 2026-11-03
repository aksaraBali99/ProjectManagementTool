# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 489 files · ~297,508 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2086 nodes · 5992 edges · 214 communities (176 shown, 38 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 405 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4dfa4808`
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
- Task.php
- LARAVEL_README.md
- ImportBatch
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
- Organization
- User
- Illuminate\View\View
- auth.php
- TaskManagementController
- PermissionSeeder.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Illuminate\Http\UploadedFile
- ProjectManagementController
- CodeLanguageClassSanitizer
- Project
- OrgMember
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- ImportController.php
- Document
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Database\Eloquent\Relations\HasMany
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- AuditEventNotifier
- StoreDepartmentRequest
- Illuminate\Http\RedirectResponse
- require
- Organization.php
- Comment
- FileStorageService
- psr-4
- UpdateTaskPriorityColorsRequest
- AccessProfile
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UpdateProjectRequest
- makeMeasuredTasks
- UserManagementController.php
- BootstrapEnvironment
- keywords
- LinkPreviewController.php
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
- TagsImportBatch.php
- RichText
- Role
- TaskIndexesTest.php
- StoreProjectRequest
- ValidatesTaskAssignment.php
- UpdateTaskStatusColorsRequest
- Illuminate\Database\Eloquent\Casts\Attribute
- post-create-project-cmd
- ValidClientUser.php
- AuditLog
- BackfillTaskCompletedAtTest.php
- FileCategory.php
- config

## God Nodes (most connected - your core abstractions)
1. `User` - 357 edges
2. `Organization` - 244 edges
3. `Task` - 200 edges
4. `OrgMember` - 183 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 51 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `grantDept()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/AccessPermission.php
- `makeStaffWhoCanCreateTasksForLinkPreview()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/RichText/LinkPreviewTest.php → app/Models/AccessPermission.php
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeStaffWithDeptAccessForFolderTest()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskFolderAttachTest.php → app/Models/AccessPermission.php
- `makePreviewViewer()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskPreviewTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (214 total, 38 thin omitted)

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

### Community 11 - "Task.php"
Cohesion: 0.07
Nodes (6): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ImportBatch"
Cohesion: 0.14
Nodes (7): ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

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

### Community 86 - "Organization"
Cohesion: 0.06
Nodes (28): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeStaffForFolderTest() (+20 more)

### Community 87 - "User"
Cohesion: 0.04
Nodes (22): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, NotificationSettingPolicy, OrganizationPolicy, RolePolicy, TaskPolicy (+14 more)

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

### Community 110 - "Project"
Cohesion: 0.06
Nodes (42): AccessPermission, Department, Project, ProjectPolicy, DepartmentSeeder, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeStaffOnCalendar() (+34 more)

### Community 111 - "OrgMember"
Cohesion: 0.10
Nodes (3): OrgMember, findKanbanCard(), DOMElement

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "ImportController.php"
Cohesion: 0.22
Nodes (3): ImportController, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 115 - "Document"
Cohesion: 0.08
Nodes (18): CommentReactionController, DocumentController, DocumentFolderController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController (+10 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (5): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Comment"
Cohesion: 0.13
Nodes (4): CommentController, Comment, CommentPolicy, addComment()

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

### Community 158 - "makeMeasuredTasks"
Cohesion: 0.50
Nodes (4): countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

### Community 164 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, Illuminate\Console\Command

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
Cohesion: 0.20
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

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

### Community 188 - "TagsImportBatch.php"
Cohesion: 0.33
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 191 - "Role"
Cohesion: 0.10
Nodes (29): PermissionManagementController, Permission, Role, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest() (+21 more)

### Community 200 - "ValidatesTaskAssignment.php"
Cohesion: 0.13
Nodes (4): SubtaskController, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 202 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 204 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

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
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventNotifier`, `NotificationEventType.php`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Organization.php`, `ImportBatch`, `Comment`, `Department.php`, `makeMeasuredTasks`, `UserManagementController.php`, `BootstrapEnvironment`, `LinkPreviewController.php`, `TaskStatus`, `Subtask`, `PerformanceSeeder`, `LoginRequest`, `DocumentFolder`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Role`, `ValidClientUser.php`, `AuditLog`, `Organization`, `Illuminate\View\View`, `auth.php`, `TaskManagementController`, `PermissionSeeder.php`, `Illuminate\Http\UploadedFile`, `ProjectManagementController`, `Project`, `OrgMember`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Relations\HasMany`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Task.php`, `Illuminate\Http\RedirectResponse`, `Organization.php`, `ImportBatch`, `Department.php`, `Illuminate\Validation\Validator`, `UserManagementController.php`, `BootstrapEnvironment`, `TaskStatus`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DocumentFolder`, `DashboardController`, `Role`, `BackfillTaskCompletedAtTest.php`, `User`, `Illuminate\View\View`, `auth.php`, `TaskManagementController`, `PermissionSeeder.php`, `ProjectManagementController`, `Project`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Relations\HasMany`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Support\Collection`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Department.php`, `ImportBatch`, `Comment`, `Organization.php`, `makeMeasuredTasks`, `LinkPreviewController.php`, `TaskStatus`, `Subtask`, `PerformanceSeeder`, `DocumentFolder`, `DashboardController`, `Pest.php`, `Role`, `ValidatesTaskAssignment.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `AuditLog`, `BackfillTaskCompletedAtTest.php`, `FileCategory.php`, `Illuminate\View\View`, `User`, `Organization`, `TaskManagementController`, `PermissionSeeder.php`, `Illuminate\Http\UploadedFile`, `ProjectManagementController`, `Project`, `OrgMember`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.070) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._