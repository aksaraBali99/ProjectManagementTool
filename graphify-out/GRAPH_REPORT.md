# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 408 files · ~206,538 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1709 nodes · 4604 edges · 195 communities (157 shown, 38 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4f95e9b1`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
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
- Organization
- User
- TaskManagementController
- UpdateUserRequest
- BootstrapEnvironment
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- OrgMember
- _form.blade.php
- AccessPermission.php
- Illuminate\Database\Eloquent\Builder
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreview
- RichText
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Document
- FileStorageException
- Task.php
- Illuminate\Http\Request
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- StoreDepartmentRequest
- createRichTextEditor
- require
- Illuminate\Http\UploadedFile
- ClipboardMediaPasteTest.php
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- UpdateDepartmentRequest
- Illuminate\View\View
- HasAdminConfigurableColors.php
- config
- ProjectStatus.php
- LoginRequest
- buildEmojiPicker
- UpdateRoleRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- Comment
- keywords
- UpdateTaskStatusColorsRequest
- UpdateProjectRequest
- lightbox.js
- Permission
- FileStorageServiceTest.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- AuditEventNotifier
- static
- link-preview-extension.js
- link-preview-thumbnail.js
- DocumentAccessLevel.php

## God Nodes (most connected - your core abstractions)
1. `User` - 278 edges
2. `Organization` - 155 edges
3. `OrgMember` - 129 edges
4. `Task` - 120 edges
5. `Role` - 93 edges
6. `Project` - 79 edges
7. `Department` - 64 edges
8. `Document` - 63 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForNewMenuTest()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentsNewMenuTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (195 total, 38 thin omitted)

### Community 0 - "Task"
Cohesion: 0.08
Nodes (14): DocumentAlreadyAttachedException, self, TaskDocumentController, Task, MentionedInCommentNotification, TaskObserver, TaskDocumentLinker, Illuminate\Database\Eloquent\SoftDeletes (+6 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "FileCategory.php"
Cohesion: 0.20
Nodes (4): PastedMedia, PastedMediaNamer, Closure, RuntimeException

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ValidatesTaskAssignment.php"
Cohesion: 0.14
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

### Community 86 - "Organization"
Cohesion: 0.07
Nodes (47): AnalyticsController, AccessPermission, Department, Organization, Project, Role, DepartmentSeeder, makeTaskForAnalytics() (+39 more)

### Community 87 - "User"
Cohesion: 0.04
Nodes (17): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, NotificationSettingPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+9 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "OrgMember"
Cohesion: 0.11
Nodes (8): OrgMember, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest(), joinOrg()

### Community 107 - "AccessPermission.php"
Cohesion: 0.17
Nodes (5): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement, findKanbanCard(), DOMElement

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 115 - "Document"
Cohesion: 0.11
Nodes (9): DocumentController, Document, DocumentFolder, DocumentFolderPolicy, DocumentDependencyService, DocumentAccessLevel, Illuminate\Http\Response, uploadDocumentForDownload() (+1 more)

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 118 - "Task.php"
Cohesion: 0.08
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 119 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (12): CommentReactionController, DashboardController, Collection, DocumentFolderController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (4): NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.14
Nodes (13): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakeDocumentFile() (+5 more)

### Community 145 - "ClipboardMediaPasteTest.php"
Cohesion: 0.40
Nodes (3): fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.17
Nodes (5): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (17): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, staffOptionsByProject(), resolveCurrentOrganization(), Controller, DepartmentManagementController (+9 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 165 - "ProjectStatus.php"
Cohesion: 0.11
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.23
Nodes (6): ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet(), assertDocumentViewParity()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.11
Nodes (16): PermissionManagementController, Permission, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), createOwnerForGrant(), grantManageDocuments() (+8 more)

### Community 185 - "FileStorageServiceTest.php"
Cohesion: 0.50
Nodes (3): fileFor(), FileCategory, UploadedFile

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `FileCategory.php`, `User.php`, `ValidatesTaskAssignment.php`, `NotificationEventType.php`, `Subtask`, `UserManagementController`, `Illuminate\View\View`, `ProjectStatus.php`, `LoginRequest`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Permission`, `AuditEventNotifier`, `static`, `DocumentAccessLevel.php`, `Organization`, `TaskManagementController`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `AccessPermission.php`, `Illuminate\Database\Eloquent\Builder`, `ImportBatch`, `LinkPreview`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Role.php`, `Illuminate\Http\RedirectResponse`, `User.php`, `ValidatesTaskAssignment.php`, `Illuminate\Validation\Validator`, `UserManagementController`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Permission`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `AccessPermission.php`, `CalendarController.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `FileCategory.php`, `User.php`, `Subtask`, `Illuminate\View\View`, `Permission`, `static`, `DocumentAccessLevel.php`, `Organization`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `AccessPermission.php`, `Illuminate\Database\Eloquent\Builder`, `CalendarController.php`, `ImportBatch`, `LinkPreview`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 51 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 51 INFERRED edges - model-reasoned connections that need verification._