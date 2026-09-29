# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 408 files · ~207,810 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1717 nodes · 4604 edges · 202 communities (157 shown, 45 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0002b43f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- App\Models\Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- FileCategory.php
- LARAVEL_README.md
- AppServiceProvider.php
- ValidatesTaskAssignment.php
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
- Illuminate\Database\Eloquent\Relations\HasMany
- User
- TaskManagementController
- UserManagementController.php
- BootstrapEnvironment
- Organization
- setup
- documents/create.blade.php
- app.js
- TaskDocumentController
- _form.blade.php
- AuditEventNotifier
- DocumentFolder
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- RichText
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Document
- FileStorageException
- Illuminate\Http\JsonResponse
- config
- Illuminate\Http\Request
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- StoreDepartmentRequest
- createRichTextEditor
- require
- OrgMember
- Illuminate\Http\UploadedFile
- TaskObserver
- psr-4
- NotificationSetting
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- DepartmentManagementController.php
- Illuminate\View\View
- TaskStatus.php
- config
- StoreProjectRequest
- Controller
- buildEmojiPicker
- FileStorageService
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- ProjectManagementController
- Illuminate\Database\Seeder
- Comment
- keywords
- CommentPolicy
- TaskManagementController.php
- lightbox.js
- Illuminate\Database\Eloquent\Model
- ProjectPolicy
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- SubtaskPolicy
- static
- link-preview-extension.js
- link-preview-thumbnail.js
- UserPolicy
- .resolvePending
- .storePending
- .storePending
- FileStorageServiceTest.php
- DocumentAccessLevel.php
- 2026_08_17_001200_create_documents_table.php
- UploadedFile

## God Nodes (most connected - your core abstractions)
1. `User` - 272 edges
2. `Organization` - 150 edges
3. `OrgMember` - 127 edges
4. `Task` - 116 edges
5. `Role` - 89 edges
6. `Project` - 77 edges
7. `Department` - 64 edges
8. `Document` - 58 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocumentsForEditTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/Permission.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php

## Import Cycles
- None detected.

## Communities (202 total, 45 thin omitted)

### Community 0 - "Task"
Cohesion: 0.10
Nodes (12): RichTextDocumentController, Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "App\Models\Task"
Cohesion: 0.11
Nodes (6): App\Models\Project, App\Models\Task, Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement, User

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReactionController, CommentReaction, organization(), PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 14 - "ValidatesTaskAssignment.php"
Cohesion: 0.12
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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

### Community 87 - "User"
Cohesion: 0.08
Nodes (10): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.08
Nodes (5): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, ImportRow, Illuminate\Console\Command

### Community 92 - "Organization"
Cohesion: 0.06
Nodes (49): AccessPermission, Department, Organization, Project, Role, UserSeeder, makeTaskForAnalytics(), makeStaffOnCalendar() (+41 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "TaskDocumentController"
Cohesion: 0.33
Nodes (3): Document, TaskDocumentController, Controller

### Community 108 - "DocumentFolder"
Cohesion: 0.21
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.11
Nodes (14): CalendarController, staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Carbon, Illuminate\Support\Carbon (+6 more)

### Community 111 - "ImportBatch"
Cohesion: 0.13
Nodes (9): ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver (+1 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 115 - "Document"
Cohesion: 0.11
Nodes (10): Document, DocumentPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\Builder, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentForList() (+2 more)

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 119 - "Illuminate\Http\JsonResponse"
Cohesion: 0.19
Nodes (5): DocumentController, RichTextImageController, SubtaskController, DocumentAccessLevel, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Http\Request"
Cohesion: 0.23
Nodes (6): AuditTrailController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (7): AccessControlController, NotificationSettingsController, OrganizationManagementController, TaskColorController, TaskPriorityColor, Illuminate\Http\RedirectResponse, Illuminate\Http\Response

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "OrgMember"
Cohesion: 0.09
Nodes (8): App\Models\Organization, OrgMember, App\Models\Role, App\Models\User, makeStaffForDocumentCreate(), makeClientForUploadTest(), makeStaffForUploadTest(), User

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (15): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile (+7 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "NotificationSetting"
Cohesion: 0.14
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 149 - "Subtask"
Cohesion: 0.24
Nodes (5): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateTaskPriorityColorsRequest, UpdateTaskStatusColorsRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.18
Nodes (5): NotificationController, PermissionManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 160 - "TaskStatus.php"
Cohesion: 0.19
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 165 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 166 - "Controller"
Cohesion: 0.14
Nodes (5): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, LoginRequest

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 181 - "TaskManagementController.php"
Cohesion: 0.24
Nodes (4): DocumentAlreadyAttachedException, self, TaskDocumentLinker, RuntimeException

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.10
Nodes (18): App\Models\Document, Permission, TaskStatusColor, up(), Illuminate\Database\Eloquent\Model, Role, grantManageDocumentsForDeleteTest(), makeClientForDeleteTest() (+10 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.22
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 198 - "FileStorageServiceTest.php"
Cohesion: 0.50
Nodes (3): fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `App\Models\Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `FileCategory.php`, `OrgMember`, `ValidatesTaskAssignment.php`, `NotificationSetting`, `Subtask`, `UserManagementController`, `StoreProjectRequest`, `Controller`, `ProjectManagementController`, `Comment`, `ImportTemplateBuilder`, `CommentPolicy`, `Illuminate\Database\Eloquent\Model`, `ProjectPolicy`, `SubtaskPolicy`, `static`, `UserPolicy`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `TaskManagementController`, `UserManagementController.php`, `BootstrapEnvironment`, `Organization`, `AuditEventNotifier`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.146) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `App\Models\Task`, `Illuminate\Http\RedirectResponse`, `FileCategory.php`, `OrgMember`, `ValidatesTaskAssignment.php`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController`, `DepartmentManagementController.php`, `Controller`, `ProjectManagementController`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Relations\HasMany`, `User`, `TaskManagementController`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `App\Models\Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `FileCategory.php`, `OrgMember`, `ValidatesTaskAssignment.php`, `TaskObserver`, `Subtask`, `Controller`, `Comment`, `TaskManagementController.php`, `Illuminate\Database\Eloquent\Model`, `SubtaskPolicy`, `static`, `.resolvePending`, `.storePending`, `.storePending`, `DocumentAccessLevel.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `TaskManagementController`, `Organization`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `OrgMember` (e.g. with `makeClientForUploadTest()` and `makeStaffForUploadTest()`) actually correct?**
  _`OrgMember` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._