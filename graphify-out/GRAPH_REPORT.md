# Graph Report - ProjectManagementTool  (2026-10-30)

## Corpus Check
- 470 files · ~273,891 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1993 nodes · 5673 edges · 218 communities (175 shown, 43 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 383 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `8b4ad451`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Document
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
- Illuminate\Database\Eloquent\Model
- LARAVEL_README.md
- Organization
- web.php
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
- RichText
- User
- Illuminate\View\View
- TaskStatus
- TaskManagementController
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- Role
- TaskDocumentLinker.php
- CodeLanguageClassSanitizer
- Pest.php
- OrgMember
- LinkPreviewController.php
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- UserManagementController
- BootstrapEnvironment
- config
- Task.php
- task-colors/edit.blade.php
- static
- ImportBatch
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- FileCategory.php
- psr-4
- ClipboardMediaPasteTest.php
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Comment
- TagsImportBatch.php
- AuditLog
- FileStorageService
- keywords
- ValidatesTaskAssignment.php
- buildEmojiPicker
- TaskManagementController.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- ResolvesCurrentOrganization.php
- code-highlight.js
- DashboardController
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- UpdateTaskRequest
- DepartmentManagementController.php
- AppServiceProvider.php
- DocumentEditTest.php
- StoreTaskRequest
- Illuminate\Testing\TestResponse
- UpdateTaskPriorityColorsRequest
- DocumentDependencyService
- CompanyRoleRules
- TaskIndexesTest.php
- StoreProjectRequest
- .resolvePending
- .storePending
- .storePending
- .storePending
- UpdateProjectRequest
- post-create-project-cmd
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 342 edges
2. `Organization` - 217 edges
3. `OrgMember` - 174 edges
4. `Task` - 174 edges
5. `Role` - 112 edges
6. `Project` - 96 edges
7. `Department` - 86 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (218 total, 43 thin omitted)

### Community 0 - "Document"
Cohesion: 0.16
Nodes (7): DocumentController, TaskDocumentController, Document, DocumentAccessLevel, Illuminate\Http\Request, Illuminate\Http\Response, makeDocumentForIconTest()

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (25): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, addTaskAudit(), completedAtOf(), makeLegacyTask() (+17 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.21
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.07
Nodes (4): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Organization"
Cohesion: 0.05
Nodes (37): DepartmentManagementController, Organization, Project, DepartmentSeeder, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard() (+29 more)

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
Cohesion: 0.22
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 86 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.05
Nodes (23): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+15 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController, OrganizationManagementController, ProjectManagementController (+4 more)

### Community 90 - "TaskStatus"
Cohesion: 0.18
Nodes (6): AnalyticsController, TaskStatus, TaskColorController, UpdateTaskStatusColorsRequest, up(), referenceTaskListOrder()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "Role"
Cohesion: 0.08
Nodes (45): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), PermissionSeeder, makeStaffOnCalendar() (+37 more)

### Community 108 - "TaskDocumentLinker.php"
Cohesion: 0.18
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Pest.php"
Cohesion: 0.08
Nodes (23): DOMDocument, Illuminate\Foundation\Testing\TestCase, makeMeasuredTasks(), measurePageGrowth(), runImportValidationAndCommit(), listIds(), buildImportTestFile(), countQueries() (+15 more)

### Community 111 - "OrgMember"
Cohesion: 0.10
Nodes (9): OrgMember, CompanyRoleSyncer, Illuminate\Support\Facades\Notification, makeClientForDeleteTest(), makeStaffForDocumentList(), makeStaffForUploadTest(), findKanbanCard(), DOMElement (+1 more)

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.16
Nodes (6): completedWindowStartFor(), showsAllCompleted(), KanbanController, DateTimeInterface, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Collection

### Community 115 - "Closure"
Cohesion: 0.22
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "BootstrapEnvironment"
Cohesion: 0.06
Nodes (10): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, config(), prefix(), Attribute (+2 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "static"
Cohesion: 0.20
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 134 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "FileCategory.php"
Cohesion: 0.11
Nodes (17): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fileFor(), FileCategory (+9 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "ClipboardMediaPasteTest.php"
Cohesion: 0.22
Nodes (4): PastedMedia, fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (11): BackfillTaskCompletedAt, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet() (+3 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Comment"
Cohesion: 0.16
Nodes (3): Comment, CommentPolicy, addComment()

### Community 158 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 159 - "AuditLog"
Cohesion: 0.14
Nodes (6): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "TaskManagementController.php"
Cohesion: 0.23
Nodes (3): DocumentUploadService, FileCategory, up()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.22
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.10
Nodes (6): DatabaseSeeder, OrganizationSeeder, PerformanceSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 180 - "ResolvesCurrentOrganization.php"
Cohesion: 0.25
Nodes (5): AccessControlController, CalendarController, Carbon, resolveCurrentOrganization(), Illuminate\Support\Carbon

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.13
Nodes (11): CommentReactionController, DocumentFolderController, RichTextDocumentController, TaskFolderController, DocumentFolder, Illuminate\Http\JsonResponse, makeWarningTestFolder(), makeLinkerFolderTestFolder() (+3 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 199 - "DocumentEditTest.php"
Cohesion: 0.40
Nodes (4): grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest()

### Community 201 - "Illuminate\Testing\TestResponse"
Cohesion: 0.60
Nodes (3): Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor()

### Community 206 - "StoreProjectRequest"
Cohesion: 0.14
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 214 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **43 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Document`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `static`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Organization`, `User.php`, `Illuminate\Support\Collection`, `Comment`, `AuditLog`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `LoginRequest`, `ImportTemplateBuilder`, `ResolvesCurrentOrganization.php`, `DashboardController`, `Illuminate\Http\JsonResponse`, `NotificationSetting`, `DocumentEditTest.php`, `Illuminate\Testing\TestResponse`, `DocumentDependencyService`, `StoreProjectRequest`, `RichText`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Role.php`, `Role`, `TaskDocumentLinker.php`, `Pest.php`, `OrgMember`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `UserManagementController`, `BootstrapEnvironment`, `Task.php`?**
  _High betweenness centrality (0.166) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Document`, `ImportValidator`, `AccessProfileCache`, `Task`, `ImportBatch`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `User.php`, `Illuminate\Support\Collection`, `Illuminate\Foundation\Http\FormRequest`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `ImportTemplateBuilder`, `ResolvesCurrentOrganization.php`, `DashboardController`, `Illuminate\Http\JsonResponse`, `DepartmentManagementController.php`, `DocumentEditTest.php`, `CompanyRoleRules`, `User`, `Illuminate\View\View`, `TaskStatus`, `TaskManagementController`, `Role.php`, `Role`, `TaskDocumentLinker.php`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `UserManagementController`, `BootstrapEnvironment`, `Task.php`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Document`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `static`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Organization`, `web.php`, `FileCategory.php`, `Illuminate\Support\Collection`, `Comment`, `AuditLog`, `ValidatesTaskAssignment.php`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `ResolvesCurrentOrganization.php`, `DashboardController`, `Illuminate\Http\JsonResponse`, `DocumentDependencyService`, `.resolvePending`, `.storePending`, `.storePending`, `.storePending`, `RichText`, `User`, `TaskStatus`, `TaskManagementController`, `Role.php`, `Role`, `TaskDocumentLinker.php`, `Pest.php`, `OrgMember`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `BootstrapEnvironment`, `Task.php`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 24 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 24 INFERRED edges - model-reasoned connections that need verification._
- **Are the 69 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 69 INFERRED edges - model-reasoned connections that need verification._