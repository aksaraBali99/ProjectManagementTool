# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 418 files · ~219,108 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1767 nodes · 4783 edges · 200 communities (160 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 291 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6a90c444`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- ImportValidator
- .boardOrganizationIds
- TaskManagementController.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Model
- LARAVEL_README.md
- AppServiceProvider.php
- StoreDepartmentRequest
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
- DocumentAccessLevel.php
- User
- Task
- UserManagementController
- Priority.php
- Organization
- setup
- documents/create.blade.php
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- App\Models\DocumentFolder
- DocumentFolder
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- App\Models\Document
- Role
- FileStorageException
- App\Models\Role
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- App\Models\Task
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- Document
- psr-4
- NotificationSetting
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- DocumentFolderController
- Permission.php
- Illuminate\View\View
- ProjectManagementController
- config
- StoreProjectRequest
- LoginRequest
- buildEmojiPicker
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- FileStorageService
- Illuminate\Database\Seeder
- PermissionManagementController.php
- keywords
- UpdateDepartmentRequest
- FileCategory.php
- lightbox.js
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- StoreTaskRequest
- static
- link-preview-extension.js
- UpdateTaskRequest
- AuditEventNotifier
- link-preview-thumbnail.js
- Closure
- self
- OrgMember

## God Nodes (most connected - your core abstractions)
1. `User` - 270 edges
2. `Organization` - 150 edges
3. `OrgMember` - 133 edges
4. `Task` - 107 edges
5. `Role` - 91 edges
6. `Project` - 73 edges
7. `Department` - 64 edges
8. `Document` - 45 edges
9. `AuditLog` - 45 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForFolderTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/OrgMember.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `makeStaffWithDeptAccessForFolderTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Tasks/TaskFolderAttachTest.php → app/Models/Permission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `assertTaskDocumentAttachedAuditEntry()` --calls--> `AuditLog`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentLinkerAuditTest.php → app/Models/AuditLog.php

## Import Cycles
- None detected.

## Communities (200 total, 40 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (16): CommentReactionController, DocumentController, Document, Organization, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController (+8 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "TaskManagementController.php"
Cohesion: 0.18
Nodes (7): Organization, Task, TaskManagementController, App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, App\Http\Requests\Tasks\StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest, Project

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.12
Nodes (10): FolderAlreadyLinkedException, AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, TaskDocumentLinker, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage (+2 more)

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

### Community 11 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.23
Nodes (5): App\Models\Concerns\BelongsToOrganization, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

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
Cohesion: 0.11
Nodes (19): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+11 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (17): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+9 more)

### Community 88 - "Task"
Cohesion: 0.07
Nodes (14): User, Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload() (+6 more)

### Community 91 - "Priority.php"
Cohesion: 0.06
Nodes (9): AbandonStaleImportBatches, CleanupStalePendingMedia, allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self (+1 more)

### Community 92 - "Organization"
Cohesion: 0.07
Nodes (35): isAssignableStaffForProject(), AccessPermission, Department, Organization, Project, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeStaffOnCalendar() (+27 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 107 - "App\Models\DocumentFolder"
Cohesion: 0.17
Nodes (9): App\Models\DocumentFolder, makeWarningTestFolder(), DocumentFolder, makeAttachTestFolder(), DocumentFolder, makeDisplayTestFolder(), DocumentFolder, makePickerFolder() (+1 more)

### Community 108 - "DocumentFolder"
Cohesion: 0.13
Nodes (3): BootstrapEnvironment, DocumentFolder, DocumentFolderPolicy

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.12
Nodes (15): AccessControlController, CalendarController, App\Http\Controllers\Concerns\BuildsAssigneeOptions, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection (+7 more)

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.10
Nodes (5): CommentController, Comment, CommentPolicy, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "App\Models\Document"
Cohesion: 0.16
Nodes (4): App\Models\Document, DocumentDependencyService, Illuminate\Database\QueryException, makeLinkerFolderTestFolder()

### Community 115 - "Role"
Cohesion: 0.09
Nodes (21): Permission, Role, RolePolicy, up(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest() (+13 more)

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.27
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "App\Models\Task"
Cohesion: 0.10
Nodes (4): App\Models\Project, App\Models\Task, Task, makeWarningTestTask()

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.08
Nodes (6): App\Models\Organization, App\Models\User, makeStaffWithDeptAccessForFolderTest(), User, makeDeptStaffForDisplayTest(), User

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (17): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio(), UploadedFile (+9 more)

### Community 145 - "Document"
Cohesion: 0.11
Nodes (14): Document, DocumentPolicy, makeAttachableDocumentSet(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), makeClientWithProjectAccessForDownload(), uploadDocumentForDownload(), makeClientForDocumentList() (+6 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "NotificationSetting"
Cohesion: 0.15
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 149 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "DocumentFolderController"
Cohesion: 0.26
Nodes (5): DocumentFolderController, DocumentFolder, DocumentFolder, TaskFolderController, Controller

### Community 158 - "Permission.php"
Cohesion: 0.23
Nodes (5): Role, createOwnerForGrant(), grantManageDocuments(), makeStaffForFolderTest(), User

### Community 159 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController (+5 more)

### Community 164 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 165 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.19
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 197 - "AuditEventNotifier"
Cohesion: 0.17
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 200 - "Closure"
Cohesion: 0.27
Nodes (4): DocumentAlreadyAttachedException, self, Closure, RuntimeException

### Community 204 - "OrgMember"
Cohesion: 0.15
Nodes (5): OrgMember, CompanyRoleSyncer, Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `App\Models\Task`, `App\Models\User`, `Document`, `NotificationSetting`, `Subtask`, `Illuminate\View\View`, `ProjectManagementController`, `StoreProjectRequest`, `LoginRequest`, `ImportTemplateBuilder`, `AuditEventNotifier`, `OrgMember`, `DocumentAccessLevel.php`, `Task`, `UserManagementController`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `App\Models\Document`, `Role`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `App\Models\Task`, `Illuminate\Database\Eloquent\Model`, `App\Models\User`, `Document`, `Illuminate\Validation\Validator`, `Illuminate\View\View`, `ProjectManagementController`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `OrgMember`, `User`, `UserManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `App\Models\Document`, `Role`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\Request`, `ImportValidator`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `App\Models\Task`, `Illuminate\Database\Eloquent\Model`, `App\Models\User`, `Document`, `Subtask`, `FileCategory.php`, `AuditEventNotifier`, `Closure`, `OrgMember`, `DocumentAccessLevel.php`, `User`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `App\Models\Document`, `Role`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 20 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `OrgMember` (e.g. with `makeStaffForFolderTest()` and `makeStaffWithDeptAccessForFolderTest()`) actually correct?**
  _`OrgMember` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 9 INFERRED edges - model-reasoned connections that need verification._