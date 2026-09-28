# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 405 files · ~203,611 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1716 nodes · 4570 edges · 194 communities (159 shown, 35 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 292 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5825a2dc`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- UpdateProjectRequest
- LARAVEL_README.md
- AppServiceProvider.php
- App\Http\Requests\Tasks\StoreTaskRequest
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
- Organization
- User
- TaskManagementController
- UpdateUserRequest
- BootstrapEnvironment
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- App\Models\Role
- _form.blade.php
- App\Models\Project
- CommentPolicy
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreviewService.php
- Comment
- Role
- Document
- FileCategory.php
- PermissionManagementController
- Illuminate\Http\JsonResponse
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- config
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- StoreProjectRequest
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- TaskDocumentController
- Illuminate\Http\Request
- HasAdminConfigurableColors.php
- NotificationSetting
- App\Models\Task
- UpdateTaskPriorityColorsRequest
- buildEmojiPicker
- link-preview-thumbnail.js
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- AuditEventNotifier
- keywords
- Illuminate\Database\Eloquent\Model
- lightbox.js
- Permission
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- static
- link-preview-extension.js
- DocumentFolder
- CompanyRoleRules
- LoginRequest
- DocumentAccessLevel.php

## God Nodes (most connected - your core abstractions)
1. `User` - 271 edges
2. `Organization` - 149 edges
3. `OrgMember` - 125 edges
4. `Task` - 104 edges
5. `Role` - 89 edges
6. `Project` - 73 edges
7. `Department` - 64 edges
8. `Document` - 53 edges
9. `ImportValidator` - 42 edges
10. `AuditLog` - 42 edges

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

## Communities (194 total, 35 thin omitted)

### Community 0 - "Task"
Cohesion: 0.09
Nodes (13): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+5 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "OrgMember"
Cohesion: 0.11
Nodes (13): OrgMember, CompanyRoleSyncer, findCommentCardBody(), DOMElement, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest() (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.20
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

### Community 11 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "App\Http\Requests\Tasks\StoreTaskRequest"
Cohesion: 0.10
Nodes (6): StoreDepartmentRequest, App\Http\Requests\Tasks\StoreTaskRequest, StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks, users._unsaved-changes-guard

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (19): Audio, FileChip, ResizableImage, basenameNoExtension(), buildClipboardMediaPaste(), buildDragDropUpload(), buildImageUpload(), buildLinkBar() (+11 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "Organization"
Cohesion: 0.07
Nodes (27): AccessPermission, Department, Organization, DepartmentSeeder, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+19 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (14): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, SubtaskPolicy, UserPolicy (+6 more)

### Community 88 - "TaskManagementController"
Cohesion: 0.27
Nodes (4): Organization, Task, TaskManagementController, Project

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.12
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 107 - "App\Models\Project"
Cohesion: 0.13
Nodes (7): App\Models\Document, App\Models\Project, Illuminate\Database\Eloquent\Relations\BelongsToMany, makeAttachTestDocument(), Document, makePickerDocument(), Document

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.23
Nodes (5): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.12
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Role"
Cohesion: 0.07
Nodes (24): AnalyticsController, Project, Role, RolePolicy, makeReactionClient(), makeClientWithProjectAccessForDownload(), createOwnerForGrant(), grantManageDocuments() (+16 more)

### Community 115 - "Document"
Cohesion: 0.11
Nodes (9): DocumentAlreadyAttachedException, self, DocumentController, Document, DocumentDependencyService, TaskDocumentLinker, DocumentAccessLevel, Illuminate\Http\Response (+1 more)

### Community 116 - "FileCategory.php"
Cohesion: 0.14
Nodes (8): FileStorageException, self, PastedMediaNamer, RuntimeException, fileFor(), FileCategory, UploadedFile, Throwable

### Community 119 - "Illuminate\Http\JsonResponse"
Cohesion: 0.18
Nodes (7): LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, Closure, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (9): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, NotificationSettingsController, OrganizationManagementController, RoleManagementController (+1 more)

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
Cohesion: 0.11
Nodes (16): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.19
Nodes (4): AuditLog, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 149 - "Subtask"
Cohesion: 0.19
Nodes (5): SubtaskController, App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, isAssignableStaffForProject(), Subtask, SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "TaskDocumentController"
Cohesion: 0.40
Nodes (3): Document, TaskDocumentController, Controller

### Community 159 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (10): AuditTrailController, CommentReactionController, DashboardController, Collection, ImportController, NotificationController, SettingsController, TaskColorController (+2 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (10): App\Http\Controllers\Concerns\BuildsAssigneeOptions, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells() (+2 more)

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.20
Nodes (6): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "AuditEventNotifier"
Cohesion: 0.21
Nodes (5): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 180 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.12
Nodes (15): Permission, up(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest(), grantViewDocumentsForLinkedTasksTest(), makeDocumentForLinkedTasksTest() (+7 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 193 - "DocumentFolder"
Cohesion: 0.23
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

## Knowledge Gaps
- **133 isolated node(s):** `tasks._description-field`, `tasks._subtasks`, `tasks._documents`, `tasks._comments`, `users._unsaved-changes-guard` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `UpdateProjectRequest`, `App\Models\User`, `AuditLog`, `Subtask`, `UserManagementController`, `Illuminate\Http\Request`, `NotificationSetting`, `App\Models\Task`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Permission`, `static`, `DocumentFolder`, `LoginRequest`, `DocumentAccessLevel.php`, `Organization`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `App\Models\Project`, `CommentPolicy`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Role`, `Document`, `FileCategory.php`?**
  _High betweenness centrality (0.158) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `App\Models\User`, `Subtask`, `Illuminate\Http\Request`, `App\Models\Task`, `Illuminate\Support\Collection`, `AuditEventNotifier`, `Illuminate\Database\Eloquent\Model`, `Permission`, `static`, `DocumentAccessLevel.php`, `Organization`, `User`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `CalendarController.php`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Role`, `Document`, `FileCategory.php`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `App\Http\Requests\Tasks\StoreTaskRequest`, `UserManagementController`, `Illuminate\Http\Request`, `App\Models\Task`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Model`, `Permission`, `CompanyRoleRules`, `User`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `App\Models\Project`, `CalendarController.php`, `ImportBatch`, `Role`, `Document`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 20 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **What connects `tasks._description-field`, `tasks._subtasks`, `tasks._documents` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._