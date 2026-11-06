# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 519 files · ~323,497 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2247 nodes · 6299 edges · 232 communities (187 shown, 45 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 419 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `8dbe410e`
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
- Task.php
- LARAVEL_README.md
- RuntimeException
- OrgMember
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- Illuminate\Http\JsonResponse
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
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\View\View
- BootstrapEnvironment
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- web.php
- CodeLanguageClassSanitizer
- TaskManagementController.php
- StoreTaskRequest
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- FileStorageException
- UserManagementController
- DocumentFolder
- .attach
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- AccessProfile
- require
- NotificationSetting
- AuditLog
- psr-4
- PermissionManagementController.php
- UpdateUserRequest
- Illuminate\Validation\Validator
- OrganizationManagementController.php
- TaskStatus
- ProjectManagementController
- TagsImportBatch.php
- CalendarController.php
- keywords
- Project
- buildEmojiPicker
- Illuminate\Http\RedirectResponse
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- Illuminate\Foundation\Http\FormRequest
- DashboardController
- code-highlight.js
- Illuminate\Auth\Access\Response
- Document
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- TaskDocumentDetachOwnershipTest.php
- Role
- User
- deploy.sh
- UpdateTaskRequest
- check-deploy-config.sh
- release-notes-lib.sh
- UpdateTaskPriorityColorsRequest
- run.sh
- TaskReturnAfterSaveTest.php
- StoreProjectRequest
- release.md
- UpdateProjectRequest
- CommentMentionHighlightTest.php
- UpdateTaskStatusColorsRequest
- config
- _task.blade.php
- release-notes-entry.sh
- TaskIndexesTest.php
- next-version.sh
- release-check.sh
- release-tasks.sh
- ResolvesReturnTo.php
- AccessProfileParityTest.php
- release-notes-body.sh
- release-notes-check.sh
- release-notes/index.blade.php
- FileCategory.php

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
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `giveCompanyATask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Organizations/CompanyCodeFormTest.php → app/Models/Department.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (232 total, 45 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.06
Nodes (25): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest, Organization, DepartmentSeeder, makeStaffForDocumentCreate(), makeClientForDeleteTest() (+17 more)

### Community 1 - "ImportValidator"
Cohesion: 0.06
Nodes (12): AbandonStaleImportBatches, ImportBatch, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+4 more)

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.05
Nodes (26): Task, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), addTaskAudit(), completedAtOf(), makeLegacyTask(), uploadDocumentForDownload() (+18 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.07
Nodes (7): CommentReaction, bootBelongsToOrganization(), organization(), parentTask(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.15
Nodes (7): AuditEventDatabaseNotification, AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.06
Nodes (6): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "RuntimeException"
Cohesion: 0.14
Nodes (7): CompanyCode, InvalidReleaseNotes, isSqlite(), numberTasks(), resolveCodes(), up(), RuntimeException

### Community 14 - "OrgMember"
Cohesion: 0.11
Nodes (6): OrgMember, CompanyRoleSyncer, findKanbanCard(), DOMElement, joinOrg(), makeStaffForPairsParity()

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.29
Nodes (6): tasks._return-to-inputs, users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "Illuminate\Http\JsonResponse"
Cohesion: 0.10
Nodes (10): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, TaskFolderController (+2 more)

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (27): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+19 more)

### Community 86 - "ReleaseNotesPageTest.php"
Cohesion: 0.10
Nodes (12): AppServiceProvider, ReleaseNotes, Illuminate\Support\ServiceProvider, duplicateTask(), twoReleases(), userWithRole(), withBothPrs(), withChore() (+4 more)

### Community 87 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.07
Nodes (15): Permission, up(), PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest() (+7 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (14): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, resolveCurrentOrganization(), Controller, DepartmentManagementController (+6 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 108 - "web.php"
Cohesion: 0.14
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, PastedMediaNamer, Closure, Symfony\Component\HttpFoundation\Response

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "TaskManagementController.php"
Cohesion: 0.19
Nodes (4): DocumentUploadService, FileCategory, up(), DocumentAccessLevel

### Community 111 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.18
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "FileStorageException"
Cohesion: 0.25
Nodes (3): FileStorageException, self, Throwable

### Community 116 - "DocumentFolder"
Cohesion: 0.12
Nodes (9): DocumentFolderController, DocumentFolder, DocumentFolderPolicy, DocumentDependencyService, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 118 - ".attach"
Cohesion: 0.23
Nodes (5): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker

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
Nodes (13): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet() (+5 more)

### Community 139 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "NotificationSetting"
Cohesion: 0.18
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 145 - "AuditLog"
Cohesion: 0.12
Nodes (7): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, latestEntry(), assertTaskDocumentAttachedAuditEntry()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 157 - "TaskStatus"
Cohesion: 0.26
Nodes (5): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 159 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 164 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "Project"
Cohesion: 0.09
Nodes (17): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeClientWithProjectAccessForDownload(), makeWarningTestTask(), makeClientForUploadTest() (+9 more)

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.10
Nodes (11): bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), Subtask, SubtaskObserver, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static, countRequestQueries() (+3 more)

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (13): CleanupStalePendingMedia, DbBackupCommand, PerformanceBaseline, SeedPerformanceData, DatabaseSeeder, OrganizationSeeder, PerformanceSeeder, RoleSeeder (+5 more)

### Community 178 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 182 - "Illuminate\Auth\Access\Response"
Cohesion: 0.17
Nodes (5): CommentPolicy, SubtaskPolicy, TaskPolicy, DeactivatedTask, Illuminate\Auth\Access\Response

### Community 184 - "Document"
Cohesion: 0.15
Nodes (8): DocumentController, TaskDocumentController, Document, Illuminate\Database\QueryException, Illuminate\Http\Request, Illuminate\Http\Response, makeDocumentForIconTest(), makeLinkerTestDocument()

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.13
Nodes (5): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer, addComment()

### Community 189 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 191 - "Role"
Cohesion: 0.11
Nodes (35): AccessPermission, Department, Role, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+27 more)

### Community 192 - "User"
Cohesion: 0.05
Nodes (19): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+11 more)

### Community 199 - "deploy.sh"
Cohesion: 0.44
Nodes (7): backup_failed(), deploy_fail(), main(), preflight_fail(), run_step(), deploy.sh script, usage()

### Community 202 - "release-notes-lib.sh"
Cohesion: 0.32
Nodes (4): require_jq(), release-notes-lib.sh script, main(), release-version.sh script

### Community 204 - "run.sh"
Cohesion: 0.17
Nodes (11): DEPLOY_TEST_LOG, entry_of(), expect_equal(), expect_exit(), fail(), notes_check(), pass(), pick() (+3 more)

### Community 208 - "release.md"
Cohesion: 0.29
Nodes (6): Checklist, Migrations in this release, Rollback plan, Special steps, Tasks and PRs included, Version

### Community 210 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 213 - "config"
Cohesion: 0.18
Nodes (5): config(), prefix(), Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 225 - "ResolvesReturnTo.php"
Cohesion: 0.70
Nodes (4): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams()

### Community 226 - "AccessProfileParityTest.php"
Cohesion: 0.60
Nodes (4): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays()

### Community 238 - "FileCategory.php"
Cohesion: 0.09
Nodes (22): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, Symfony\Component\HttpFoundation\StreamedResponse, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile (+14 more)

## Knowledge Gaps
- **162 isolated node(s):** `release-notes-lib.sh script`, `$schema`, `name`, `type`, `description` (+157 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Task.php`, `Organization.php`, `OrgMember`, `NotificationSetting`, `AuditLog`, `ProjectManagementController`, `Project`, `Illuminate\Http\RedirectResponse`, `Subtask`, `PerformanceSeeder`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Document`, `Comment`, `TaskDocumentDetachOwnershipTest.php`, `Role`, `UpdateProjectRequest`, `ReleaseNotesPageTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `BootstrapEnvironment`, `TaskManagementController`, `Role.php`, `AccessProfileParityTest.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `TaskManagementController.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `UserManagementController`, `DocumentFolder`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Support\Collection`, `Task.php`, `RuntimeException`, `Organization.php`, `OrgMember`, `Illuminate\Validation\Validator`, `OrganizationManagementController.php`, `TaskStatus`, `ProjectManagementController`, `CalendarController.php`, `Project`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Document`, `TaskDocumentDetachOwnershipTest.php`, `Role`, `User`, `ReleaseNotesPageTest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `AccessProfileParityTest.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `TaskManagementController.php`, `StoreTaskRequest`, `Illuminate\Database\Eloquent\Builder`, `UserManagementController`, `DocumentFolder`?**
  _High betweenness centrality (0.071) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `Illuminate\Support\Collection`, `Task.php`, `Organization.php`, `OrgMember`, `AuditLog`, `TaskStatus`, `ProjectManagementController`, `CalendarController.php`, `Project`, `Illuminate\Http\RedirectResponse`, `Illuminate\Http\JsonResponse`, `Subtask`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Document`, `Comment`, `Role`, `TaskReturnAfterSaveTest.php`, `config`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `web.php`, `TaskManagementController.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `DocumentFolder`, `.attach`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 72 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 72 INFERRED edges - model-reasoned connections that need verification._