# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 516 files · ~320,429 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2234 nodes · 6283 edges · 234 communities (187 shown, 47 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 419 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4357b103`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- TaskStatus.php
- LARAVEL_README.md
- ImportBatch.php
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
- ReleaseNotesPageTest.php
- User
- Illuminate\View\View
- FileCategory.php
- TaskManagementController
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- AuditEventNotifier
- BootstrapEnvironment
- CodeLanguageClassSanitizer
- static
- RuntimeException
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- DocumentFolder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Role
- Illuminate\Database\Seeder
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- ValidatesTaskCode.php
- require
- TaskStatus
- AuditLog
- psr-4
- Department.php
- web.php
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ProjectManagementController
- CalendarController.php
- Illuminate\Http\RedirectResponse
- CompanyCode
- keywords
- UpdateUserRequest
- buildEmojiPicker
- PermissionManagementController.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- DbBackupCommand.php
- DashboardController
- code-highlight.js
- Illuminate\Auth\Access\Response
- TaskDocumentLinker.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- StoreProjectRequest
- Department
- DocumentPolicy
- deploy.sh
- StoreDepartmentRequest
- check-deploy-config.sh
- release-notes-lib.sh
- DocumentDependencyService.php
- run.sh
- Illuminate\Console\Command
- DbBackupCommand
- release.md
- ValidClientUser.php
- Project
- post-create-project-cmd
- config
- TaskDocumentDetachOwnershipTest.php
- UpdateTaskPriorityColorsRequest
- PerformanceBaseline
- next-version.sh
- release-check.sh
- release-tasks.sh
- ResolvesReturnTo.php
- UpdateProjectRequest
- 2026_08_17_001000_create_tasks_table.php
- release-notes-body.sh
- release-notes-check.sh
- Illuminate\Database\Eloquent\Casts\Attribute
- TaskIndexesTest.php
- Illuminate\Http\UploadedFile

## God Nodes (most connected - your core abstractions)
1. `User` - 360 edges
2. `Organization` - 245 edges
3. `Task` - 206 edges
4. `OrgMember` - 187 edges
5. `Role` - 115 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 51 edges
10. `DocumentFolder` - 45 edges

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

## Communities (234 total, 47 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.06
Nodes (27): Organization, makeStaffForDocumentCreate(), makeWarningTestFolder(), makeWarningTestTask(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeStaffForNewMenuTest() (+19 more)

### Community 1 - "ImportValidator"
Cohesion: 0.06
Nodes (12): ImportBatch, ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+4 more)

### Community 2 - "AccessProfileCache"
Cohesion: 0.07
Nodes (8): Collection, AppServiceProvider, AccessProfile, Collection, self, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Support\ServiceProvider

### Community 3 - "Task"
Cohesion: 0.05
Nodes (25): Task, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), addTaskAudit(), completedAtOf(), makeKanbanCapTask(), expectedCompletedColumnOrder() (+17 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), parentTask(), Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.21
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.05
Nodes (5): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "OrgMember"
Cohesion: 0.12
Nodes (4): OrgMember, CompanyRoleSyncer, findCommentCardBody(), DOMElement

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
Cohesion: 0.09
Nodes (15): CommentReactionController, DocumentController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController (+7 more)

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (23): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+15 more)

### Community 86 - "ReleaseNotesPageTest.php"
Cohesion: 0.16
Nodes (8): ReleaseNotes, duplicateTask(), twoReleases(), withBothPrs(), withDate(), withoutBefore(), withType(), withVersion()

### Community 87 - "User"
Cohesion: 0.06
Nodes (22): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+14 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AnalyticsController, AuditTrailController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+5 more)

### Community 90 - "FileCategory.php"
Cohesion: 0.11
Nodes (11): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), PastedMediaNamer, Closure, Symfony\Component\HttpFoundation\Response (+3 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "AuditEventNotifier"
Cohesion: 0.18
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "static"
Cohesion: 0.22
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "RuntimeException"
Cohesion: 0.32
Nodes (4): FolderAlreadyLinkedException, self, InvalidReleaseNotes, RuntimeException

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.19
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "DocumentFolder"
Cohesion: 0.17
Nodes (4): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy

### Community 116 - "Role"
Cohesion: 0.07
Nodes (33): Permission, Role, up(), revokeCommentPermissionFromStaff(), capExpectedSections(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest() (+25 more)

### Community 118 - "Illuminate\Database\Seeder"
Cohesion: 0.19
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

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
Cohesion: 0.14
Nodes (14): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), Illuminate\Support\Collection, flattenCalendarCells(), capSorted(), assertAttachableParity() (+6 more)

### Community 139 - "ValidatesTaskCode.php"
Cohesion: 0.15
Nodes (5): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "TaskStatus"
Cohesion: 0.42
Nodes (3): KanbanController, TaskStatus, Illuminate\Database\Eloquent\Collection

### Community 145 - "AuditLog"
Cohesion: 0.12
Nodes (6): AuditLog, AuditEventDatabaseNotification, NotificationSettingsResolver, NotificationEventType, latestEntry(), assertTaskDocumentAttachedAuditEntry()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "web.php"
Cohesion: 0.15
Nodes (4): AccessControlController, resolveCurrentOrganization(), StoreTaskRequest, Illuminate\Http\Response

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.08
Nodes (8): AuthenticatedSessionController, NotificationSettingsController, TaskColorController, UserManagementController, NotificationSetting, NotificationSettingPolicy, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 164 - "CompanyCode"
Cohesion: 0.24
Nodes (5): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up()

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
Cohesion: 0.19
Nodes (6): Subtask, SubtaskObserver, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Illuminate\Auth\Access\Response"
Cohesion: 0.10
Nodes (6): UpdateTaskRequest, CommentPolicy, SubtaskPolicy, TaskPolicy, DeactivatedTask, Illuminate\Auth\Access\Response

### Community 184 - "TaskDocumentLinker.php"
Cohesion: 0.24
Nodes (4): DocumentAlreadyAttachedException, self, TaskDocumentLinker, Illuminate\Database\QueryException

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.15
Nodes (5): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer, addComment()

### Community 191 - "Department"
Cohesion: 0.08
Nodes (26): AccessPermission, Department, Illuminate\Contracts\Validation\Validator, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+18 more)

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 202 - "release-notes-lib.sh"
Cohesion: 0.32
Nodes (4): require_jq(), release-notes-lib.sh script, main(), release-version.sh script

### Community 204 - "run.sh"
Cohesion: 0.18
Nodes (9): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), notes_check(), pass(), pick(), run_deploy() (+1 more)

### Community 205 - "Illuminate\Console\Command"
Cohesion: 0.36
Nodes (3): CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "ValidClientUser.php"
Cohesion: 0.22
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 211 - "Project"
Cohesion: 0.09
Nodes (18): Project, makeTaskForAnalytics(), makeReactionClient(), makeReactionEligibleStaff(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest(), findKanbanCard(), makeKanbanCardAssignee() (+10 more)

### Community 212 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 215 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 225 - "ResolvesReturnTo.php"
Cohesion: 0.70
Nodes (4): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams()

### Community 231 - "Illuminate\Database\Eloquent\Casts\Attribute"
Cohesion: 0.38
Nodes (3): Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 238 - "Illuminate\Http\UploadedFile"
Cohesion: 0.06
Nodes (25): FileStorageException, self, DocumentUploadService, FileCategory, FileStorageService, StoredFile, up(), DocumentAccessLevel (+17 more)

## Knowledge Gaps
- **159 isolated node(s):** `release-notes-lib.sh script`, `$schema`, `name`, `type`, `description` (+154 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `TaskStatus.php`, `Organization.php`, `OrgMember`, `AuditLog`, `Department.php`, `web.php`, `ProjectManagementController`, `Illuminate\Http\RedirectResponse`, `Subtask`, `Document`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `TaskDocumentLinker.php`, `Comment`, `Department`, `DocumentPolicy`, `DocumentDependencyService.php`, `Illuminate\Console\Command`, `ValidClientUser.php`, `Project`, `ReleaseNotesPageTest.php`, `TaskDocumentDetachOwnershipTest.php`, `Illuminate\View\View`, `PerformanceBaseline`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `AuditEventNotifier`, `BootstrapEnvironment`, `static`, `Illuminate\Http\UploadedFile`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `Task.php`?**
  _High betweenness centrality (0.136) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Support\Collection`, `TaskStatus.php`, `ValidatesTaskCode.php`, `Organization.php`, `OrgMember`, `TaskStatus`, `Department.php`, `web.php`, `Illuminate\Validation\Validator`, `ProjectManagementController`, `CalendarController.php`, `Illuminate\Http\RedirectResponse`, `Document`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `TaskDocumentLinker.php`, `Department`, `Illuminate\Console\Command`, `Project`, `ReleaseNotesPageTest.php`, `User`, `Illuminate\View\View`, `PerformanceBaseline`, `TaskDocumentDetachOwnershipTest.php`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `Illuminate\Database\Seeder`, `Task.php`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `Illuminate\Support\Collection`, `TaskStatus.php`, `OrgMember`, `TaskStatus`, `AuditLog`, `Department.php`, `web.php`, `ProjectManagementController`, `CalendarController.php`, `Illuminate\Http\RedirectResponse`, `Document`, `Subtask`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `TaskDocumentLinker.php`, `Comment`, `Department`, `DocumentDependencyService.php`, `Project`, `config`, `FileCategory.php`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Casts\Attribute`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Illuminate\Http\UploadedFile`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role`, `Task.php`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 72 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 72 INFERRED edges - model-reasoned connections that need verification._