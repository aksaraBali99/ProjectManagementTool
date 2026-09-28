# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 397 files · ~193,956 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1682 nodes · 4442 edges · 196 communities (156 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 287 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `babb14c1`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Organization
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Model
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- UpdateProjectRequest
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
- DocumentAccessLevel.php
- User
- TaskManagementController
- AuditLog
- OrgMember
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- config
- _form.blade.php
- App\Models\User
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- Project
- Illuminate\Http\UploadedFile
- StoreOrganizationRequest
- FileCategory.php
- BootstrapEnvironment
- config
- LoginRequest
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- DocumentController.php
- createRichTextEditor
- require
- App\Models\Organization
- FileStorageService
- TagsImportBatch.php
- psr-4
- AuditEventNotifier
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Document
- UserManagementController
- UpdateUserRequest
- HasAdminConfigurableColors.php
- NotificationSetting
- StoreProjectRequest
- buildEmojiPicker
- RichText
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- ProjectManagementController
- Illuminate\Database\Seeder
- UpdateTaskStatusColorsRequest
- keywords
- PermissionManagementController.php
- StoreTaskRequest
- lightbox.js
- Permission.php
- Illuminate\Http\Request
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- UpdateDepartmentRequest
- UpdateRoleRequest
- link-preview-extension.js
- CompanyRoleRules
- UpdateOrganizationRequest
- link-preview-thumbnail.js
- App\Models\Task

## God Nodes (most connected - your core abstractions)
1. `User` - 241 edges
2. `Organization` - 132 edges
3. `OrgMember` - 119 edges
4. `Task` - 107 edges
5. `Role` - 77 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 40 edges
10. `Controller` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeClientForDeleteTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentDeleteTest.php → app/Models/OrgMember.php
- `grantManageDocumentsForDeleteTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentDeleteTest.php → app/Models/Permission.php
- `makeStaffForEditTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/OrgMember.php
- `makeClientForEditTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/OrgMember.php
- `grantManageDocumentsForEditTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/Permission.php

## Import Cycles
- None detected.

## Communities (196 total, 40 thin omitted)

### Community 0 - "Task"
Cohesion: 0.12
Nodes (10): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Organization"
Cohesion: 0.08
Nodes (26): DepartmentManagementController, AccessPermission, Department, Organization, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+18 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

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
Cohesion: 0.27
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

### Community 87 - "User"
Cohesion: 0.04
Nodes (16): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, OrganizationPolicy, ProjectPolicy, SubtaskPolicy, TaskPolicy (+8 more)

### Community 90 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 107 - "App\Models\User"
Cohesion: 0.08
Nodes (19): App\Models\Document, App\Models\User, DocumentPolicy, DocumentDependencyService, grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), Document (+11 more)

### Community 108 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (9): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController, OrganizationManagementController, SettingsController (+1 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (12): AccessControlController, CalendarController, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, KanbanController (+4 more)

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.15
Nodes (3): CommentController, Comment, CommentPolicy

### Community 114 - "Project"
Cohesion: 0.08
Nodes (20): AnalyticsController, Project, Role, RolePolicy, makeReactionClient(), makeClientWithProjectAccessForDownload(), createOwnerForGrant(), grantManageDocuments() (+12 more)

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (15): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 118 - "FileCategory.php"
Cohesion: 0.22
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

### Community 119 - "BootstrapEnvironment"
Cohesion: 0.12
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "LoginRequest"
Cohesion: 0.10
Nodes (10): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (4): NotificationSettingsController, RoleManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "DocumentController.php"
Cohesion: 0.22
Nodes (5): FileStorageException, self, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse, Throwable

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditEventNotifier"
Cohesion: 0.16
Nodes (4): AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 149 - "Subtask"
Cohesion: 0.17
Nodes (4): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): UploadImportRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Document"
Cohesion: 0.15
Nodes (8): Document, uploadDocumentForDownload(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "RichText"
Cohesion: 0.14
Nodes (3): UpdateTaskRequest, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission.php"
Cohesion: 0.24
Nodes (3): Permission, up(), Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 185 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (17): CommentReactionController, DocumentController, Document, Organization, DocumentFolderController, LinkPreviewController, RichTextAudioController, RichTextDocumentController (+9 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 195 - "App\Models\Task"
Cohesion: 0.15
Nodes (4): App\Models\Task, Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `UpdateProjectRequest`, `AuditEventNotifier`, `Subtask`, `Document`, `UserManagementController`, `NotificationSetting`, `Priority.php`, `ProjectManagementController`, `ImportTemplateBuilder`, `App\Models\Task`, `DocumentAccessLevel.php`, `TaskManagementController`, `AuditLog`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\User`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Project`, `FileCategory.php`, `BootstrapEnvironment`, `LoginRequest`?**
  _High betweenness centrality (0.166) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `App\Models\Organization`, `DepartmentManagementController.php`, `Document`, `UserManagementController`, `ProjectManagementController`, `ImportTemplateBuilder`, `CompanyRoleRules`, `App\Models\Task`, `User`, `TaskManagementController`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Project`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Organization`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `TagsImportBatch.php`, `Subtask`, `Document`, `Priority.php`, `RichText`, `Illuminate\Http\Request`, `App\Models\Task`, `DocumentAccessLevel.php`, `User`, `TaskManagementController`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\User`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Project`, `FileCategory.php`, `LoginRequest`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `OrgMember` (e.g. with `makeClientForDeleteTest()` and `makeClientForEditTest()`) actually correct?**
  _`OrgMember` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._