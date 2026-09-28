# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 397 files · ~191,941 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1673 nodes · 4433 edges · 194 communities (159 shown, 35 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 285 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `68222bb3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Role
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Model
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- ValidClientUser.php
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
- DocumentFolder
- User
- TaskManagementController
- AuditLog
- Role.php
- Organization
- setup
- documents/create.blade.php
- app.js
- config
- _form.blade.php
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Comment
- Illuminate\Http\Request
- FileCategory.php
- DocumentPolicy
- Document
- LoginRequest
- config
- EnsureBelongsToOrganization.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- createRichTextEditor
- require
- OrganizationManagementController.php
- TagsImportBatch.php
- psr-4
- StoreTaskRequest
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- static
- Closure
- UpdateUserRequest
- HasAdminConfigurableColors.php
- CalendarController.php
- FileStorageService
- buildEmojiPicker
- StoreProjectRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateTaskPriorityColorsRequest
- Illuminate\Database\Seeder
- UpdateTaskStatusColorsRequest
- UpdateTaskRequest
- UpdateProjectRequest
- post-create-project-cmd
- lightbox.js
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- code-highlight.js
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- autoload-dev
- OpenGraphMetadataParser
- link-preview-extension.js
- OrgMember

## God Nodes (most connected - your core abstractions)
1. `User` - 262 edges
2. `Organization` - 144 edges
3. `OrgMember` - 122 edges
4. `Task` - 110 edges
5. `Role` - 84 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `Document` - 50 edges
9. `ImportValidator` - 42 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `makeReactionClient()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentReactionTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (194 total, 35 thin omitted)

### Community 0 - "Task"
Cohesion: 0.10
Nodes (12): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Role"
Cohesion: 0.11
Nodes (26): AnalyticsController, AccessPermission, Department, Role, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+18 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (5): CommentReactionController, CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.14
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

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

### Community 11 - "ValidClientUser.php"
Cohesion: 0.22
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DepartmentManagementController.php"
Cohesion: 0.13
Nodes (3): DepartmentManagementController, StoreDepartmentRequest, Illuminate\Contracts\Validation\Validator

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

### Community 86 - "DocumentFolder"
Cohesion: 0.12
Nodes (5): DocumentController, DocumentFolder, DocumentFolderPolicy, up(), DocumentAccessLevel

### Community 87 - "User"
Cohesion: 0.05
Nodes (15): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy (+7 more)

### Community 90 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 92 - "Organization"
Cohesion: 0.06
Nodes (25): Organization, Project, Illuminate\Database\Eloquent\Relations\HasMany, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeLinkOnlyDocumentForDeleteTest(), makeClientWithProjectAccessForDownload() (+17 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.13
Nodes (13): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), checkFileChipStatuses() (+5 more)

### Community 105 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 108 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (10): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, RoleManagementController (+2 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (9): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells() (+1 more)

### Community 111 - "ImportBatch"
Cohesion: 0.07
Nodes (13): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, ImportController, ImportBatch, CompanyRoleSyncer, EmployeeIdGenerator, ImportCommitResolution (+5 more)

### Community 112 - "LinkPreview"
Cohesion: 0.13
Nodes (4): LinkPreview, LinkPreviewResult, LinkPreviewService, UrlSsrfGuard

### Community 113 - "Comment"
Cohesion: 0.13
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (8): DocumentFolderController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 115 - "FileCategory.php"
Cohesion: 0.11
Nodes (18): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio() (+10 more)

### Community 118 - "Document"
Cohesion: 0.14
Nodes (5): TaskDocumentController, Document, DocumentDependencyService, DocumentUploadService, Illuminate\Http\Response

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "EnsureBelongsToOrganization.php"
Cohesion: 0.27
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (4): NotificationSettingsController, TaskColorController, UserManagementController, Illuminate\Http\RedirectResponse

### Community 134 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.26
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.12
Nodes (5): SubtaskController, isAssignableStaffForProject(), Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): UpdateDepartmentRequest, UploadImportRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 158 - "Closure"
Cohesion: 0.29
Nodes (3): PastedMediaNamer, Closure, RuntimeException

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 181 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.10
Nodes (7): PermissionManagementController, Permission, up(), PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest()

### Community 185 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 195 - "OrgMember"
Cohesion: 0.13
Nodes (10): OrgMember, findCommentCardBody(), DOMElement, makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForUploadTest(), findKanbanCard() (+2 more)

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `ValidClientUser.php`, `User.php`, `Subtask`, `static`, `UpdateUserRequest`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `DocumentFolder`, `TaskManagementController`, `AuditLog`, `Role.php`, `Organization`, `Project.php`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `DocumentPolicy`, `Document`, `LoginRequest`?**
  _High betweenness centrality (0.145) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Role`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `User.php`, `DepartmentManagementController.php`, `OrganizationManagementController.php`, `Illuminate\Validation\Validator`, `CalendarController.php`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `DocumentFolder`, `User`, `TaskManagementController`, `Role.php`, `Project.php`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Document`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `TagsImportBatch.php`, `Subtask`, `static`, `Closure`, `CalendarController.php`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `DocumentFolder`, `User`, `TaskManagementController`, `Role.php`, `Organization`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Http\Request`, `FileCategory.php`, `Document`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 50 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 50 INFERRED edges - model-reasoned connections that need verification._