# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 407 files · ~205,081 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1713 nodes · 4595 edges · 192 communities (155 shown, 37 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `8e32c058`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- App\Models\Role
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- NotificationSetting
- LARAVEL_README.md
- AppServiceProvider.php
- StoreTaskRequest
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
- Permission.php
- _form.blade.php
- OrgMember
- CommentPolicy
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Comment
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Document
- FileStorageException
- Task.php
- Closure
- config
- LoginRequest
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- StoreProjectRequest
- createRichTextEditor
- require
- App\Models\User
- FileCategory.php
- UpdateTaskPriorityColorsRequest
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- Illuminate\Database\Eloquent\Model
- Illuminate\View\View
- HasAdminConfigurableColors.php
- .url
- UpdateProjectRequest
- UploadedFile
- buildEmojiPicker
- link-preview-thumbnail.js
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- ProjectManagementController
- Illuminate\Database\Seeder
- TagsImportBatch.php
- keywords
- Illuminate\Http\Request
- CommentMentionHighlightTest.php
- lightbox.js
- Role
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- CompanyRoleRules
- link-preview-extension.js
- TaskManagementController.php

## God Nodes (most connected - your core abstractions)
1. `User` - 272 edges
2. `Organization` - 150 edges
3. `OrgMember` - 126 edges
4. `Task` - 120 edges
5. `Role` - 89 edges
6. `Project` - 77 edges
7. `Department` - 64 edges
8. `Document` - 61 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `makeClientForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `grantAttachPermission()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentAttachTest.php → app/Models/Permission.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (192 total, 37 thin omitted)

### Community 0 - "Task"
Cohesion: 0.12
Nodes (12): Task, TaskObserver, DocumentDependencyService, TaskDocumentLinker, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.14
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

### Community 11 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "StoreTaskRequest"
Cohesion: 0.10
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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
Cohesion: 0.06
Nodes (31): AnalyticsController, isAssignableStaffForProject(), Organization, Project, DepartmentSeeder, makeTaskForAnalytics(), makeReactionClient(), makeAttachableDocumentSet() (+23 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (15): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, TaskPolicy (+7 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.12
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "Permission.php"
Cohesion: 0.12
Nodes (6): App\Models\Document, Document, Role, grantAttachPermission(), makeAttachTestDocument(), User

### Community 107 - "OrgMember"
Cohesion: 0.11
Nodes (9): OrgMember, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), findKanbanCard(), DOMElement (+1 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (12): CalendarController, staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Carbon, Illuminate\Support\Carbon (+4 more)

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.12
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 115 - "Document"
Cohesion: 0.12
Nodes (8): DocumentController, DocumentFolderController, TaskDocumentController, Document, DocumentFolder, DocumentFolderPolicy, DocumentAccessLevel, Illuminate\Http\JsonResponse

### Community 116 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 119 - "Closure"
Cohesion: 0.20
Nodes (4): RichTextAudioController, RichTextImageController, RichTextVideoController, Closure

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "LoginRequest"
Cohesion: 0.09
Nodes (10): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): DepartmentManagementController, NotificationSettingsController, OrganizationManagementController, Illuminate\Http\RedirectResponse

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.09
Nodes (5): App\Models\Organization, App\Models\User, makeClientForUploadTest(), makeStaffForUploadTest(), User

### Community 143 - "FileCategory.php"
Cohesion: 0.08
Nodes (23): config(), prefix(), FileStorageService, PastedMediaNamer, StoredFile, Illuminate\Http\UploadedFile, Symfony\Component\HttpFoundation\StreamedResponse, fakeUploadDoc() (+15 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.13
Nodes (6): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (5): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, makeStaffForNewMenuTest()

### Community 159 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (11): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController, RoleManagementController (+3 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 165 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.20
Nodes (6): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 180 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (5): CommentReactionController, LinkPreviewController, RichTextDocumentController, SubtaskController, Illuminate\Http\Request

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Role"
Cohesion: 0.09
Nodes (32): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), makeStaffOnCalendar(), makeEligibleStaffMember() (+24 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 199 - "TaskManagementController.php"
Cohesion: 0.13
Nodes (6): DocumentAlreadyAttachedException, self, DocumentUploadService, up(), Illuminate\Http\Response, RuntimeException

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `NotificationSetting`, `App\Models\User`, `AuditLog`, `Subtask`, `UserManagementController`, `Illuminate\Database\Eloquent\Model`, `Illuminate\View\View`, `UpdateProjectRequest`, `ProjectManagementController`, `ImportTemplateBuilder`, `Role`, `TaskManagementController.php`, `Organization`, `TaskManagementController`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Permission.php`, `OrgMember`, `CommentPolicy`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `LoginRequest`?**
  _High betweenness centrality (0.125) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `App\Models\Role`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `StoreTaskRequest`, `UserManagementController`, `Illuminate\Database\Eloquent\Model`, `Illuminate\View\View`, `ProjectManagementController`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Role`, `CompanyRoleRules`, `TaskManagementController.php`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Permission.php`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditEventMailNotification.php`, `FileCategory.php`, `AuditLog`, `Subtask`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `Role`, `TaskManagementController.php`, `Organization`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Closure`, `LoginRequest`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `OrgMember` (e.g. with `makeClientForUploadTest()` and `makeStaffForUploadTest()`) actually correct?**
  _`OrgMember` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._