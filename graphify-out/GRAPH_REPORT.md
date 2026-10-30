# Graph Report - ProjectManagementTool  (2026-10-30)

## Corpus Check
- 469 files · ~273,426 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1991 nodes · 5668 edges · 211 communities (171 shown, 40 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 382 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `64ba7a42`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- TaskManagementController.php
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
- Task.php
- LARAVEL_README.md
- DocumentFolder
- FileCategory.php
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
- AccessProfile
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- Illuminate\Support\Collection
- User
- Illuminate\View\View
- UpdateTaskStatusColorsRequest
- TaskManagementController
- Role.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Organization
- TaskDocumentLinker.php
- CodeLanguageClassSanitizer
- Pest.php
- Comment
- LinkPreview
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\Request
- HasAdminConfigurableColors.php
- UserManagementController
- config
- config
- OrgMember
- task-colors/edit.blade.php
- TaskStatus
- ImportBatch
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- Illuminate\Http\UploadedFile
- psr-4
- Illuminate\Database\Eloquent\Model
- ResolvesCurrentOrganization.php
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- RichText
- static
- NotificationEventType.php
- FileStorageService
- keywords
- AuditEventMailNotification.php
- buildEmojiPicker
- DocumentUploadService
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- AppServiceProvider.php
- CalendarController.php
- code-highlight.js
- DashboardController
- Document
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- UpdateTaskRequest
- UpdateDepartmentRequest
- UpdateOrganizationRequest
- Role
- StoreTaskRequest
- Illuminate\Testing\TestResponse
- AccessProfileParityTest.php
- .resolvePending
- .storePending
- TaskIndexesTest.php
- StoreProjectRequest
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 342 edges
2. `Organization` - 216 edges
3. `OrgMember` - 174 edges
4. `Task` - 174 edges
5. `Role` - 112 edges
6. `Project` - 96 edges
7. `Department` - 86 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makePreviewViewer()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskPreviewTest.php → app/Models/AccessPermission.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php

## Import Cycles
- None detected.

## Communities (211 total, 40 thin omitted)

### Community 0 - "TaskManagementController.php"
Cohesion: 0.13
Nodes (3): RichTextDocumentController, DocumentDependencyService, up()

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.16
Nodes (4): FlushAccessProfileCache, Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (23): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, addTaskAudit(), completedAtOf(), makeLegacyTask() (+15 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.15
Nodes (5): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

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

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "DocumentFolder"
Cohesion: 0.18
Nodes (8): DocumentFolderController, TaskFolderController, DocumentFolder, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

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
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 86 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (10): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertDocumentViewParity(), makeDocumentSetForParity() (+2 more)

### Community 87 - "User"
Cohesion: 0.04
Nodes (23): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy (+15 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+3 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Organization"
Cohesion: 0.06
Nodes (54): isAssignableStaffForProject(), AccessPermission, Department, Organization, Project, DepartmentSeeder, makeTaskForAnalytics(), makeStaffOnCalendar() (+46 more)

### Community 108 - "TaskDocumentLinker.php"
Cohesion: 0.17
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Pest.php"
Cohesion: 0.10
Nodes (20): DOMDocument, Illuminate\Foundation\Testing\TestCase, runImportValidationAndCommit(), listIds(), buildImportTestFile(), createImportOwner(), createOwner(), kanbanCardIds() (+12 more)

### Community 111 - "Comment"
Cohesion: 0.20
Nodes (6): Comment, CommentObserver, makeMeasuredTasks(), measurePageGrowth(), addComment(), countQueries()

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (9): CommentReactionController, RichTextAudioController, RichTextImageController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Illuminate\Http\Request (+1 more)

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "config"
Cohesion: 0.20
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "OrgMember"
Cohesion: 0.08
Nodes (10): OrgMember, CompanyRoleSyncer, makeClientForDeleteTest(), makeStaffForFolderTest(), makeStaffForUploadTest(), findKanbanCard(), DOMElement, joinOrg() (+2 more)

### Community 133 - "TaskStatus"
Cohesion: 0.33
Nodes (4): AnalyticsController, KanbanController, TaskStatus, Illuminate\Database\Eloquent\Collection

### Community 134 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.11
Nodes (19): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory, UploadedFile (+11 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (4): TaskPriorityColor, TaskStatusColor, up(), Illuminate\Database\Eloquent\Model

### Community 149 - "ResolvesCurrentOrganization.php"
Cohesion: 0.22
Nodes (3): AccessControlController, resolveCurrentOrganization(), UpdateProjectRequest

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 158 - "static"
Cohesion: 0.20
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "AuditEventMailNotification.php"
Cohesion: 0.33
Nodes (5): AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.05
Nodes (12): AbandonStaleImportBatches, BackfillTaskCompletedAt, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, DatabaseSeeder, OrganizationSeeder, PerformanceSeeder (+4 more)

### Community 180 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 184 - "Document"
Cohesion: 0.11
Nodes (12): DocumentController, SubtaskController, TaskDocumentController, Document, DocumentAccessLevel, Illuminate\Http\JsonResponse, Illuminate\Http\Response, makeLinkOnlyDocumentForDeleteTest() (+4 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 199 - "Role"
Cohesion: 0.09
Nodes (27): PermissionManagementController, Permission, Role, RolePolicy, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest() (+19 more)

### Community 200 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 201 - "Illuminate\Testing\TestResponse"
Cohesion: 0.60
Nodes (3): Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor()

### Community 202 - "AccessProfileParityTest.php"
Cohesion: 0.60
Nodes (4): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays()

### Community 206 - "StoreProjectRequest"
Cohesion: 0.14
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `TaskManagementController.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `TaskStatus`, `AuditLog`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Task.php`, `User.php`, `DocumentFolder`, `Illuminate\Database\Eloquent\Model`, `ResolvesCurrentOrganization.php`, `RichText`, `static`, `NotificationEventType.php`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Document`, `NotificationSetting`, `Role`, `Illuminate\Testing\TestResponse`, `AccessProfileParityTest.php`, `StoreProjectRequest`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `Organization`, `TaskDocumentLinker.php`, `Pest.php`, `Comment`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `UserManagementController`, `OrgMember`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `TaskManagementController.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `TaskStatus`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Task.php`, `User.php`, `DocumentFolder`, `Illuminate\Database\Eloquent\Model`, `ResolvesCurrentOrganization.php`, `Illuminate\Validation\Validator`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `ImportTemplateBuilder`, `CalendarController.php`, `DashboardController`, `Document`, `Role`, `StoreTaskRequest`, `AccessProfileParityTest.php`, `Illuminate\Support\Collection`, `User`, `Illuminate\View\View`, `TaskManagementController`, `Role.php`, `TaskDocumentLinker.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `UserManagementController`, `OrgMember`?**
  _High betweenness centrality (0.070) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `TaskManagementController.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `TaskStatus`, `ImportBatch`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Task.php`, `DocumentFolder`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`, `RichText`, `static`, `DocumentUploadService`, `Subtask`, `Illuminate\Database\Eloquent\Relations\HasMany`, `CalendarController.php`, `DashboardController`, `Document`, `Role`, `.resolvePending`, `.storePending`, `Illuminate\Support\Collection`, `User`, `TaskManagementController`, `Role.php`, `Organization`, `TaskDocumentLinker.php`, `Pest.php`, `Comment`, `LinkPreview`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `config`, `OrgMember`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 23 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 23 INFERRED edges - model-reasoned connections that need verification._
- **Are the 69 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 69 INFERRED edges - model-reasoned connections that need verification._