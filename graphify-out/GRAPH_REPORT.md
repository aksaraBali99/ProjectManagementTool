# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 486 files · ~294,087 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2072 nodes · 5935 edges · 217 communities (178 shown, 39 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `85d77bdf`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- PerformanceSeeder.php
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
- ImportController.php
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- StoreOrganizationRequest
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
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- DocumentAccessLevel.php
- OrgMember
- CodeLanguageClassSanitizer
- Department
- Role
- LinkPreviewService.php
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\HasMany
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
- Illuminate\Http\UploadedFile
- psr-4
- AppServiceProvider.php
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Model
- UpdateRoleRequest
- UserManagementController.php
- BootstrapEnvironment
- keywords
- UpdateTaskPriorityColorsRequest
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
- AuditEventNotifier
- RichText
- TaskStatus
- TaskIndexesTest.php
- ValidatesTaskAssignment.php
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Casts\Attribute
- TaskPolicy
- StoreProjectRequest
- TaskObserver
- .__invoke
- AuditLog
- BackfillTaskCompletedAtTest.php
- FileCategory.php
- config
- UpdateProjectRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 353 edges
2. `Organization` - 239 edges
3. `Task` - 190 edges
4. `OrgMember` - 181 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
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

## Communities (217 total, 39 thin omitted)

### Community 0 - "PerformanceSeeder.php"
Cohesion: 0.17
Nodes (5): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up()

### Community 1 - "ImportValidator"
Cohesion: 0.07
Nodes (11): ImportBatch, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver (+3 more)

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.07
Nodes (23): Task, MentionedInCommentNotification, Illuminate\Database\Eloquent\SoftDeletes, makeTaskForAnalytics(), makeWarningTestTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.07
Nodes (20): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, makeStaffForDocumentCreate(), makeClientForDocumentList(), makeDocumentForList() (+12 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (19): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+11 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, NotificationController, RoleManagementController (+3 more)

### Community 90 - "auth.php"
Cohesion: 0.17
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 105 - "FileStorageException"
Cohesion: 0.21
Nodes (4): FileStorageException, self, RuntimeException, Throwable

### Community 107 - "DocumentAccessLevel.php"
Cohesion: 0.22
Nodes (3): DocumentUploadService, FileCategory, up()

### Community 108 - "OrgMember"
Cohesion: 0.09
Nodes (17): OrgMember, Project, makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), findKanbanCard(), DOMElement, projectRow() (+9 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Department"
Cohesion: 0.12
Nodes (23): AccessPermission, Department, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+15 more)

### Community 111 - "Role"
Cohesion: 0.07
Nodes (32): AnalyticsController, PermissionManagementController, Permission, Role, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest() (+24 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (12): CommentReactionController, DocumentController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController (+4 more)

### Community 118 - "static"
Cohesion: 0.22
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "Document"
Cohesion: 0.09
Nodes (15): Document, DocumentPolicy, DocumentDependencyService, assertAttachableParity(), makeAttachableDocumentSet(), makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest() (+7 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (3): NotificationSettingsController, OrganizationManagementController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Comment"
Cohesion: 0.14
Nodes (4): CommentController, Comment, CommentPolicy, addComment()

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (15): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+7 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.06
Nodes (20): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), isSafeReturnUrl(), resolveReturnTo(), ProjectManagementController (+12 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.16
Nodes (4): TaskPriorityColor, TaskStatusColor, CompanyRoleSyncer, Illuminate\Database\Eloquent\Model

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
Nodes (7): Subtask, SubtaskObserver, SubtaskPolicy, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

### Community 178 - "TaskDocumentLinker.php"
Cohesion: 0.17
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, makeLinkerTestDocument()

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 184 - "DocumentFolder"
Cohesion: 0.13
Nodes (9): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "AuditEventNotifier"
Cohesion: 0.17
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 191 - "TaskStatus"
Cohesion: 0.29
Nodes (5): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 200 - "ValidatesTaskAssignment.php"
Cohesion: 0.14
Nodes (4): StoreDepartmentRequest, isAssignableStaffForProject(), StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 201 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 202 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 206 - "StoreProjectRequest"
Cohesion: 0.15
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 212 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 213 - "AuditLog"
Cohesion: 0.12
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

### Community 214 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 215 - "FileCategory.php"
Cohesion: 0.14
Nodes (9): PastedMedia, PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile, fakePastedImage(), fakePastedVideo() (+1 more)

## Knowledge Gaps
- **134 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+129 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **39 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `NotificationSetting`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role.php`, `Project.php`, `Comment`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Model`, `UserManagementController.php`, `BootstrapEnvironment`, `Subtask`, `LoginRequest`, `ImportTemplateBuilder`, `TaskDocumentLinker.php`, `DashboardController`, `Pest.php`, `DocumentFolder`, `AuditEventNotifier`, `TaskPolicy`, `StoreProjectRequest`, `AuditLog`, `Organization`, `Illuminate\View\View`, `auth.php`, `TaskManagementController`, `Department.php`, `DocumentAccessLevel.php`, `OrgMember`, `Department`, `Role`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`?**
  _High betweenness centrality (0.123) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `PerformanceSeeder.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role.php`, `Project.php`, `Illuminate\Support\Collection`, `Illuminate\Validation\Validator`, `Illuminate\Database\Eloquent\Model`, `UserManagementController.php`, `BootstrapEnvironment`, `PerformanceSeeder`, `ImportTemplateBuilder`, `TaskDocumentLinker.php`, `DashboardController`, `DocumentFolder`, `TaskStatus`, `ValidatesTaskAssignment.php`, `Illuminate\Database\Seeder`, `TaskObserver`, `.__invoke`, `BackfillTaskCompletedAtTest.php`, `User`, `Illuminate\View\View`, `auth.php`, `TaskManagementController`, `Department.php`, `OrgMember`, `Department`, `Role`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`?**
  _High betweenness centrality (0.092) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Project.php`, `Role.php`, `Comment`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Model`, `Subtask`, `TaskDocumentLinker.php`, `DashboardController`, `Pest.php`, `DocumentFolder`, `RichText`, `TaskStatus`, `ValidatesTaskAssignment.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `TaskPolicy`, `TaskObserver`, `.__invoke`, `AuditLog`, `BackfillTaskCompletedAtTest.php`, `FileCategory.php`, `Illuminate\View\View`, `Organization`, `TaskManagementController`, `Department.php`, `DocumentAccessLevel.php`, `OrgMember`, `Department`, `Role`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._