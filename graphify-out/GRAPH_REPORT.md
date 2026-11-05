# Graph Report - ProjectManagementTool  (2026-11-05)

## Corpus Check
- 498 files · ~308,023 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2156 nodes · 6086 edges · 223 communities (181 shown, 42 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `98acbccf`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Support\Collection
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
- TaskStatus.php
- LARAVEL_README.md
- UserManagementController
- Role
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
- AccessProfile
- User
- Illuminate\View\View
- Closure
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- AuditEventNotifier
- _form.blade.php
- FileCategory.php
- Illuminate\Database\Eloquent\Relations\HasMany
- CodeLanguageClassSanitizer
- Organization
- Task.php
- LinkPreviewService.php
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\Request
- Document
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreDepartmentRequest
- FileStorageService
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- NotificationSetting
- require
- OrgMember
- BackfillTaskCompletedAtTest.php
- static
- psr-4
- ValidatesTaskCode.php
- ClipboardMediaPasteTest.php
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ProjectManagementController
- .__invoke
- OrganizationManagementController
- ImportBatch
- keywords
- FileStorageException
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- TaskDocumentLinker.php
- DashboardController
- code-highlight.js
- StoreTaskRequest
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- DocumentFolderPolicy
- Department
- Department.php
- deploy.sh
- UpdateTaskRequest
- post-create-project-cmd
- Illuminate\Database\Seeder
- run.sh
- TaskIndexesTest.php
- release.md
- ProjectStatus.php
- PerformanceSeeder.php
- AuditEventMailNotification.php
- Illuminate\Console\Command
- next-version.sh
- release-check.sh
- release-tasks.sh
- ValidatesTaskAssignment.php
- AppServiceProvider.php
- StoreProjectRequest
- DocumentAccessLevel.php

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

## Communities (223 total, 42 thin omitted)

### Community 0 - "Illuminate\Support\Collection"
Cohesion: 0.12
Nodes (15): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+7 more)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (23): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.15
Nodes (6): AuditLog, NotificationEventType, NotificationSettingsResolver, NotificationEventType, latestEntry(), assertTaskDocumentAttachedAuditEntry()

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
Nodes (7): TaskPriorityColor, TaskStatusColor, Illuminate\Contracts\Validation\Validator, Illuminate\Database\Eloquent\Model, capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Role"
Cohesion: 0.08
Nodes (31): AnalyticsController, PermissionManagementController, Permission, Role, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest() (+23 more)

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
Cohesion: 0.32
Nodes (4): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (22): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+14 more)

### Community 86 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 87 - "User"
Cohesion: 0.05
Nodes (24): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+16 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (15): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController (+7 more)

### Community 90 - "Closure"
Cohesion: 0.15
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 91 - "TaskManagementController"
Cohesion: 0.13
Nodes (4): isSafeReturnUrl(), resolveReturnTo(), TaskManagementController, TaskCodeQuery

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "AuditEventNotifier"
Cohesion: 0.20
Nodes (5): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier

### Community 107 - "FileCategory.php"
Cohesion: 0.11
Nodes (17): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory (+9 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.06
Nodes (31): Organization, Project, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeStaffForEditTest(), makeClientForDocumentList(), makeDocumentForList() (+23 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (18): CommentReactionController, DocumentController, DocumentFolderController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController (+10 more)

### Community 115 - "Document"
Cohesion: 0.10
Nodes (10): Document, DocumentPolicy, DocumentDependencyService, Illuminate\Http\Response, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentForIconTest() (+2 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "1. One-time setup"
Cohesion: 0.11
Nodes (17): 1. One-time setup, 2. A normal release, 3. A hotfix, 4. Re-deploying the same commit, 5. Rollback, 6. When a deploy fails, 7. Release notes, a. Create the production branch from what's live on PROD (+9 more)

### Community 139 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 145 - "static"
Cohesion: 0.22
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "ValidatesTaskCode.php"
Cohesion: 0.15
Nodes (5): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 149 - "ClipboardMediaPasteTest.php"
Cohesion: 0.15
Nodes (5): PastedMedia, PastedMediaNamer, fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, UpdateRoleRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 164 - "ImportBatch"
Cohesion: 0.15
Nodes (7): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.13
Nodes (8): Subtask, SubtaskObserver, SubtaskPolicy, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), makePreviewViewer(), countQueries()

### Community 178 - "TaskDocumentLinker.php"
Cohesion: 0.15
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

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
Cohesion: 0.09
Nodes (6): CommentController, Comment, CommentPolicy, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer, addComment()

### Community 191 - "Department"
Cohesion: 0.10
Nodes (28): AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff(), makeReactionEligibleStaff() (+20 more)

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 201 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 203 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 204 - "run.sh"
Cohesion: 0.27
Nodes (7): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), pass(), run_deploy(), run.sh script

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "ProjectStatus.php"
Cohesion: 0.12
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 211 - "PerformanceSeeder.php"
Cohesion: 0.17
Nodes (5): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up()

### Community 213 - "AuditEventMailNotification.php"
Cohesion: 0.15
Nodes (7): AuditEventDatabaseNotification, AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 217 - "Illuminate\Console\Command"
Cohesion: 0.06
Nodes (13): AbandonStaleImportBatches, CleanupStalePendingMedia, DbBackupCommand, PerformanceBaseline, SeedPerformanceData, config(), prefix(), Attribute (+5 more)

### Community 229 - "DocumentAccessLevel.php"
Cohesion: 0.19
Nodes (5): DocumentUploadService, FileCategory, up(), DocumentAccessLevel, makeStaffForDocumentCreate()

## Knowledge Gaps
- **156 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+151 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `NotificationSetting`, `TaskStatus.php`, `UserManagementController`, `Role`, `OrgMember`, `static`, `ProjectManagementController`, `ImportBatch`, `Subtask`, `LoginRequest`, `TaskDocumentLinker.php`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Comment`, `DocumentFolderPolicy`, `Department`, `Department.php`, `ProjectStatus.php`, `Illuminate\View\View`, `Illuminate\Console\Command`, `Closure`, `TaskManagementController`, `Role.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Organization`, `Task.php`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `TaskStatus.php`, `UserManagementController`, `Role`, `OrgMember`, `BackfillTaskCompletedAtTest.php`, `static`, `ValidatesTaskCode.php`, `Illuminate\Validation\Validator`, `ProjectManagementController`, `.__invoke`, `OrganizationManagementController`, `ImportBatch`, `TaskStatus`, `PerformanceSeeder`, `ImportTemplateBuilder`, `TaskDocumentLinker.php`, `DashboardController`, `Department`, `Department.php`, `Illuminate\Database\Seeder`, `PerformanceSeeder.php`, `User`, `Illuminate\View\View`, `Illuminate\Console\Command`, `Closure`, `TaskManagementController`, `Role.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Task.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreDepartmentRequest`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Support\Collection`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `TaskStatus.php`, `OrgMember`, `Role`, `BackfillTaskCompletedAtTest.php`, `static`, `ClipboardMediaPasteTest.php`, `ProjectManagementController`, `.__invoke`, `ImportBatch`, `TaskStatus`, `Subtask`, `TaskDocumentLinker.php`, `DashboardController`, `Pest.php`, `Comment`, `Department`, `Department.php`, `AuditEventMailNotification.php`, `User`, `Illuminate\View\View`, `Illuminate\Console\Command`, `Closure`, `TaskManagementController`, `Role.php`, `ValidatesTaskAssignment.php`, `DocumentAccessLevel.php`, `Organization`, `Task.php`, `LinkPreviewService.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `Document`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.081) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._