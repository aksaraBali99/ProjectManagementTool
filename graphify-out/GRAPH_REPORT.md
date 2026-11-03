# Graph Report - ProjectManagementTool  (2026-11-03)

## Corpus Check
- 484 files · ~292,484 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2068 nodes · 5923 edges · 212 communities (175 shown, 37 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 404 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `947366d2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- auth.php
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
- Project.php
- ImportBatch
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- ValidatesTaskCode.php
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
- Comment
- User
- Illuminate\View\View
- NotificationSetting
- TaskManagementController
- Department.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- FileStorageException
- _form.blade.php
- DocumentController.php
- CodeLanguageClassSanitizer
- Organization
- Permission
- LinkPreviewController.php
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- FileStorageService
- StoreTaskRequest
- config
- HasAdminConfigurableColors.php
- task-colors/edit.blade.php
- Document
- UpdateTaskRequest
- Illuminate\Http\RedirectResponse
- require
- Organization.php
- CommentPolicy
- Illuminate\Http\UploadedFile
- psr-4
- static
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- AuditLog
- RuntimeException
- UserManagementController
- 2026_08_17_001200_create_documents_table.php
- keywords
- ProjectManagementController
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Subtask
- PerformanceSeeder
- LoginRequest
- .attach
- DashboardController
- code-highlight.js
- DocumentFolder
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- TagsImportBatch.php
- RichText
- UpdateRoleRequest
- UpdateTaskStatusColorsRequest
- OrgMember
- post-create-project-cmd
- AppServiceProvider.php
- config
- StoreProjectRequest
- ValidClientUser.php
- AuditEventMailNotification.php
- FileCategory.php

## God Nodes (most connected - your core abstractions)
1. `User` - 353 edges
2. `Organization` - 239 edges
3. `Task` - 189 edges
4. `OrgMember` - 180 edges
5. `Role` - 114 edges
6. `Project` - 103 edges
7. `Department` - 88 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
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

## Communities (212 total, 37 thin omitted)

### Community 0 - "auth.php"
Cohesion: 0.17
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (24): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, addTaskAudit(), completedAtOf(), makeLegacyTask(), makeKanbanCapTask() (+16 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (6): CommentReaction, bootBelongsToOrganization(), organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventNotifier"
Cohesion: 0.17
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
Nodes (6): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, capExpectedSections(), capSorted(), expectedKanbanColumnOrder()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ImportBatch"
Cohesion: 0.13
Nodes (7): AbandonStaleImportBatches, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 44 - "ValidatesTaskCode.php"
Cohesion: 0.21
Nodes (4): taskCodeMessages(), taskCodeRules(), taskCodeTakenMessage(), UpdateOrganizationRequest

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "Pest.php"
Cohesion: 0.06
Nodes (31): DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, analyticsFor(), dashboardFor(), assertCappedSections(), capDashboard(), countRequestQueries() (+23 more)

### Community 86 - "Comment"
Cohesion: 0.19
Nodes (3): CommentController, Comment, addComment()

### Community 87 - "User"
Cohesion: 0.05
Nodes (22): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, TaskPolicy, UserPolicy (+14 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (12): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController (+4 more)

### Community 90 - "NotificationSetting"
Cohesion: 0.21
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "DocumentController.php"
Cohesion: 0.22
Nodes (3): ImportController, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Organization"
Cohesion: 0.06
Nodes (64): AccessPermission, Department, Organization, Project, Role, DepartmentSeeder, makeTaskForAnalytics(), makeStaffOnCalendar() (+56 more)

### Community 111 - "Permission"
Cohesion: 0.12
Nodes (16): Permission, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), createOwnerForGrant(), grantManageDocuments(), grantManageDocumentsForLinkedTasksTest() (+8 more)

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.13
Nodes (5): completedWindowStartFor(), showsAllCompleted(), UpdateProjectRequest, DateTimeInterface, Illuminate\Database\Eloquent\Builder

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (10): CommentReactionController, DocumentController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController (+2 more)

### Community 119 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 133 - "Document"
Cohesion: 0.08
Nodes (19): Document, DocumentPolicy, DocumentDependencyService, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest() (+11 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (4): DepartmentManagementController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "Illuminate\Http\UploadedFile"
Cohesion: 0.11
Nodes (18): DocumentUploadService, FileCategory, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+10 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "static"
Cohesion: 0.28
Nodes (4): bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 149 - "Illuminate\Support\Collection"
Cohesion: 0.05
Nodes (25): BackfillTaskCompletedAt, CalendarController, Carbon, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), KanbanController (+17 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "AuditLog"
Cohesion: 0.15
Nodes (4): AuditLog, AuditEventDatabaseNotification, DocumentAccessLevel, assertTaskDocumentAttachedAuditEntry()

### Community 158 - "RuntimeException"
Cohesion: 0.28
Nodes (5): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, RuntimeException

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "ProjectManagementController"
Cohesion: 0.23
Nodes (3): isSafeReturnUrl(), resolveReturnTo(), ProjectManagementController

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Subtask"
Cohesion: 0.16
Nodes (5): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, SubtaskPolicy

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (15): CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, CompanyCode, isSqlite(), numberTasks(), resolveCodes(), up() (+7 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "DocumentFolder"
Cohesion: 0.12
Nodes (9): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentFolderPolicy, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 199 - "OrgMember"
Cohesion: 0.11
Nodes (4): OrgMember, CompanyRoleSyncer, findKanbanCard(), DOMElement

### Community 200 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 202 - "config"
Cohesion: 0.20
Nodes (5): config(), prefix(), Attribute, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 206 - "ValidClientUser.php"
Cohesion: 0.23
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 213 - "AuditEventMailNotification.php"
Cohesion: 0.33
Nodes (5): AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 215 - "FileCategory.php"
Cohesion: 0.23
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **134 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+129 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `auth.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `AuditEventNotifier`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Organization.php`, `ImportBatch`, `CommentPolicy`, `Project.php`, `Illuminate\Http\UploadedFile`, `static`, `Illuminate\Support\Collection`, `AuditLog`, `UserManagementController`, `ProjectManagementController`, `Subtask`, `PerformanceSeeder`, `LoginRequest`, `ImportTemplateBuilder`, `DashboardController`, `Pest.php`, `Subtask.php`, `DocumentFolder`, `OrgMember`, `ValidClientUser.php`, `Comment`, `Illuminate\View\View`, `NotificationSetting`, `TaskManagementController`, `Department.php`, `DocumentController.php`, `Organization`, `Permission`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `auth.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Document`, `Task.php`, `Illuminate\Http\RedirectResponse`, `Organization.php`, `ImportBatch`, `Project.php`, `Illuminate\Support\Collection`, `Illuminate\Validation\Validator`, `UserManagementController`, `ProjectManagementController`, `ValidatesTaskCode.php`, `PerformanceSeeder`, `ImportTemplateBuilder`, `DashboardController`, `DocumentFolder`, `OrgMember`, `User`, `Illuminate\View\View`, `TaskManagementController`, `Department.php`, `DocumentController.php`, `Permission`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreTaskRequest`?**
  _High betweenness centrality (0.085) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Document`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Project.php`, `ImportBatch`, `CommentPolicy`, `Illuminate\Http\UploadedFile`, `static`, `Illuminate\Support\Collection`, `AuditLog`, `ProjectManagementController`, `Subtask`, `.attach`, `DashboardController`, `Pest.php`, `DocumentFolder`, `RichText`, `OrgMember`, `config`, `Comment`, `FileCategory.php`, `Illuminate\View\View`, `User`, `TaskManagementController`, `Department.php`, `Organization`, `Permission`, `LinkPreviewController.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.071) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 29 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 29 INFERRED edges - model-reasoned connections that need verification._
- **Are the 71 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 71 INFERRED edges - model-reasoned connections that need verification._