# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 504 files · ~313,397 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2183 nodes · 6180 edges · 242 communities (186 shown, 56 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 416 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `acd5dff5`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
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
- Controller
- OrgMember
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- Document
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
- .withNextFilename
- User
- Illuminate\View\View
- Closure
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- FileCategory.php
- CompanyRoleRules
- CodeLanguageClassSanitizer
- static
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\JsonResponse
- UserManagementController
- Role
- Illuminate\Database\Seeder
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- TagsImportBatch.php
- require
- Organization.php
- TaskStatus
- UpdateTaskRequest
- psr-4
- AuditEventMailNotification.php
- AuditEventNotifier
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ProjectManagementController
- .__invoke
- Illuminate\Http\RedirectResponse
- ImportBatch
- keywords
- FileStorageException
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- .attach
- DashboardController
- code-highlight.js
- Illuminate\Auth\Access\Response
- RichText
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- DocumentDependencyService
- Department
- App\Http\Controllers\Concerns\ResolvesReturnTo
- deploy.sh
- StoreDepartmentRequest
- check-deploy-config.sh
- AccessProfile
- ImportBatch.php
- run.sh
- Illuminate\Console\Command
- DbBackupCommand
- release.md
- ProjectStatus.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreProjectRequest
- AuditLog
- UpdateTaskPriorityColorsRequest
- UpdateProjectRequest
- BackfillTaskCompletedAtTest.php
- PerformanceBaseline
- next-version.sh
- release-check.sh
- release-tasks.sh
- DbBackupCommand.php
- LinkPreviewController.php
- TaskReturnAfterSaveTest.php
- RuntimeException
- DocumentAccessLevel.php
- UpdateDepartmentRequest
- Illuminate\Database\Eloquent\Casts\Attribute
- UpdateRoleRequest
- config
- StoreTaskRequest
- ImportController.php
- TaskIndexesTest.php
- post-create-project-cmd
- fakePastedImage
- fakeUploadDoc
- fakeVideo

## God Nodes (most connected - your core abstractions)
1. `User` - 358 edges
2. `Organization` - 243 edges
3. `Task` - 206 edges
4. `OrgMember` - 184 edges
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

## Communities (242 total, 56 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.05
Nodes (37): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, Project, makeTaskForAnalytics(), makeReactionClient() (+29 more)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.13
Nodes (5): Collection, AppServiceProvider, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Support\ServiceProvider

### Community 3 - "Task"
Cohesion: 0.07
Nodes (24): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), uploadDocumentForDownload(), makeKanbanCapTask(), expectedCompletedColumnOrder() (+16 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), parentTask(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.06
Nodes (3): PastedMedia, Illuminate\Database\Eloquent\Model, expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Controller"
Cohesion: 0.19
Nodes (5): AuthenticatedSessionController, GoogleAuthController, CommentReactionController, Controller, RichTextAudioController

### Community 14 - "OrgMember"
Cohesion: 0.08
Nodes (14): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForFolderTest(), makeStaffForNewMenuTest() (+6 more)

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.29
Nodes (6): tasks._return-to-inputs, users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "Document"
Cohesion: 0.14
Nodes (9): DocumentController, TaskDocumentController, Document, DocumentAccessLevel, Illuminate\Database\QueryException, Illuminate\Http\Request, Illuminate\Http\Response, makeDocumentForIconTest() (+1 more)

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.06
Nodes (33): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), capExpectedSections() (+25 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (25): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy (+17 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (7): AccessControlController, AuditTrailController, DepartmentManagementController, NotificationController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 90 - "Closure"
Cohesion: 0.17
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Closure, Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "FileCategory.php"
Cohesion: 0.13
Nodes (14): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory, UploadedFile (+6 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "static"
Cohesion: 0.10
Nodes (11): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), CompanyCode, UserFactory, isSqlite(), numberTasks(), resolveCodes() (+3 more)

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.12
Nodes (12): DocumentFolderController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskFolderController, DocumentFolder, Illuminate\Http\JsonResponse, makeWarningTestFolder() (+4 more)

### Community 116 - "Role"
Cohesion: 0.08
Nodes (26): AnalyticsController, PermissionManagementController, Permission, Role, RolePolicy, up(), PermissionSeeder, revokeCommentPermissionFromStaff() (+18 more)

### Community 118 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

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
Cohesion: 0.12
Nodes (15): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+7 more)

### Community 139 - "TagsImportBatch.php"
Cohesion: 0.33
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "TaskStatus"
Cohesion: 0.32
Nodes (4): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditEventMailNotification.php"
Cohesion: 0.33
Nodes (5): AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.16
Nodes (4): StoreOrganizationRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.09
Nodes (9): NotificationSettingsController, OrganizationManagementController, TaskColorController, NotificationSetting, TaskPriorityColor, TaskStatusColor, NotificationSettingPolicy, Illuminate\Http\RedirectResponse (+1 more)

### Community 164 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "FileStorageException"
Cohesion: 0.25
Nodes (3): FileStorageException, self, Throwable

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.24
Nodes (4): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver

### Community 178 - ".attach"
Cohesion: 0.33
Nodes (3): DocumentAlreadyAttachedException, self, TaskDocumentLinker

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Illuminate\Auth\Access\Response"
Cohesion: 0.13
Nodes (5): CommentPolicy, SubtaskPolicy, TaskPolicy, DeactivatedTask, Illuminate\Auth\Access\Response

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.27
Nodes (3): CommentController, Comment, addComment()

### Community 191 - "Department"
Cohesion: 0.10
Nodes (25): AccessPermission, Department, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+17 more)

### Community 192 - "App\Http\Controllers\Concerns\ResolvesReturnTo"
Cohesion: 0.70
Nodes (4): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams()

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 202 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 204 - "run.sh"
Cohesion: 0.24
Nodes (7): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), pass(), run_deploy(), run.sh script

### Community 205 - "Illuminate\Console\Command"
Cohesion: 0.36
Nodes (3): CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "ProjectStatus.php"
Cohesion: 0.15
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 213 - "AuditLog"
Cohesion: 0.13
Nodes (4): AuditLog, AuditEventDatabaseNotification, latestEntry(), assertTaskDocumentAttachedAuditEntry()

### Community 216 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 228 - "RuntimeException"
Cohesion: 0.50
Nodes (3): FolderAlreadyLinkedException, self, RuntimeException

### Community 229 - "DocumentAccessLevel.php"
Cohesion: 0.22
Nodes (3): DocumentUploadService, FileCategory, up()

### Community 231 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 235 - "ImportController.php"
Cohesion: 0.22
Nodes (3): ImportController, UploadImportRequest, Symfony\Component\HttpFoundation\StreamedResponse

### Community 237 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 238 - "fakePastedImage"
Cohesion: 0.67
Nodes (3): fakePastedImage(), fakePastedVideo(), UploadedFile

## Knowledge Gaps
- **158 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+153 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **56 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `NotificationEventType.php`, `Illuminate\Support\Collection`, `TaskStatus.php`, `Organization.php`, `OrgMember`, `AuditEventNotifier`, `ProjectManagementController`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `Document`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Comment`, `DocumentDependencyService`, `Department`, `Illuminate\Console\Command`, `ProjectStatus.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditLog`, `Illuminate\View\View`, `PerformanceBaseline`, `TaskManagementController`, `Role.php`, `LinkPreviewController.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\JsonResponse`, `UserManagementController`, `Role`, `Task.php`?**
  _High betweenness centrality (0.149) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `TaskStatus.php`, `Controller`, `OrgMember`, `TaskStatus`, `ProjectManagementController`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `Document`, `Subtask`, `.attach`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Comment`, `DocumentDependencyService`, `Department`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditLog`, `.withNextFilename`, `User`, `Illuminate\View\View`, `BackfillTaskCompletedAtTest.php`, `Closure`, `TaskManagementController`, `Role.php`, `LinkPreviewController.php`, `TaskReturnAfterSaveTest.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\JsonResponse`, `Role`, `Task.php`?**
  _High betweenness centrality (0.095) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Support\Collection`, `TaskStatus.php`, `Organization.php`, `OrgMember`, `TaskStatus`, `ProjectManagementController`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `Document`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Department`, `StoreDepartmentRequest`, `Illuminate\Console\Command`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `Illuminate\View\View`, `PerformanceBaseline`, `BackfillTaskCompletedAtTest.php`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `CompanyRoleRules`, `static`, `Department.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\JsonResponse`, `UserManagementController`, `Role`, `Illuminate\Database\Seeder`, `Task.php`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._