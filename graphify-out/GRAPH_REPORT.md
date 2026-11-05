# Graph Report - ProjectManagementTool  (2026-11-05)

## Corpus Check
- 498 files · ~306,408 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2149 nodes · 6071 edges · 224 communities (182 shown, 42 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `db81f23d`
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
- Task.php
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
- Project
- User
- Illuminate\View\View
- auth.php
- TaskManagementController
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Document
- _form.blade.php
- FileCategory.php
- ProjectManagementController
- CodeLanguageClassSanitizer
- Organization
- OrgMember
- LinkPreviewController.php
- Illuminate\Database\Eloquent\Builder
- DocumentFolder
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- StoreTaskRequest
- Illuminate\Database\Eloquent\Model
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Http\RedirectResponse
- require
- Organization.php
- BackfillTaskCompletedAtTest.php
- static
- psr-4
- NotificationSetting
- AccessProfile
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- TaskPolicy
- ResolvesCurrentOrganization.php
- TaskObserver
- ImportBatch
- keywords
- TaskManagementController.php
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- TaskDocumentLinker.php
- DashboardController
- code-highlight.js
- Illuminate\Database\Eloquent\Casts\Attribute
- PermissionManagementController.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- RichText
- Department
- deploy.sh
- UpdateTaskRequest
- UpdateDepartmentRequest
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- run.sh
- TaskIndexesTest.php
- StoreProjectRequest
- release.md
- .resolvePending
- post-create-project-cmd
- 2026_08_17_001200_create_documents_table.php
- AuditLog
- MentionedInCommentNotification
- Illuminate\Console\Command
- next-version.sh
- release-check.sh
- release-tasks.sh

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

## Communities (224 total, 42 thin omitted)

### Community 0 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (14): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+6 more)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.11
Nodes (6): Collection, AppServiceProvider, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Support\ServiceProvider

### Community 3 - "Task"
Cohesion: 0.08
Nodes (21): Task, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask(), makePlainKanbanTasks(), emojiTaskPayload() (+13 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "Task.php"
Cohesion: 0.07
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Role"
Cohesion: 0.12
Nodes (25): Permission, Role, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest() (+17 more)

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
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.06
Nodes (30): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), countRequestQueries() (+22 more)

### Community 86 - "Project"
Cohesion: 0.08
Nodes (19): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), capMixedCompany(), makeClientWithProjectAccessForDownload(), makeWarningTestFolder(), makeWarningTestTask() (+11 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (23): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+15 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (13): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, ImportController, NotificationController, OrganizationManagementController (+5 more)

### Community 90 - "auth.php"
Cohesion: 0.16
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "Document"
Cohesion: 0.09
Nodes (14): Document, DocumentPolicy, DocumentDependencyService, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForIconTest(), makeDocumentSetForParity(), makeLinkerTestDocument() (+6 more)

### Community 107 - "FileCategory.php"
Cohesion: 0.06
Nodes (29): FileStorageException, self, DocumentUploadService, FileCategory, FileStorageService, PastedMediaNamer, StoredFile, Closure (+21 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.04
Nodes (23): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeClientForDeleteTest() (+15 more)

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.18
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "DocumentFolder"
Cohesion: 0.18
Nodes (7): TaskFolderController, DocumentFolder, DocumentFolderPolicy, makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (11): CommentReactionController, DocumentController, DocumentFolderController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController (+3 more)

### Community 118 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 119 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.19
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "1. One-time setup"
Cohesion: 0.11
Nodes (17): 1. One-time setup, 2. A normal release, 3. A hotfix, 4. Re-deploying the same commit, 5. Rollback, 6. When a deploy fails, 7. Release notes, a. Create the production branch from what's live on PROD (+9 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (4): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "static"
Cohesion: 0.20
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 149 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "ResolvesCurrentOrganization.php"
Cohesion: 0.40
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 164 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "TaskManagementController.php"
Cohesion: 0.33
Nodes (3): isSafeReturnUrl(), resolveReturnTo(), TaskCodeQuery

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.13
Nodes (5): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.06
Nodes (13): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up(), DatabaseSeeder, DepartmentSeeder, OrganizationSeeder (+5 more)

### Community 178 - "TaskDocumentLinker.php"
Cohesion: 0.18
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

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
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 191 - "Department"
Cohesion: 0.08
Nodes (26): AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff(), makeReactionEligibleStaff() (+18 more)

### Community 199 - "deploy.sh"
Cohesion: 0.33
Nodes (6): deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 204 - "run.sh"
Cohesion: 0.39
Nodes (5): expect_equal(), expect_exit(), fail(), pass(), run.sh script

### Community 206 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 211 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 213 - "AuditLog"
Cohesion: 0.11
Nodes (9): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, latestEntry() (+1 more)

### Community 217 - "Illuminate\Console\Command"
Cohesion: 0.08
Nodes (8): AbandonStaleImportBatches, CleanupStalePendingMedia, DbBackupCommand, PerformanceBaseline, SeedPerformanceData, config(), prefix(), Illuminate\Console\Command

## Knowledge Gaps
- **155 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+150 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventNotifier`, `Illuminate\Http\RedirectResponse`, `Task.php`, `UserManagementController`, `Role`, `Organization.php`, `static`, `NotificationSetting`, `TaskPolicy`, `ImportBatch`, `TaskManagementController.php`, `Subtask`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Comment`, `RichText`, `Department`, `Project.php`, `StoreProjectRequest`, `AuditLog`, `Project`, `Illuminate\View\View`, `Illuminate\Console\Command`, `auth.php`, `TaskManagementController`, `Department.php`, `Document`, `FileCategory.php`, `ProjectManagementController`, `Organization`, `OrgMember`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.139) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Support\Collection`, `ImportValidator`, `AccessProfileCache`, `Task`, `Task.php`, `Illuminate\Http\RedirectResponse`, `UserManagementController`, `Organization.php`, `BackfillTaskCompletedAtTest.php`, `Role`, `Illuminate\Validation\Validator`, `ResolvesCurrentOrganization.php`, `TaskObserver`, `ImportBatch`, `TaskManagementController.php`, `TaskStatus`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Department`, `Project.php`, `Project`, `User`, `Illuminate\View\View`, `Illuminate\Console\Command`, `auth.php`, `TaskManagementController`, `Department.php`, `Document`, `FileCategory.php`, `ProjectManagementController`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.101) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Support\Collection`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role`, `BackfillTaskCompletedAtTest.php`, `static`, `TaskPolicy`, `ResolvesCurrentOrganization.php`, `TaskObserver`, `ImportBatch`, `TaskManagementController.php`, `TaskStatus`, `Subtask`, `TaskDocumentLinker.php`, `DashboardController`, `Illuminate\Database\Eloquent\Casts\Attribute`, `Pest.php`, `Comment`, `RichText`, `Department`, `Project.php`, `.resolvePending`, `AuditLog`, `MentionedInCommentNotification`, `Project`, `Illuminate\View\View`, `Illuminate\Console\Command`, `User`, `TaskManagementController`, `Department.php`, `Document`, `FileCategory.php`, `ProjectManagementController`, `Organization`, `OrgMember`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.082) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._