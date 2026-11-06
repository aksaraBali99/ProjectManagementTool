# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 503 files · ~313,083 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2182 nodes · 6172 edges · 237 communities (189 shown, 48 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 416 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `be1c09dd`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
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
- .update
- OrgMember
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- ImportBatch.php
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
- FileCategory.php
- User
- Illuminate\View\View
- auth.php
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- Illuminate\Http\UploadedFile
- static
- CodeLanguageClassSanitizer
- ValidatesTaskCode.php
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\Request
- DocumentFolder
- Project
- StoreDepartmentRequest
- Organization
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- Comment
- require
- TaskStatus
- Illuminate\Database\Seeder
- psr-4
- NotificationSetting
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
- TaskDocumentLinker.php
- DashboardController
- code-highlight.js
- Illuminate\Auth\Access\Response
- Illuminate\Database\Eloquent\Model
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- RichText
- Document
- Role
- BackfillTaskCompletedAtTest.php
- deploy.sh
- UpdateTaskRequest
- check-deploy-config.sh
- AccessProfile
- FileStorageService
- run.sh
- TaskIndexesTest.php
- TaskReturnAfterSaveTest.php
- release.md
- Closure
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreProjectRequest
- AuditLog
- UpdateTaskPriorityColorsRequest
- UpdateProjectRequest
- UserManagementController
- config
- next-version.sh
- release-check.sh
- release-tasks.sh
- Illuminate\Console\Command
- PerformanceBaseline
- AuditEventNotifier
- DocumentAccessLevel.php
- DbBackupCommand.php
- Illuminate\Database\Eloquent\Casts\Attribute
- AppServiceProvider.php
- UploadImportRequest
- post-create-project-cmd
- FileStorageService.php
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 358 edges
2. `Organization` - 243 edges
3. `Task` - 205 edges
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
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php

## Import Cycles
- None detected.

## Communities (237 total, 48 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (23): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "OrgMember"
Cohesion: 0.10
Nodes (8): OrgMember, CompanyRoleSyncer, makeStaffForEditTest(), makeStaffForFolderTest(), findKanbanCard(), DOMElement, makeStaffForPairsParity(), makeStaffForEligibility()

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.29
Nodes (6): tasks._return-to-inputs, users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.06
Nodes (29): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), countRequestQueries() (+21 more)

### Community 86 - "FileCategory.php"
Cohesion: 0.17
Nodes (5): PastedMedia, PastedMediaNamer, fileFor(), FileCategory, UploadedFile

### Community 87 - "User"
Cohesion: 0.06
Nodes (20): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+12 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (10): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, ImportController, NotificationController, OrganizationManagementController, RoleManagementController (+2 more)

### Community 90 - "auth.php"
Cohesion: 0.18
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 108 - "static"
Cohesion: 0.10
Nodes (11): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), CompanyCode, UserFactory, isSqlite(), numberTasks(), resolveCodes() (+3 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "ValidatesTaskCode.php"
Cohesion: 0.15
Nodes (5): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (13): CommentReactionController, DocumentController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController (+5 more)

### Community 115 - "DocumentFolder"
Cohesion: 0.13
Nodes (9): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 116 - "Project"
Cohesion: 0.09
Nodes (20): isAssignableStaffForProject(), Project, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), projectRow(), makeClientWithProjectAccessForAudio() (+12 more)

### Community 119 - "Organization"
Cohesion: 0.07
Nodes (22): AnalyticsController, Organization, makeClientForDeleteTest(), makeClientForEditTest(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeStaffForNewMenuTest() (+14 more)

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
Cohesion: 0.13
Nodes (13): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+5 more)

### Community 139 - "Comment"
Cohesion: 0.14
Nodes (6): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), addComment()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "TaskStatus"
Cohesion: 0.29
Nodes (5): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 145 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (5): UpdateDepartmentRequest, UpdateRoleRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "ProjectManagementController"
Cohesion: 0.20
Nodes (5): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams(), ProjectManagementController

### Community 158 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (5): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 164 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

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

### Community 178 - "TaskDocumentLinker.php"
Cohesion: 0.17
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Illuminate\Auth\Access\Response"
Cohesion: 0.15
Nodes (5): CommentPolicy, SubtaskPolicy, TaskPolicy, DeactivatedTask, Illuminate\Auth\Access\Response

### Community 184 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.16
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "RichText"
Cohesion: 0.21
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 189 - "Document"
Cohesion: 0.12
Nodes (10): Document, DocumentPolicy, DocumentDependencyService, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentForIconTest(), makeDocumentSetForParity() (+2 more)

### Community 191 - "Role"
Cohesion: 0.08
Nodes (46): AccessPermission, Department, Permission, Role, up(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+38 more)

### Community 192 - "BackfillTaskCompletedAtTest.php"
Cohesion: 0.25
Nodes (3): addTaskAudit(), completedAtOf(), makeLegacyTask()

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 200 - "UpdateTaskRequest"
Cohesion: 0.13
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 202 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 204 - "run.sh"
Cohesion: 0.24
Nodes (7): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), pass(), run_deploy(), run.sh script

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "Closure"
Cohesion: 0.26
Nodes (5): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 213 - "AuditLog"
Cohesion: 0.12
Nodes (9): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, latestEntry() (+1 more)

### Community 217 - "config"
Cohesion: 0.22
Nodes (3): DbBackupCommand, config(), prefix()

### Community 225 - "Illuminate\Console\Command"
Cohesion: 0.27
Nodes (3): CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

### Community 229 - "DocumentAccessLevel.php"
Cohesion: 0.19
Nodes (4): DocumentUploadService, FileCategory, up(), makeStaffForDocumentCreate()

### Community 231 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 234 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

## Knowledge Gaps
- **158 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+153 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `NotificationEventType.php`, `Illuminate\Support\Collection`, `Comment`, `Task.php`, `Organization.php`, `OrgMember`, `ImportCommitService.php`, `NotificationSetting`, `ProjectManagementController`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `LoginRequest`, `ImportTemplateBuilder`, `TaskDocumentLinker.php`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Illuminate\Database\Eloquent\Model`, `RichText`, `Document`, `Role`, `Closure`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditLog`, `UserManagementController`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `Illuminate\Console\Command`, `PerformanceBaseline`, `AuditEventNotifier`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `DocumentFolder`, `Project`, `Organization`?**
  _High betweenness centrality (0.163) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Comment`, `Task.php`, `OrgMember`, `TaskStatus`, `ImportCommitService.php`, `ProjectManagementController`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `Subtask`, `TaskDocumentLinker.php`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Illuminate\Database\Eloquent\Model`, `RichText`, `Document`, `Role`, `BackfillTaskCompletedAtTest.php`, `TaskReturnAfterSaveTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditLog`, `FileCategory.php`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `DocumentFolder`, `Project`, `Organization`?**
  _High betweenness centrality (0.084) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Support\Collection`, `Task.php`, `Organization.php`, `OrgMember`, `TaskStatus`, `Illuminate\Database\Seeder`, `Illuminate\Validation\Validator`, `ProjectManagementController`, `.__invoke`, `Illuminate\Http\RedirectResponse`, `ImportBatch`, `PerformanceSeeder`, `ImportTemplateBuilder`, `TaskDocumentLinker.php`, `DashboardController`, `Illuminate\Database\Eloquent\Model`, `Document`, `Role`, `BackfillTaskCompletedAtTest.php`, `UpdateTaskRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `Illuminate\View\View`, `UserManagementController`, `TaskManagementController`, `Role.php`, `Illuminate\Console\Command`, `PerformanceBaseline`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `ValidatesTaskCode.php`, `Department.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `DocumentFolder`, `Project`?**
  _High betweenness centrality (0.076) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._