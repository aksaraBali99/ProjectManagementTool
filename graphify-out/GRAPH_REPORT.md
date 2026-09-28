# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 405 files · ~203,611 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1706 nodes · 4575 edges · 204 communities (164 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 295 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5784d684`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- StoreProjectRequest
- LARAVEL_README.md
- AppServiceProvider.php
- DepartmentManagementController.php
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- tasks/index.blade.php
- CLAUDE.md
- copilot-instructions.md
- rich-text-editor.js
- _password-input.blade.php
- ImportTemplateBuilder
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- Role
- User
- TaskManagementController
- UpdateUserRequest
- BootstrapEnvironment
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- Role.php
- _form.blade.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\View\View
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreviewService.php
- Comment
- Organization
- Document
- FileCategory.php
- FileStorageException
- Illuminate\Http\JsonResponse
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- config
- createRichTextEditor
- require
- Illuminate\Http\UploadedFile
- AuditEventNotifier
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- TaskDocumentController.php
- Illuminate\Http\Request
- HasAdminConfigurableColors.php
- NotificationSetting
- SubtaskPolicy
- buildEmojiPicker
- UpdateRoleRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- TagsImportBatch.php
- keywords
- Illuminate\Database\Eloquent\Model
- RichText
- lightbox.js
- Permission
- StoreTaskRequest
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- ClipboardMediaPasteTest.php
- static
- link-preview-extension.js
- UpdateTaskStatusColorsRequest
- DocumentFolder
- CompanyRoleRules
- LoginRequest
- .resolvePending
- post-create-project-cmd
- DocumentAccessLevel.php
- code-highlight.js
- FileStorageServiceTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 278 edges
2. `Organization` - 155 edges
3. `OrgMember` - 128 edges
4. `Task` - 118 edges
5. `Role` - 93 edges
6. `Project` - 79 edges
7. `Department` - 64 edges
8. `Document` - 61 edges
9. `AuditLog` - 42 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (204 total, 40 thin omitted)

### Community 0 - "Task"
Cohesion: 0.13
Nodes (12): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "OrgMember"
Cohesion: 0.10
Nodes (11): OrgMember, findCommentCardBody(), DOMElement, makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest() (+3 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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

### Community 11 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DepartmentManagementController.php"
Cohesion: 0.14
Nodes (3): StoreDepartmentRequest, UpdateDepartmentRequest, Illuminate\Contracts\Validation\Validator

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._description-field, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (19): Audio, FileChip, ResizableImage, basenameNoExtension(), buildClipboardMediaPaste(), buildDragDropUpload(), buildImageUpload(), buildLinkBar() (+11 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.09
Nodes (21): ImportController, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase (+13 more)

### Community 86 - "Role"
Cohesion: 0.11
Nodes (26): AnalyticsController, AccessPermission, Department, Role, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember() (+18 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (16): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+8 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.12
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.13
Nodes (13): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), checkFileChipStatuses() (+5 more)

### Community 108 - "Illuminate\View\View"
Cohesion: 0.15
Nodes (5): NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Organization"
Cohesion: 0.07
Nodes (27): DepartmentManagementController, isAssignableStaffForProject(), Organization, Project, DepartmentSeeder, makeTaskForAnalytics(), makeReactionClient(), makeStaffForDocumentCreate() (+19 more)

### Community 115 - "Document"
Cohesion: 0.16
Nodes (6): DocumentController, Document, DocumentDependencyService, DocumentAccessLevel, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 116 - "FileCategory.php"
Cohesion: 0.20
Nodes (4): PastedMedia, PastedMediaNamer, Closure, RuntimeException

### Community 118 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 119 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (7): Controller, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.16
Nodes (5): AccessControlController, AuthenticatedSessionController, GoogleAuthController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.14
Nodes (13): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakeDocumentFile() (+5 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "TaskDocumentController.php"
Cohesion: 0.18
Nodes (4): DocumentAlreadyAttachedException, self, TaskDocumentController, TaskDocumentLinker

### Community 159 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (4): AuditTrailController, CommentReactionController, UpdateTaskRequest, Illuminate\Http\Request

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "NotificationSetting"
Cohesion: 0.18
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (12): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells() (+4 more)

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "TagsImportBatch.php"
Cohesion: 0.26
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 180 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.24
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.14
Nodes (13): PermissionManagementController, Permission, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest() (+5 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "ClipboardMediaPasteTest.php"
Cohesion: 0.40
Nodes (3): fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 193 - "DocumentFolder"
Cohesion: 0.23
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 198 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 200 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 201 - "FileStorageServiceTest.php"
Cohesion: 0.50
Nodes (3): fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `StoreProjectRequest`, `User.php`, `AuditEventNotifier`, `NotificationEventType.php`, `UserManagementController`, `Illuminate\Http\Request`, `NotificationSetting`, `Priority.php`, `SubtaskPolicy`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Permission`, `static`, `DocumentFolder`, `LoginRequest`, `DocumentAccessLevel.php`, `Role`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Organization`, `Document`, `FileCategory.php`?**
  _High betweenness centrality (0.146) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `User.php`, `DepartmentManagementController.php`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController`, `Illuminate\Http\Request`, `Priority.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Model`, `Permission`, `CompanyRoleRules`, `Role`, `User`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\View\View`, `CalendarController.php`, `ImportBatch`, `Document`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Subtask`, `TaskDocumentController.php`, `Illuminate\Http\Request`, `Priority.php`, `SubtaskPolicy`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Model`, `Permission`, `static`, `.resolvePending`, `DocumentAccessLevel.php`, `Role`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CalendarController.php`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Organization`, `Document`, `FileCategory.php`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 51 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 51 INFERRED edges - model-reasoned connections that need verification._