# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 501 files · ~310,372 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2168 nodes · 6104 edges · 228 communities (187 shown, 41 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `63f29e2c`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OrgMember
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
- ImportBatch
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
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- TagsImportBatch.php
- _form.blade.php
- Illuminate\Http\UploadedFile
- Illuminate\Database\Eloquent\Model
- CodeLanguageClassSanitizer
- Organization
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Document
- static
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreTaskRequest
- FileStorageService
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- ValidatesTaskCode.php
- require
- TaskStatus
- Illuminate\Database\Seeder
- psr-4
- config
- FileCategory.php
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Project
- .__invoke
- Illuminate\Http\RedirectResponse
- Illuminate\Console\Command
- keywords
- FileStorageException
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- TaskManagementController.php
- DashboardController
- code-highlight.js
- DbBackupCommand
- DbBackupCommand.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- ValidatesTaskAssignment.php
- Department
- deploy.sh
- UpdateTaskRequest
- check-deploy-config.sh
- TaskDocumentDetachOwnershipTest.php
- run.sh
- TaskIndexesTest.php
- BackfillTaskCompletedAt
- release.md
- UpdateProjectRequest
- .attach
- post-create-project-cmd
- AuditEventMailNotification.php
- makeMeasuredTasks
- 2026_08_17_001000_create_tasks_table.php
- PerformanceBaseline
- next-version.sh
- release-check.sh
- release-tasks.sh
- AppServiceProvider.php
- StoreProjectRequest
- DocumentUploadService

## God Nodes (most connected - your core abstractions)
1. `User` - 357 edges
2. `Organization` - 243 edges
3. `Task` - 202 edges
4. `OrgMember` - 183 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 51 edges
10. `DocumentFolder` - 45 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffWhoCanCreateTasksForLinkPreview()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/RichText/LinkPreviewTest.php → app/Models/AccessPermission.php
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeStaffWithDeptAccessForFolderTest()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskFolderAttachTest.php → app/Models/AccessPermission.php
- `makePreviewViewer()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskPreviewTest.php → app/Models/AccessPermission.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (228 total, 41 thin omitted)

### Community 0 - "OrgMember"
Cohesion: 0.11
Nodes (4): OrgMember, CompanyRoleSyncer, findCommentCardBody(), DOMElement

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.13
Nodes (4): FlushAccessProfileCache, Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.05
Nodes (28): Task, TaskObserver, DocumentDependencyService, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), addTaskAudit(), completedAtOf(), makeLegacyTask() (+20 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.05
Nodes (6): AbandonStaleImportBatches, CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.11
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, latestEntry(), assertTaskDocumentAttachedAuditEntry()

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

### Community 14 - "Role"
Cohesion: 0.07
Nodes (34): PermissionManagementController, Permission, Role, RolePolicy, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest() (+26 more)

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.29
Nodes (6): tasks._return-to-inputs, users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (26): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+18 more)

### Community 86 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 87 - "User"
Cohesion: 0.05
Nodes (21): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, TaskPolicy (+13 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+4 more)

### Community 90 - "Closure"
Cohesion: 0.14
Nodes (7): RichTextAudioController, RichTextVideoController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 91 - "TaskManagementController"
Cohesion: 0.13
Nodes (5): isSafeReturnUrl(), resolveReturnTo(), returnToParams(), TaskManagementController, TaskCodeQuery

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 105 - "TagsImportBatch.php"
Cohesion: 0.33
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 107 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 108 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.28
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.04
Nodes (23): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeLinkOnlyDocumentForDeleteTest(), makeWarningTestFolder(), makeWarningTestTask(), makeClientForDocumentList(), makeDocumentForList() (+15 more)

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Document"
Cohesion: 0.11
Nodes (12): CommentReactionController, DocumentController, DocumentFolderController, LinkPreviewController, RichTextDocumentController, RichTextImageController, TaskDocumentController, TaskFolderController (+4 more)

### Community 115 - "static"
Cohesion: 0.10
Nodes (11): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), CompanyCode, UserFactory, isSqlite(), numberTasks(), resolveCodes() (+3 more)

### Community 118 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "1. One-time setup"
Cohesion: 0.11
Nodes (17): 1. One-time setup, 2. A normal release, 3. A hotfix, 4. Re-deploying the same commit, 5. Rollback, 6. When a deploy fails, 7. Release notes, a. Create the production branch from what's live on PROD (+9 more)

### Community 134 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (14): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet() (+6 more)

### Community 139 - "ValidatesTaskCode.php"
Cohesion: 0.15
Nodes (5): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "TaskStatus"
Cohesion: 0.32
Nodes (4): AnalyticsController, KanbanController, TaskStatus, Illuminate\Database\Eloquent\Collection

### Community 145 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "config"
Cohesion: 0.18
Nodes (5): config(), prefix(), Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 149 - "FileCategory.php"
Cohesion: 0.20
Nodes (4): PastedMediaNamer, fileFor(), FileCategory, UploadedFile

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, UpdateTaskPriorityColorsRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Project"
Cohesion: 0.08
Nodes (18): ProjectManagementController, isAssignableStaffForProject(), Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), capMixedCompany(), makeClientWithProjectAccessForDownload() (+10 more)

### Community 158 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.10
Nodes (7): GoogleAuthController, NotificationSettingsController, TaskColorController, NotificationSetting, NotificationSettingPolicy, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 164 - "Illuminate\Console\Command"
Cohesion: 0.31
Nodes (3): CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

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
Cohesion: 0.17
Nodes (4): Subtask, SubtaskObserver, SubtaskPolicy, makePreviewViewer()

### Community 178 - "TaskManagementController.php"
Cohesion: 0.10
Nodes (9): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, up(), DocumentAccessLevel, Illuminate\Http\Response, RuntimeException (+1 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.14
Nodes (5): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer, addComment()

### Community 191 - "Department"
Cohesion: 0.08
Nodes (31): AccessPermission, Department, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+23 more)

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 203 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.38
Nodes (5): Illuminate\Database\QueryException, grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 204 - "run.sh"
Cohesion: 0.24
Nodes (7): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), pass(), run_deploy(), run.sh script

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 212 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 213 - "AuditEventMailNotification.php"
Cohesion: 0.21
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 214 - "makeMeasuredTasks"
Cohesion: 0.50
Nodes (4): countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

## Knowledge Gaps
- **158 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+153 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `OrgMember`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `Illuminate\Support\Collection`, `TaskStatus.php`, `UserManagementController`, `Organization.php`, `TaskStatus`, `Role`, `Project`, `Illuminate\Http\RedirectResponse`, `Illuminate\Console\Command`, `Subtask`, `ImportBatch`, `LoginRequest`, `TaskManagementController.php`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Comment`, `Department`, `Project.php`, `TaskDocumentDetachOwnershipTest.php`, `UpdateProjectRequest`, `makeMeasuredTasks`, `Illuminate\View\View`, `PerformanceBaseline`, `TaskManagementController`, `Department.php`, `DocumentUploadService`, `Organization`, `Task.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Document`, `static`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.165) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `OrgMember`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Support\Collection`, `TaskStatus.php`, `ValidatesTaskCode.php`, `UserManagementController`, `Organization.php`, `TaskStatus`, `Role`, `Illuminate\Database\Seeder`, `Illuminate\Validation\Validator`, `Project`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `Illuminate\Console\Command`, `ImportBatch`, `PerformanceSeeder`, `TaskManagementController.php`, `ImportTemplateBuilder`, `DashboardController`, `Department`, `Project.php`, `TaskDocumentDetachOwnershipTest.php`, `User`, `Illuminate\View\View`, `PerformanceBaseline`, `TaskManagementController`, `Department.php`, `Illuminate\Database\Eloquent\Model`, `Task.php`, `Illuminate\Database\Eloquent\Builder`, `Document`, `static`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreTaskRequest`?**
  _High betweenness centrality (0.090) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `OrgMember`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `Illuminate\Support\Collection`, `TaskStatus.php`, `Role`, `TaskStatus`, `config`, `FileCategory.php`, `Project`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `Subtask`, `ImportBatch`, `TaskManagementController.php`, `DashboardController`, `Pest.php`, `Comment`, `ValidatesTaskAssignment.php`, `Department`, `Project.php`, `.attach`, `AuditEventMailNotification.php`, `makeMeasuredTasks`, `User`, `Closure`, `TaskManagementController`, `Department.php`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Model`, `Organization`, `Task.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Document`, `static`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.082) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._