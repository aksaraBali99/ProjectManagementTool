# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 481 files · ~288,575 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2049 nodes · 5869 edges · 214 communities (176 shown, 38 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 401 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `71051fe6`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Closure
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- NotificationSetting
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Task.php
- LARAVEL_README.md
- Department.php
- DocumentAccessLevel.php
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- PerformanceSeeder.php
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
- Comment
- User
- Illuminate\View\View
- TaskStatus
- TaskManagementController
- OrgMember
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Department
- Illuminate\Console\Command
- CodeLanguageClassSanitizer
- Organization
- Role
- LinkPreviewService.php
- Illuminate\Database\Eloquent\Builder
- Project
- Illuminate\Http\JsonResponse
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- FileCategory.php
- ImportBatch
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- Document
- StoreTaskRequest
- Illuminate\Http\RedirectResponse
- require
- Illuminate\Database\Eloquent\Model
- Illuminate\Http\UploadedFile
- psr-4
- static
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- AuditLog
- UserManagementController.php
- TaskObserver
- Illuminate\Database\Seeder
- keywords
- ProjectManagementController
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- PerformanceBaseline
- DashboardController
- code-highlight.js
- AccessProfile
- DocumentFolder
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- RichText
- AuditEventNotifier
- UpdateTaskRequest
- Project.php
- AppServiceProvider.php
- FileStorageService
- config
- BackfillTaskCompletedAtTest.php
- StoreProjectRequest
- UpdateProjectRequest
- ProjectStatus.php
- AccessPermission.php
- UpdateTaskStatusColorsRequest
- TaskIndexesTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 353 edges
2. `Organization` - 225 edges
3. `Task` - 187 edges
4. `OrgMember` - 179 edges
5. `Role` - 114 edges
6. `Project` - 102 edges
7. `Department` - 87 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
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

### Community 0 - "Closure"
Cohesion: 0.22
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): CalendarController, Carbon, DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Illuminate\Support\Carbon

### Community 2 - "AccessProfileCache"
Cohesion: 0.16
Nodes (4): FlushAccessProfileCache, Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.07
Nodes (22): Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask(), makePlainKanbanTasks() (+14 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "NotificationSetting"
Cohesion: 0.15
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

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

### Community 11 - "Task.php"
Cohesion: 0.08
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DocumentAccessLevel.php"
Cohesion: 0.24
Nodes (4): DocumentUploadService, FileCategory, up(), DocumentAccessLevel

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "PerformanceSeeder.php"
Cohesion: 0.16
Nodes (5): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up()

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (27): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+19 more)

### Community 86 - "Comment"
Cohesion: 0.15
Nodes (6): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), addComment()

### Community 87 - "User"
Cohesion: 0.05
Nodes (21): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, UserPolicy (+13 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+5 more)

### Community 90 - "TaskStatus"
Cohesion: 0.29
Nodes (5): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 91 - "TaskManagementController"
Cohesion: 0.18
Nodes (3): isSafeReturnUrl(), resolveReturnTo(), TaskManagementController

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
Cohesion: 0.11
Nodes (25): AccessPermission, Department, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+17 more)

### Community 108 - "Illuminate\Console\Command"
Cohesion: 0.19
Nodes (4): AbandonStaleImportBatches, CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.05
Nodes (17): AnalyticsController, Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeClientForEditTest(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+9 more)

### Community 111 - "Role"
Cohesion: 0.07
Nodes (31): PermissionManagementController, Permission, Role, RolePolicy, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest() (+23 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Project"
Cohesion: 0.08
Nodes (19): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), projectRow(), makeClientWithProjectAccessForAudio() (+11 more)

### Community 115 - "Illuminate\Http\JsonResponse"
Cohesion: 0.13
Nodes (8): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 119 - "ImportBatch"
Cohesion: 0.10
Nodes (8): BootstrapEnvironment, ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "Document"
Cohesion: 0.11
Nodes (13): DocumentController, RichTextDocumentController, TaskDocumentController, Document, Illuminate\Http\Request, Illuminate\Http\Response, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload() (+5 more)

### Community 134 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (3): GoogleAuthController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.10
Nodes (19): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory, UploadedFile (+11 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "static"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (14): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+6 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "AuditLog"
Cohesion: 0.14
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

### Community 164 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

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

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 184 - "DocumentFolder"
Cohesion: 0.09
Nodes (15): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, DocumentFolderController, TaskFolderController, DocumentFolder, TaskDocumentLinker (+7 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "RichText"
Cohesion: 0.21
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 202 - "config"
Cohesion: 0.17
Nodes (5): config(), prefix(), Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 203 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 206 - "ProjectStatus.php"
Cohesion: 0.15
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 210 - "AccessPermission.php"
Cohesion: 0.11
Nodes (4): findCommentCardBody(), DOMElement, findKanbanCard(), DOMElement

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `NotificationSetting`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Organization.php`, `DocumentAccessLevel.php`, `Department.php`, `static`, `Illuminate\Support\Collection`, `AuditLog`, `UserManagementController.php`, `ProjectManagementController`, `PerformanceSeeder.php`, `Subtask`, `LoginRequest`, `PerformanceBaseline`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `DocumentFolder`, `RichText`, `AuditEventNotifier`, `Project.php`, `ProjectStatus.php`, `AccessPermission.php`, `Comment`, `Illuminate\View\View`, `TaskManagementController`, `OrgMember`, `Department`, `Organization`, `Role`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportBatch`?**
  _High betweenness centrality (0.131) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Closure`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Organization.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Model`, `Department.php`, `static`, `Illuminate\Support\Collection`, `AuditLog`, `TaskObserver`, `ProjectManagementController`, `Subtask`, `DashboardController`, `Pest.php`, `DocumentFolder`, `RichText`, `Project.php`, `config`, `BackfillTaskCompletedAtTest.php`, `Comment`, `User`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `OrgMember`, `Department`, `Organization`, `Role`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Project`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `FileCategory.php`, `ImportBatch`?**
  _High betweenness centrality (0.082) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Document`, `StoreTaskRequest`, `Task.php`, `Illuminate\Http\RedirectResponse`, `Organization.php`, `Department.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `Illuminate\Validation\Validator`, `UserManagementController.php`, `TaskObserver`, `Illuminate\Database\Seeder`, `ProjectManagementController`, `PerformanceSeeder.php`, `PerformanceSeeder`, `PerformanceBaseline`, `ImportTemplateBuilder`, `DashboardController`, `DocumentFolder`, `Project.php`, `BackfillTaskCompletedAtTest.php`, `AccessPermission.php`, `User`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `OrgMember`, `Department`, `Role`, `Illuminate\Database\Eloquent\Builder`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportBatch`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._