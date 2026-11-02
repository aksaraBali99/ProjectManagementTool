# Graph Report - ProjectManagementTool  (2026-11-02)

## Corpus Check
- 475 files · ~279,728 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2014 nodes · 5762 edges · 223 communities (177 shown, 46 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 391 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `77a5163f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentAccessLevel.php
- ImportValidator
- .visibleOrganizationIds
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Model
- LARAVEL_README.md
- Organization
- Project
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
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
- RichText
- User
- Illuminate\View\View
- TaskStatus
- TaskManagementController
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Department
- TaskDocumentLinker.php
- CodeLanguageClassSanitizer
- TaskObserver
- Role
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\Request
- HasAdminConfigurableColors.php
- FileStorageService
- BootstrapEnvironment
- config
- task-colors/edit.blade.php
- Document
- ImportBatch
- Illuminate\Http\RedirectResponse
- require
- OrgMember
- UpdateTaskRequest
- Illuminate\Http\UploadedFile
- psr-4
- static
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Comment
- UserManagementController.php
- NotificationEventType.php
- FileCategory.php
- keywords
- ProjectManagementController
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- DashboardController
- code-highlight.js
- AccessProfile
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSettingPolicy
- StoreTaskRequest
- AuditEventNotifier
- DocumentPolicy
- AccessProfileCache
- Illuminate\Database\Seeder
- TaskPolicy
- config
- DocumentDependencyService
- CommentPolicy
- ValidatesTaskAssignment.php
- ProjectStatus.php
- UpdateOrganizationRequest
- SubtaskPolicy
- UpdateTaskPriorityColorsRequest
- AccessProfileParityTest.php
- TaskDocumentDetachOwnershipTest.php
- post-create-project-cmd
- UpdateTaskStatusColorsRequest
- CommentMentionHighlightTest.php
- TaskIndexesTest.php
- UpdateProjectRequest
- makeMeasuredTasks

## God Nodes (most connected - your core abstractions)
1. `User` - 350 edges
2. `Organization` - 218 edges
3. `OrgMember` - 178 edges
4. `Task` - 178 edges
5. `Role` - 114 edges
6. `Project` - 102 edges
7. `Department` - 87 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `grantDept()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/AccessPermission.php
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makePreviewViewer()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskPreviewTest.php → app/Models/AccessPermission.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php
- `setUpCompletedWindowCompany()` --calls--> `AccessPermission`  [INFERRED]
  tests/Pest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (223 total, 46 thin omitted)

### Community 0 - "DocumentAccessLevel.php"
Cohesion: 0.21
Nodes (3): DocumentUploadService, FileCategory, up()

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Task"
Cohesion: 0.07
Nodes (23): Task, MentionedInCommentNotification, Illuminate\Database\Eloquent\SoftDeletes, addTaskAudit(), completedAtOf(), makeLegacyTask(), uploadDocumentForDownload(), makeKanbanCapTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.13
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

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

### Community 11 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.06
Nodes (6): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, capMixedCompany(), expectedKanbanColumnOrder(), makePreviewViewer()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Organization"
Cohesion: 0.09
Nodes (17): Organization, makeStaffForDocumentCreate(), makeLinkOnlyDocumentForDeleteTest(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument() (+9 more)

### Community 14 - "Project"
Cohesion: 0.12
Nodes (15): isAssignableStaffForProject(), Project, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeWarningTestTask(), makeClientForUploadTest(), projectRow() (+7 more)

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
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.07
Nodes (28): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), runImportValidationAndCommit() (+20 more)

### Community 86 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.07
Nodes (12): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+4 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AccessControlController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+5 more)

### Community 90 - "TaskStatus"
Cohesion: 0.23
Nodes (6): AnalyticsController, KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection, referenceTaskListOrder()

### Community 91 - "TaskManagementController"
Cohesion: 0.17
Nodes (3): isSafeReturnUrl(), resolveReturnTo(), TaskManagementController

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Department"
Cohesion: 0.08
Nodes (28): AccessPermission, Department, UserSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff() (+20 more)

### Community 108 - "TaskDocumentLinker.php"
Cohesion: 0.18
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 111 - "Role"
Cohesion: 0.07
Nodes (30): PermissionManagementController, Permission, Role, RolePolicy, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest() (+22 more)

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.21
Nodes (4): completedWindowStartFor(), showsAllCompleted(), DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.14
Nodes (10): CommentReactionController, RichTextAudioController, RichTextDocumentController, RichTextVideoController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure (+2 more)

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Document"
Cohesion: 0.17
Nodes (5): DocumentController, Document, DocumentAccessLevel, Illuminate\Http\Response, makeDocumentForIconTest()

### Community 134 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (5): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "static"
Cohesion: 0.18
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.12
Nodes (16): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), capExpectedSections() (+8 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Comment"
Cohesion: 0.19
Nodes (3): Comment, CommentObserver, addComment()

### Community 164 - "FileCategory.php"
Cohesion: 0.23
Nodes (4): PastedMediaNamer, fileFor(), FileCategory, UploadedFile

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
Nodes (5): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 174 - "PerformanceSeeder"
Cohesion: 0.06
Nodes (9): AbandonStaleImportBatches, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, CalendarController, Carbon, PerformanceSeeder, Illuminate\Console\Command (+1 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.11
Nodes (12): DocumentFolderController, LinkPreviewController, RichTextImageController, TaskDocumentController, TaskFolderController, DocumentFolder, Illuminate\Http\JsonResponse, makeWarningTestFolder() (+4 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 199 - "AccessProfileCache"
Cohesion: 0.17
Nodes (5): FlushAccessProfileCache, AppServiceProvider, CompanyRoleSyncer, AccessProfileCache, Illuminate\Support\ServiceProvider

### Community 200 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 202 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 206 - "ProjectStatus.php"
Cohesion: 0.11
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 212 - "AccessProfileParityTest.php"
Cohesion: 0.47
Nodes (5): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays(), grantDept()

### Community 213 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 214 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 220 - "makeMeasuredTasks"
Cohesion: 0.50
Nodes (4): countRequestQueries(), makeMeasuredTasks(), measurePageGrowth(), countQueries()

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **46 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentAccessLevel.php`, `ImportValidator`, `.visibleOrganizationIds`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `Document`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `OrgMember`, `Project`, `Organization`, `static`, `Illuminate\Support\Collection`, `Comment`, `UserManagementController.php`, `NotificationEventType.php`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Illuminate\Http\JsonResponse`, `NotificationSettingPolicy`, `AuditEventNotifier`, `DocumentPolicy`, `AccessProfileCache`, `TaskPolicy`, `DocumentDependencyService`, `CommentPolicy`, `ProjectStatus.php`, `SubtaskPolicy`, `AccessProfileParityTest.php`, `TaskDocumentDetachOwnershipTest.php`, `RichText`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Department.php`, `makeMeasuredTasks`, `Department`, `TaskDocumentLinker.php`, `Role`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `BootstrapEnvironment`, `Task.php`?**
  _High betweenness centrality (0.152) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.visibleOrganizationIds`, `Task`, `Document`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `OrgMember`, `Project`, `Illuminate\Support\Collection`, `Illuminate\Validation\Validator`, `UserManagementController.php`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Illuminate\Http\JsonResponse`, `StoreTaskRequest`, `Illuminate\Database\Seeder`, `AccessProfileParityTest.php`, `TaskDocumentDetachOwnershipTest.php`, `User`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Department.php`, `Department`, `TaskDocumentLinker.php`, `Role`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Task.php`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `DocumentAccessLevel.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `ImportBatch`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `OrgMember`, `Project`, `Organization`, `static`, `Illuminate\Support\Collection`, `Comment`, `FileCategory.php`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `DashboardController`, `Pest.php`, `Illuminate\Http\JsonResponse`, `TaskPolicy`, `DocumentDependencyService`, `CommentPolicy`, `ValidatesTaskAssignment.php`, `SubtaskPolicy`, `RichText`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Department.php`, `makeMeasuredTasks`, `Department`, `TaskDocumentLinker.php`, `TaskObserver`, `Role`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `Task.php`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 27 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 27 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._