# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 398 files · ~195,601 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1679 nodes · 4448 edges · 194 communities (156 shown, 38 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 286 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `98e4550c`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Organization
- Illuminate\Database\Eloquent\Model
- App\Models\Document
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- UpdateProjectRequest
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
- Document
- User
- TaskManagementController
- DocumentEditTest.php
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- config
- _form.blade.php
- DocumentController
- Illuminate\View\View
- CodeLanguageClassSanitizer
- .__invoke
- ImportBatch
- LinkPreviewService.php
- Comment
- Role
- Illuminate\Http\UploadedFile
- Controller
- DocumentController.php
- BootstrapEnvironment
- config
- Illuminate\Http\Request
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- createRichTextEditor
- require
- OrgMember
- FileCategory.php
- TagsImportBatch.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- .myTaskSection
- UserManagementController
- UpdateUserRequest
- HasAdminConfigurableColors.php
- StoreDepartmentRequest
- StoreProjectRequest
- buildEmojiPicker
- UpdateOrganizationRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- link-preview-thumbnail.js
- keywords
- PermissionManagementController
- lightbox.js
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- CompanyRoleRules
- link-preview-extension.js
- DocumentFolder
- App\Models\Role
- LoginRequest
- UpdateTaskPriorityColorsRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 258 edges
2. `Organization` - 139 edges
3. `OrgMember` - 121 edges
4. `Task` - 110 edges
5. `Role` - 83 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `ImportValidator` - 42 edges
9. `Document` - 41 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeStaffForNewMenuTest()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentsNewMenuTest.php → app/Models/Role.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (194 total, 38 thin omitted)

### Community 0 - "Task"
Cohesion: 0.11
Nodes (11): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Organization"
Cohesion: 0.07
Nodes (27): AccessPermission, Department, Organization, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+19 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (8): CommentReaction, organization(), ImportRow, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.11
Nodes (19): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+11 more)

### Community 86 - "Document"
Cohesion: 0.14
Nodes (7): Document, DocumentPolicy, DocumentDependencyService, DocumentUploadService, up(), DocumentAccessLevel, uploadDocumentForDownload()

### Community 87 - "User"
Cohesion: 0.06
Nodes (13): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Builder (+5 more)

### Community 90 - "DocumentEditTest.php"
Cohesion: 0.15
Nodes (8): Permission, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 107 - "DocumentController"
Cohesion: 0.29
Nodes (4): DocumentController, Organization, Controller, Document

### Community 108 - "Illuminate\View\View"
Cohesion: 0.17
Nodes (6): AccessControlController, App\Http\Controllers\Concerns\ResolvesCurrentOrganization, KanbanController, NotificationController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - ".__invoke"
Cohesion: 0.62
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.11
Nodes (9): AbandonStaleImportBatches, CleanupStalePendingMedia, ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+1 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.09
Nodes (7): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.07
Nodes (7): CommentController, UpdateTaskRequest, Comment, CommentObserver, CommentPolicy, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Role"
Cohesion: 0.07
Nodes (27): isAssignableStaffForProject(), Project, Role, makeReactionClient(), makeClientForDeleteTest(), makeClientWithProjectAccessForDownload(), createOwnerForGrant(), grantManageDocuments() (+19 more)

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (17): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio(), UploadedFile (+9 more)

### Community 116 - "Controller"
Cohesion: 0.25
Nodes (4): AnalyticsController, AuditTrailController, Controller, RoleManagementController

### Community 118 - "DocumentController.php"
Cohesion: 0.44
Nodes (4): Illuminate\Http\Response, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse, Throwable

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (6): CommentReactionController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (6): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, OrganizationManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "OrgMember"
Cohesion: 0.09
Nodes (9): App\Models\Organization, OrgMember, App\Models\User, findCommentCardBody(), DOMElement, makeStaffForDocumentCreate(), User, makeStaffForNewMenuTest() (+1 more)

### Community 143 - "FileCategory.php"
Cohesion: 0.16
Nodes (4): FileStorageService, PastedMediaNamer, StoredFile, Closure

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 149 - "Subtask"
Cohesion: 0.13
Nodes (4): SubtaskController, Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.18
Nodes (6): staffOptionsByProject(), resolveCurrentOrganization(), ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertDocumentViewParity()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 185 - "Illuminate\Http\JsonResponse"
Cohesion: 0.18
Nodes (6): RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "CompanyRoleRules"
Cohesion: 0.15
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 193 - "DocumentFolder"
Cohesion: 0.21
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 195 - "App\Models\Role"
Cohesion: 0.15
Nodes (4): App\Models\Role, CompanyRoleSyncer, Illuminate\Support\Facades\Notification, toggleStaffManageDocuments()

## Knowledge Gaps
- **133 isolated node(s):** `users._unsaved-changes-guard`, `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Organization`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `UpdateProjectRequest`, `OrgMember`, `AuditLog`, `Subtask`, `.myTaskSection`, `UserManagementController`, `Task.php`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CompanyRoleRules`, `DocumentFolder`, `App\Models\Role`, `LoginRequest`, `Document`, `DocumentEditTest.php`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Role`, `DocumentController.php`, `BootstrapEnvironment`?**
  _High betweenness centrality (0.161) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Organization`, `Illuminate\Database\Eloquent\Model`, `OrgMember`, `FileCategory.php`, `TagsImportBatch.php`, `Subtask`, `.myTaskSection`, `Task.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `CompanyRoleRules`, `App\Models\Role`, `Document`, `User`, `TaskManagementController`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentController`, `Illuminate\View\View`, `.__invoke`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Role`, `DocumentController.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `.myTaskSection`, `Task.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CompanyRoleRules`, `App\Models\Role`, `User`, `DocumentEditTest.php`, `Department.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `.__invoke`, `ImportBatch`, `Role`?**
  _High betweenness centrality (0.042) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `OrgMember` (e.g. with `makeStaffForDocumentCreate()` and `makeStaffForNewMenuTest()`) actually correct?**
  _`OrgMember` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._