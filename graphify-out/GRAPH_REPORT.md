# Graph Report - ProjectManagementTool  (2026-11-06)

## Corpus Check
- 517 files · ~321,903 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2241 nodes · 6295 edges · 230 communities (186 shown, 44 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 419 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4cd351f2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
- ImportValidator
- User
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
- FileCategory.php
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
- DocumentFolderPolicy
- Illuminate\View\View
- DbBackupCommand.php
- TaskManagementController
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- AccessProfile
- BootstrapEnvironment
- CodeLanguageClassSanitizer
- TaskManagementController.php
- RichText
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- FileStorageException
- UserManagementController.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Database\Eloquent\Model
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- 1. One-time setup
- Illuminate\Support\Collection
- BackfillTaskCompletedAt
- require
- TaskStatus
- AuditLog
- psr-4
- PermissionManagementController.php
- DepartmentPolicy
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ProjectManagementController
- CalendarController.php
- Illuminate\Http\RedirectResponse
- OrganizationPolicy
- keywords
- ProjectPolicy
- buildEmojiPicker
- Illuminate\Http\Request
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- UpdateRoleRequest
- DashboardController
- code-highlight.js
- Illuminate\Auth\Access\Response
- Document
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Comment
- TaskDocumentDetachOwnershipTest.php
- Role
- DocumentPolicy
- deploy.sh
- NotificationSettingPolicy
- check-deploy-config.sh
- release-notes-lib.sh
- RolePolicy
- run.sh
- TaskIndexesTest.php
- release.md
- ValidPhoneNumber.php
- CommentMentionHighlightTest.php
- DocumentEditTest.php
- config
- AccessProfileParityTest.php
- TaskViewableIdsParityTest.php
- release-notes-entry.sh
- next-version.sh
- release-check.sh
- release-tasks.sh
- ResolvesReturnTo.php
- release-notes-body.sh
- release-notes-check.sh
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

## Communities (230 total, 44 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.05
Nodes (35): Organization, Project, DepartmentSeeder, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeClientForDeleteTest() (+27 more)

### Community 1 - "ImportValidator"
Cohesion: 0.06
Nodes (12): ImportBatch, ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+4 more)

### Community 2 - "User"
Cohesion: 0.09
Nodes (14): Collection, User, AuditLogPolicy, UserPolicy, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 3 - "Task"
Cohesion: 0.04
Nodes (30): Attribute, Task, TaskObserver, DocumentDependencyService, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\SoftDeletes, messageTask(), addTaskAudit() (+22 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
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
Nodes (3): capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "FileCategory.php"
Cohesion: 0.14
Nodes (9): PastedMedia, PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile, fakePastedImage(), fakePastedVideo() (+1 more)

### Community 14 - "OrgMember"
Cohesion: 0.11
Nodes (4): OrgMember, makeStaffForFolderTest(), findKanbanCard(), DOMElement

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
Cohesion: 0.11
Nodes (12): DocumentFolderController, RichTextAudioController, RichTextImageController, RichTextVideoController, TaskDocumentController, TaskFolderController, DocumentFolder, Illuminate\Http\JsonResponse (+4 more)

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
Cohesion: 0.11
Nodes (12): AppServiceProvider, ReleaseNotes, Illuminate\Support\ServiceProvider, duplicateTask(), twoReleases(), userWithRole(), withBothPrs(), withChore() (+4 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (11): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, ReleaseNotesController (+3 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 108 - "BootstrapEnvironment"
Cohesion: 0.05
Nodes (13): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, DbBackupCommand, PerformanceBaseline, SeedPerformanceData, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged (+5 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "TaskManagementController.php"
Cohesion: 0.14
Nodes (5): DocumentUploadService, FileCategory, OpenGraphMetadataParser, TaskCodeQuery, DOMXPath

### Community 111 - "RichText"
Cohesion: 0.08
Nodes (6): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, RichText, Illuminate\Contracts\Validation\Validator, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 112 - "LinkPreview"
Cohesion: 0.13
Nodes (4): LinkPreview, LinkPreviewResult, LinkPreviewService, UrlSsrfGuard

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.17
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 114 - "FileStorageException"
Cohesion: 0.25
Nodes (3): FileStorageException, self, Throwable

### Community 115 - "UserManagementController.php"
Cohesion: 0.13
Nodes (3): UserManagementController, UpdateUserPasswordRequest, UpdateUserRequest

### Community 118 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

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
Nodes (13): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet() (+5 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "TaskStatus"
Cohesion: 0.29
Nodes (5): KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 145 - "AuditLog"
Cohesion: 0.08
Nodes (11): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, TaskDocumentLinker, up() (+3 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.09
Nodes (8): TaskColorController, UpdateTaskPriorityColorsRequest, UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (9): OrganizationManagementController, UpdateDepartmentRequest, UploadImportRequest, taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), StoreOrganizationRequest, UpdateOrganizationRequest (+1 more)

### Community 158 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.09
Nodes (8): AccessControlController, GoogleAuthController, NotificationSettingsController, StoreProjectRequest, UpdateProjectRequest, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (5): CommentReactionController, DocumentController, LinkPreviewController, RichTextDocumentController, Illuminate\Http\Request

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.15
Nodes (8): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (16): bootBelongsToOrganization(), bootFlushesAccessProfiles(), CompanyCode, UserFactory, isSqlite(), numberTasks(), resolveCodes(), up() (+8 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Illuminate\Auth\Access\Response"
Cohesion: 0.13
Nodes (5): CommentPolicy, SubtaskPolicy, TaskPolicy, DeactivatedTask, Illuminate\Auth\Access\Response

### Community 184 - "Document"
Cohesion: 0.11
Nodes (15): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, Document, InvalidReleaseNotes, Illuminate\Database\QueryException, Illuminate\Http\Response (+7 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Comment"
Cohesion: 0.15
Nodes (7): CommentController, Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), addComment()

### Community 189 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 191 - "Role"
Cohesion: 0.08
Nodes (46): AccessPermission, Department, Permission, Role, up(), PermissionSeeder, makeStaffOnCalendar(), makeEligibleStaffMember() (+38 more)

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

### Community 210 - "ValidPhoneNumber.php"
Cohesion: 0.25
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 212 - "DocumentEditTest.php"
Cohesion: 0.40
Nodes (4): grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest()

### Community 213 - "config"
Cohesion: 0.22
Nodes (3): config(), prefix(), Attribute

### Community 214 - "AccessProfileParityTest.php"
Cohesion: 0.60
Nodes (4): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays()

### Community 215 - "TaskViewableIdsParityTest.php"
Cohesion: 0.40
Nodes (4): assertViewParity(), makeClientForParity(), makeManagementForParity(), makeStaffForParity()

### Community 225 - "ResolvesReturnTo.php"
Cohesion: 0.70
Nodes (4): checkedReturnTo(), isSafeReturnUrl(), resolveReturnTo(), returnToParams()

### Community 238 - "Illuminate\Http\UploadedFile"
Cohesion: 0.11
Nodes (15): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+7 more)

## Knowledge Gaps
- **159 isolated node(s):** `release-notes-lib.sh script`, `$schema`, `name`, `type`, `description` (+154 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Task.php`, `Role.php`, `OrgMember`, `AuditLog`, `DepartmentPolicy`, `ProjectManagementController`, `Illuminate\Http\RedirectResponse`, `OrganizationPolicy`, `ProjectPolicy`, `Illuminate\Http\JsonResponse`, `Subtask`, `PerformanceSeeder`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Document`, `Comment`, `TaskDocumentDetachOwnershipTest.php`, `Role`, `DocumentPolicy`, `NotificationSettingPolicy`, `RolePolicy`, `ValidPhoneNumber.php`, `DocumentEditTest.php`, `AccessProfileParityTest.php`, `DocumentFolderPolicy`, `Illuminate\View\View`, `ReleaseNotesPageTest.php`, `TaskViewableIdsParityTest.php`, `TaskManagementController`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `TaskManagementController.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `UserManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project.php`?**
  _High betweenness centrality (0.131) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `User`, `Task`, `Illuminate\Support\Collection`, `Task.php`, `Role.php`, `OrgMember`, `TaskStatus`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `ProjectManagementController`, `CalendarController.php`, `Illuminate\Http\RedirectResponse`, `OrganizationPolicy`, `Illuminate\Http\Request`, `Illuminate\Http\JsonResponse`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Document`, `TaskDocumentDetachOwnershipTest.php`, `Role`, `DocumentEditTest.php`, `AccessProfileParityTest.php`, `ReleaseNotesPageTest.php`, `Illuminate\View\View`, `TaskViewableIdsParityTest.php`, `TaskManagementController`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `TaskManagementController.php`, `RichText`, `Illuminate\Database\Eloquent\Builder`, `UserManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `Project.php`?**
  _High betweenness centrality (0.080) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `Illuminate\Support\Collection`, `Task.php`, `FileCategory.php`, `OrgMember`, `TaskStatus`, `Role.php`, `AuditLog`, `ProjectManagementController`, `CalendarController.php`, `Illuminate\Http\Request`, `Illuminate\Http\JsonResponse`, `Subtask`, `PerformanceSeeder`, `DashboardController`, `Illuminate\Auth\Access\Response`, `Pest.php`, `Document`, `Comment`, `Role`, `config`, `TaskViewableIdsParityTest.php`, `Illuminate\View\View`, `TaskManagementController`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `TaskManagementController.php`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `Project.php`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 72 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 72 INFERRED edges - model-reasoned connections that need verification._