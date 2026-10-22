# Graph Report - ProjectManagementTool  (2026-10-22)

## Corpus Check
- 455 files · ~263,385 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1936 nodes · 5510 edges · 200 communities (169 shown, 31 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 370 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6a748f44`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentFolder
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
- OrgMember
- LARAVEL_README.md
- ProjectManagementController
- Organization
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
- AccessProfile
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
- TaskStatus
- Comment
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Support\Collection
- _form.blade.php
- Department
- TaskDocumentLinker.php
- CodeLanguageClassSanitizer
- Role
- ImportBatch
- LinkPreviewController.php
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\Request
- HasAdminConfigurableColors.php
- Project
- UpdateProjectRequest
- config
- Role.php
- task-colors/edit.blade.php
- ValidatesTaskAssignment.php
- Pest.php
- Illuminate\Http\RedirectResponse
- require
- User.php
- UpdateUserRequest
- AppServiceProvider.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- RichText
- static
- NotificationEventType.php
- LoginRequest
- keywords
- DocumentController.php
- buildEmojiPicker
- TaskDocumentDetachOwnershipTest.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- StoreProjectRequest
- PerformanceSeeder
- Illuminate\Database\Eloquent\Model
- TagsImportBatch.php
- UserManagementController
- code-highlight.js
- Document
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- TaskIndexesTest.php
- UpdateRoleRequest
- CalendarController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 338 edges
2. `Organization` - 207 edges
3. `OrgMember` - 169 edges
4. `Task` - 157 edges
5. `Role` - 111 edges
6. `Project` - 92 edges
7. `Department` - 84 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

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

## Communities (200 total, 31 thin omitted)

### Community 0 - "DocumentFolder"
Cohesion: 0.16
Nodes (7): DocumentFolderController, TaskFolderController, DocumentFolder, makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (4): DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.08
Nodes (16): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeKanbanCapTask(), makePlainKanbanTasks(), emojiTaskPayload() (+8 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReactionController, CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.11
Nodes (18): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout (+10 more)

### Community 9 - "dependencies"
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "OrgMember"
Cohesion: 0.12
Nodes (4): OrgMember, makeClientForDeleteTest(), makeStaffForEditTest(), makeStaffForNewMenuTest()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Organization"
Cohesion: 0.05
Nodes (20): Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeStaffForDocumentCreate(), makeClientForEditTest(), makeStaffForFolderTest(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+12 more)

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

### Community 86 - "FileCategory.php"
Cohesion: 0.05
Nodes (32): config(), prefix(), FileStorageException, self, DocumentUploadService, FileCategory, FileStorageService, PastedMediaNamer (+24 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (19): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, UserPolicy (+11 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (6): AuditTrailController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 90 - "TaskStatus"
Cohesion: 0.21
Nodes (5): AnalyticsController, TaskStatus, TaskColorController, UpdateTaskStatusColorsRequest, referenceTaskListOrder()

### Community 91 - "Comment"
Cohesion: 0.18
Nodes (3): CommentController, Comment, addComment()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "Illuminate\Support\Collection"
Cohesion: 0.18
Nodes (11): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), DepartmentManagementController, KanbanController, Illuminate\Database\Eloquent\Collection, Illuminate\Support\Collection (+3 more)

### Community 107 - "Department"
Cohesion: 0.09
Nodes (24): AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeCommentPermissionStaff(), makeReactionEligibleStaff() (+16 more)

### Community 108 - "TaskDocumentLinker.php"
Cohesion: 0.18
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Role"
Cohesion: 0.11
Nodes (22): PermissionManagementController, Permission, Role, RolePolicy, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest() (+14 more)

### Community 111 - "ImportBatch"
Cohesion: 0.11
Nodes (8): BootstrapEnvironment, ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.09
Nodes (7): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (9): DashboardController, Collection, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Illuminate\Http\Request (+1 more)

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "Project"
Cohesion: 0.09
Nodes (16): Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeWarningTestFolder(), makeWarningTestTask(), makeClientForUploadTest() (+8 more)

### Community 119 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "ValidatesTaskAssignment.php"
Cohesion: 0.10
Nodes (5): StoreDepartmentRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 134 - "Pest.php"
Cohesion: 0.10
Nodes (20): DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, makeMeasuredTasks(), measurePageGrowth(), buildImportTestFile(), countQueries(), createImportOwner() (+12 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (6): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.16
Nodes (4): AuditLog, AuditEventNotifier, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.16
Nodes (4): SubtaskController, Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "DocumentController.php"
Cohesion: 0.24
Nodes (3): ImportController, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "TaskDocumentDetachOwnershipTest.php"
Cohesion: 0.47
Nodes (4): grantManageDocumentsForDetach(), makeClientForDetach(), makeDetachTestDocument(), makeStaffForDetach()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (13): AbandonStaleImportBatches, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PerformanceSeeder (+5 more)

### Community 177 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.05
Nodes (7): PastedMedia, TaskPriorityColor, TaskStatusColor, CompanyRoleSyncer, up(), Illuminate\Database\Eloquent\Model, expectedKanbanColumnOrder()

### Community 178 - "TagsImportBatch.php"
Cohesion: 0.26
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Document"
Cohesion: 0.08
Nodes (15): TaskDocumentController, Document, DocumentDependencyService, Attribute, Illuminate\Database\Eloquent\Casts\Attribute, makeAttachableDocumentSet(), makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload() (+7 more)

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.16
Nodes (6): DocumentController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, Illuminate\Http\JsonResponse

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 199 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **31 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Pest.php`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController`, `User.php`, `Organization`, `AuditLog`, `Subtask`, `static`, `NotificationEventType.php`, `LoginRequest`, `TaskDocumentDetachOwnershipTest.php`, `PerformanceSeeder`, `Illuminate\Database\Eloquent\Model`, `ImportTemplateBuilder`, `UserManagementController`, `Document`, `NotificationSetting`, `FileCategory.php`, `Illuminate\View\View`, `TaskStatus`, `Comment`, `AuditLog.php`, `Illuminate\Support\Collection`, `Department`, `Role`, `ImportBatch`, `LinkPreviewController.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `Project`, `UpdateProjectRequest`, `Role.php`?**
  _High betweenness centrality (0.163) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController`, `User.php`, `Illuminate\Validation\Validator`, `DocumentController.php`, `TaskDocumentDetachOwnershipTest.php`, `PerformanceSeeder`, `Illuminate\Database\Eloquent\Model`, `ImportTemplateBuilder`, `UserManagementController`, `Document`, `CalendarController.php`, `User`, `Illuminate\View\View`, `TaskStatus`, `AuditLog.php`, `Illuminate\Support\Collection`, `Department`, `Role`, `ImportBatch`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `Project`, `Role.php`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `DocumentFolder`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ValidatesTaskAssignment.php`, `Pest.php`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `User.php`, `Organization`, `AuditLog`, `Subtask`, `RichText`, `static`, `Illuminate\Database\Eloquent\Model`, `Document`, `Illuminate\Http\JsonResponse`, `CalendarController.php`, `FileCategory.php`, `User`, `TaskStatus`, `Comment`, `AuditLog.php`, `Illuminate\Support\Collection`, `Department`, `TaskDocumentLinker.php`, `Role`, `ImportBatch`, `LinkPreviewController.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `Project`, `Role.php`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._