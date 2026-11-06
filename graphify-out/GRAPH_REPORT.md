# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 501 files · ~311,018 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2171 nodes · 6108 edges · 229 communities (189 shown, 40 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `07392196`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- web.php
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
- Illuminate\Http\Request
- Role
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
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
- Comment.php
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
- FileCategory.php
- static
- CodeLanguageClassSanitizer
- ValidatesTaskCode.php
- Department.php
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\JsonResponse
- DocumentFolder
- Project
- StoreDepartmentRequest
- OrgMember
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- TagsImportBatch.php
- require
- Organization.php
- TaskStatus
- Illuminate\Database\Seeder
- psr-4
- NotificationSetting
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ProjectManagementController
- CalendarController.php
- Illuminate\Http\RedirectResponse
- UserManagementController.php
- keywords
- FileStorageException
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- RuntimeException
- DashboardController
- code-highlight.js
- TaskPolicy
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- UpdateRoleRequest
- Organization
- BackfillTaskCompletedAtTest.php
- deploy.sh
- RichText
- check-deploy-config.sh
- AccessProfileParityTest.php
- TaskDocumentDetachOwnershipTest.php
- run.sh
- TaskIndexesTest.php
- TaskReturnAfterSaveTest.php
- release.md
- ValidClientUser.php
- ResolvesReturnTo.php
- StoreProjectRequest
- AuditLog
- UpdateTaskPriorityColorsRequest
- UpdateProjectRequest
- MentionedInCommentNotification
- BootstrapEnvironment
- next-version.sh
- release-check.sh
- release-tasks.sh
- CommentPermissionTest.php
- setUpCompletedWindowCompany
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
- `makeEligibleStaffMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php
- `makeIneligibleProjectMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php
- `makeProjectMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionTest.php → app/Models/AccessPermission.php
- `makeCommentPermissionStaff()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentPermissionTest.php → app/Models/AccessPermission.php
- `makeReactionEligibleStaff()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentReactionTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (229 total, 40 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.06
Nodes (12): ImportController, ImportBatch, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+4 more)

### Community 2 - "AccessProfileCache"
Cohesion: 0.06
Nodes (9): Collection, AppServiceProvider, AccessProfile, Collection, self, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Database\Eloquent\Relations\BelongsToMany (+1 more)

### Community 3 - "Task"
Cohesion: 0.07
Nodes (23): Task, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask(), makePlainKanbanTasks() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventNotifier"
Cohesion: 0.19
Nodes (4): AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

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
Cohesion: 0.07
Nodes (7): TaskPriorityColor, TaskStatusColor, up(), Illuminate\Database\Eloquent\Model, kanbanBoardCursor(), kanbanChanges(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (7): AccessControlController, resolveCurrentOrganization(), DocumentController, PermissionManagementController, RichTextDocumentController, Illuminate\Http\Request, Illuminate\Http\Response

### Community 14 - "Role"
Cohesion: 0.09
Nodes (27): Permission, Role, up(), capExpectedSections(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), grantManageDocumentsForEditTest() (+19 more)

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
Nodes (27): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), countRequestQueries() (+19 more)

### Community 86 - "Comment.php"
Cohesion: 0.15
Nodes (6): PastedMedia, PastedMediaNamer, Closure, fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 87 - "User"
Cohesion: 0.06
Nodes (22): Document, User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, RolePolicy, UserPolicy (+14 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (10): AnalyticsController, AuditTrailController, AuthenticatedSessionController, CommentReactionController, Controller, NotificationController, OrganizationManagementController, RoleManagementController (+2 more)

### Community 90 - "auth.php"
Cohesion: 0.15
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 92 - "Role.php"
Cohesion: 0.12
Nodes (4): Illuminate\Support\Facades\Notification, assignExistingTask(), createTaskWithAssignee(), companyUpdate()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 107 - "FileCategory.php"
Cohesion: 0.10
Nodes (19): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, Symfony\Component\HttpFoundation\StreamedResponse, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile (+11 more)

### Community 108 - "static"
Cohesion: 0.20
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "ValidatesTaskCode.php"
Cohesion: 0.15
Nodes (5): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest

### Community 111 - "Department.php"
Cohesion: 0.08
Nodes (4): Illuminate\Contracts\Validation\Validator, findCommentCardBody(), DOMElement, makeCompany()

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.19
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.16
Nodes (6): LinkPreviewController, RichTextAudioController, RichTextImageController, RichTextVideoController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 115 - "DocumentFolder"
Cohesion: 0.18
Nodes (4): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy

### Community 116 - "Project"
Cohesion: 0.08
Nodes (16): Project, ProjectPolicy, makeTaskForAnalytics(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeEligibleStaff(), makeLegacyTask() (+8 more)

### Community 119 - "OrgMember"
Cohesion: 0.16
Nodes (13): OrgMember, makeReactionClient(), makeReactionEligibleStaff(), makeStaffForDocumentCreate(), makeStaffForNewMenuTest(), makeClientForUploadTest(), makeStaffForUploadTest(), joinOrg() (+5 more)

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
Cohesion: 0.19
Nodes (11): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), Illuminate\Support\Collection, flattenCalendarCells(), capSorted(), assertDocumentViewParity() (+3 more)

### Community 139 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Organization.php"
Cohesion: 0.08
Nodes (3): Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Notifications\Notifiable, makeDocumentForIconTest()

### Community 143 - "TaskStatus"
Cohesion: 0.36
Nodes (4): KanbanController, TaskStatus, Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 145 - "Illuminate\Database\Seeder"
Cohesion: 0.19
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "NotificationSetting"
Cohesion: 0.18
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.16
Nodes (4): UpdateDepartmentRequest, UploadImportRequest, UpdateTaskStatusColorsRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.18
Nodes (5): GoogleAuthController, DepartmentManagementController, TaskColorController, UserManagementController, Illuminate\Http\RedirectResponse

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
Nodes (5): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.10
Nodes (6): CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up(), PerformanceSeeder

### Community 178 - "RuntimeException"
Cohesion: 0.20
Nodes (6): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, RuntimeException

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
Cohesion: 0.18
Nodes (3): CommentController, Comment, CommentPolicy

### Community 191 - "Organization"
Cohesion: 0.05
Nodes (34): AccessPermission, Department, Organization, makeStaffOnCalendar(), capMixedCompany(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeWarningTestFolder() (+26 more)

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 200 - "RichText"
Cohesion: 0.10
Nodes (4): StoreTaskRequest, UpdateTaskRequest, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 202 - "AccessProfileParityTest.php"
Cohesion: 0.47
Nodes (5): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays(), grantDept()

### Community 203 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 204 - "run.sh"
Cohesion: 0.24
Nodes (7): DEPLOY_TEST_LOG, expect_equal(), expect_exit(), fail(), pass(), run_deploy(), run.sh script

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "ValidClientUser.php"
Cohesion: 0.25
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 211 - "ResolvesReturnTo.php"
Cohesion: 0.70
Nodes (4): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams()

### Community 213 - "AuditLog"
Cohesion: 0.11
Nodes (9): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, latestEntry() (+1 more)

### Community 217 - "BootstrapEnvironment"
Cohesion: 0.05
Nodes (14): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, DbBackupCommand, PerformanceBaseline, SeedPerformanceData, config(), prefix() (+6 more)

### Community 226 - "setUpCompletedWindowCompany"
Cohesion: 0.67
Nodes (3): createImportOwner(), createOwner(), setUpCompletedWindowCompany()

### Community 229 - "DocumentUploadService"
Cohesion: 0.20
Nodes (4): DocumentUploadService, FileCategory, up(), DocumentAccessLevel

## Knowledge Gaps
- **158 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+153 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `web.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventNotifier`, `Illuminate\Support\Collection`, `Task.php`, `Organization.php`, `Role`, `NotificationSetting`, `ProjectManagementController`, `Illuminate\Http\RedirectResponse`, `UserManagementController.php`, `Subtask`, `AuditLog.php`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `TaskPolicy`, `Pest.php`, `Document.php`, `Comment`, `Organization`, `AccessProfileParityTest.php`, `TaskDocumentDetachOwnershipTest.php`, `ValidClientUser.php`, `AuditLog`, `Illuminate\View\View`, `BootstrapEnvironment`, `auth.php`, `TaskManagementController`, `Role.php`, `CommentPermissionTest.php`, `setUpCompletedWindowCompany`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Relations\HasMany`, `FileCategory.php`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `Project`, `OrgMember`?**
  _High betweenness centrality (0.164) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `web.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Task.php`, `Illuminate\Http\Request`, `Role`, `TaskStatus`, `Organization.php`, `Illuminate\Database\Seeder`, `Illuminate\Validation\Validator`, `ProjectManagementController`, `CalendarController.php`, `Illuminate\Http\RedirectResponse`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Document.php`, `BackfillTaskCompletedAtTest.php`, `AccessProfileParityTest.php`, `TaskDocumentDetachOwnershipTest.php`, `User`, `Illuminate\View\View`, `BootstrapEnvironment`, `auth.php`, `TaskManagementController`, `Role.php`, `CommentPermissionTest.php`, `setUpCompletedWindowCompany`, `Illuminate\Database\Eloquent\Relations\HasMany`, `FileCategory.php`, `ValidatesTaskCode.php`, `Department.php`, `Illuminate\Database\Eloquent\Builder`, `Project`, `OrgMember`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `web.php`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Task.php`, `Illuminate\Http\Request`, `Role`, `TaskStatus`, `Organization.php`, `ProjectManagementController`, `CalendarController.php`, `Subtask`, `AuditLog.php`, `RuntimeException`, `DashboardController`, `TaskPolicy`, `Pest.php`, `Comment`, `Organization`, `BackfillTaskCompletedAtTest.php`, `TaskReturnAfterSaveTest.php`, `AuditLog`, `Comment.php`, `User`, `MentionedInCommentNotification`, `BootstrapEnvironment`, `TaskManagementController`, `Role.php`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Relations\HasMany`, `static`, `Department.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\JsonResponse`, `DocumentFolder`, `Project`, `OrgMember`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._