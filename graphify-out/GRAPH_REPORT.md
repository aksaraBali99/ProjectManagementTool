# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 418 files · ~220,141 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1761 nodes · 4791 edges · 195 communities (165 shown, 30 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6e818f52`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- ImportValidator
- .boardOrganizationIds
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- FileCategory.php
- LARAVEL_README.md
- AppServiceProvider.php
- Comment
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
- NotificationSetting
- BootstrapEnvironment
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- RichText
- OrgMember
- Role
- HasAdminConfigurableColors.php
- App\Models\Department
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- App\Models\Role
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- Document
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- DocumentFolderController
- CalendarController.php
- Illuminate\View\View
- FileStorageException
- config
- ProjectManagementController.php
- LoginRequest
- buildEmojiPicker
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Database\Seeder
- DepartmentManagementController.php
- keywords
- TaskFolderController
- link-preview-thumbnail.js
- lightbox.js
- CommentMentionHighlightTest.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- static
- link-preview-extension.js
- StoreDepartmentRequest
- TaskDocumentLinker.php
- App\Models\Task

## God Nodes (most connected - your core abstractions)
1. `User` - 279 edges
2. `Organization` - 158 edges
3. `OrgMember` - 138 edges
4. `Task` - 115 edges
5. `Role` - 90 edges
6. `Project` - 75 edges
7. `Department` - 68 edges
8. `Document` - 51 edges
9. `AuditLog` - 45 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForEditTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/OrgMember.php
- `makeClientForEditTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/OrgMember.php
- `grantManageDocumentsForEditTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/Permission.php
- `makeDeptStaffForDisplayTest()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskFolderDisplayTest.php → app/Models/AccessPermission.php
- `makeDeptStaffForDisplayTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Tasks/TaskFolderDisplayTest.php → app/Models/OrgMember.php

## Import Cycles
- None detected.

## Communities (195 total, 30 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (12): DocumentController, Document, Organization, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "TaskManagementController"
Cohesion: 0.27
Nodes (4): Organization, Task, TaskManagementController, Project

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "FileCategory.php"
Cohesion: 0.13
Nodes (8): PastedMedia, PastedMediaNamer, fileFor(), FileCategory, UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Comment"
Cohesion: 0.14
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

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
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "DocumentAccessLevel.php"
Cohesion: 0.22
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

### Community 87 - "User"
Cohesion: 0.06
Nodes (16): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+8 more)

### Community 88 - "Task"
Cohesion: 0.11
Nodes (10): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 91 - "Priority.php"
Cohesion: 0.11
Nodes (6): App\Http\Controllers\Concerns\BuildsAssigneeOptions, App\Http\Controllers\Concerns\ResolvesCurrentOrganization, KanbanController, App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, App\Http\Requests\Tasks\StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest

### Community 92 - "Organization"
Cohesion: 0.07
Nodes (38): isAssignableStaffForProject(), AccessPermission, Department, Organization, Project, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember() (+30 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.08
Nodes (6): DocumentFolder, Illuminate\Database\Eloquent\Relations\HasMany, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makePickerFolder()

### Community 107 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.14
Nodes (11): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+3 more)

### Community 111 - "ImportBatch"
Cohesion: 0.10
Nodes (11): AbandonStaleImportBatches, CleanupStalePendingMedia, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+3 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "OrgMember"
Cohesion: 0.13
Nodes (8): OrgMember, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeStaffForNewMenuTest(), findKanbanCard(), DOMElement, joinOrg()

### Community 115 - "Role"
Cohesion: 0.11
Nodes (17): PermissionManagementController, Permission, Role, RolePolicy, up(), grantManageDocumentsForDeleteTest(), createOwnerForGrant(), grantManageDocuments() (+9 more)

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.31
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (7): AccessControlController, GoogleAuthController, NotificationSettingsController, TaskColorController, TaskPriorityColor, TaskStatusColor, Illuminate\Http\RedirectResponse

### Community 134 - "App\Models\Role"
Cohesion: 0.19
Nodes (3): App\Models\Document, App\Models\Role, makeStaffForUploadTest()

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.07
Nodes (14): App\Models\Organization, App\Models\User, DocumentFolderPolicy, Role, grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest() (+6 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (14): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, Symfony\Component\HttpFoundation\StreamedResponse, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile (+6 more)

### Community 145 - "Document"
Cohesion: 0.10
Nodes (8): Document, DocumentPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\Builder, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeAttachTestDocument(), makePickerDocument()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.14
Nodes (6): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.18
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.09
Nodes (7): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "DocumentFolderController"
Cohesion: 0.39
Nodes (3): DocumentFolderController, DocumentFolder, Controller

### Community 158 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 159 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (13): AnalyticsController, AuditTrailController, AuthenticatedSessionController, CommentReactionController, Controller, DepartmentManagementController, ImportController, LinkPreviewController (+5 more)

### Community 160 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 164 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 165 - "ProjectManagementController.php"
Cohesion: 0.09
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.23
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
Cohesion: 0.22
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 192 - "StoreDepartmentRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 200 - "TaskDocumentLinker.php"
Cohesion: 0.17
Nodes (8): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException, makeLinkerTestDocument()

### Community 204 - "App\Models\Task"
Cohesion: 0.14
Nodes (5): App\Models\DocumentFolder, App\Models\Project, App\Models\Task, Illuminate\Database\Eloquent\Model, makeStaffForFolderTest()

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `App\Models\Role`, `App\Models\User`, `Document`, `AuditLog`, `Subtask`, `Illuminate\Validation\Validator`, `Illuminate\View\View`, `ProjectManagementController.php`, `LoginRequest`, `ImportTemplateBuilder`, `static`, `TaskDocumentLinker.php`, `App\Models\Task`, `DocumentAccessLevel.php`, `Task`, `UserManagementController`, `Priority.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `NotificationSetting`, `BootstrapEnvironment`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `OrgMember`, `Role`, `App\Models\Department`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `App\Models\Role`, `App\Models\User`, `Document`, `Illuminate\Validation\Validator`, `CalendarController.php`, `Illuminate\View\View`, `ProjectManagementController.php`, `DepartmentManagementController.php`, `ImportTemplateBuilder`, `StoreDepartmentRequest`, `TaskDocumentLinker.php`, `App\Models\Task`, `User`, `UserManagementController`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `OrgMember`, `Role`, `App\Models\Department`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\Request`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `App\Models\Role`, `FileCategory.php`, `App\Models\User`, `Comment`, `Document`, `AuditLog`, `Subtask`, `CalendarController.php`, `Illuminate\View\View`, `static`, `TaskDocumentLinker.php`, `App\Models\Task`, `DocumentAccessLevel.php`, `User`, `Priority.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `OrgMember`, `Role`, `App\Models\Department`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.042) - this node is a cross-community bridge._
- **Are the 20 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `OrgMember` (e.g. with `makeClientForEditTest()` and `makeStaffForEditTest()`) actually correct?**
  _`OrgMember` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 11 INFERRED edges - model-reasoned connections that need verification._