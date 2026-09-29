# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 409 files · ~209,287 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1728 nodes · 4622 edges · 205 communities (164 shown, 41 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 297 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7c0bfd92`
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
- Role
- setup
- documents/create.blade.php
- app.js
- TaskDocumentController
- _form.blade.php
- OrgMember
- DocumentFolder
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- RichText
- App\Models\Document
- Document
- FileStorageException
- DocumentController
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\Request
- TaskStatus.php
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- TagsImportBatch.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- App\Models\Project
- Illuminate\View\View
- HasAdminConfigurableColors.php
- config
- StoreProjectRequest
- LoginRequest
- buildEmojiPicker
- FileStorageService
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Database\Eloquent\Model
- Illuminate\Database\Seeder
- Comment
- keywords
- NotificationSetting
- TaskDocumentController.php
- lightbox.js
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- CalendarController.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- DocumentDeleteTest.php
- static
- link-preview-extension.js
- link-preview-thumbnail.js
- Illuminate\Http\JsonResponse
- UrlSsrfGuard
- .storePending
- .myTaskSection
- DocumentController.php
- UpdateTaskStatusColorsRequest
- UploadedFile
- .storePending
- FileStorageService.php
- .__invoke

## God Nodes (most connected - your core abstractions)
1. `User` - 265 edges
2. `Organization` - 145 edges
3. `OrgMember` - 125 edges
4. `Task` - 113 edges
5. `Role` - 84 edges
6. `Project` - 77 edges
7. `Department` - 64 edges
8. `Document` - 51 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `makeClientForDeleteTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentDeleteTest.php → app/Models/OrgMember.php
- `grantManageDocumentsForDeleteTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentDeleteTest.php → app/Models/Permission.php
- `grantAttachPermission()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentAttachTest.php → app/Models/Permission.php
- `makeClientForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `makeStaffForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php

## Import Cycles
- None detected.

## Communities (205 total, 41 thin omitted)

### Community 0 - "Task"
Cohesion: 0.10
Nodes (12): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReactionController, CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "FileCategory.php"
Cohesion: 0.24
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ValidatesTaskAssignment.php"
Cohesion: 0.09
Nodes (5): StoreDepartmentRequest, isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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
Nodes (20): Organization, Project, Illuminate\Database\Eloquent\Relations\HasMany, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForDocumentList(), makeDocumentForList() (+12 more)

### Community 87 - "User"
Cohesion: 0.06
Nodes (13): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy (+5 more)

### Community 92 - "Role"
Cohesion: 0.11
Nodes (26): AccessPermission, Department, Role, DepartmentSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember() (+18 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "TaskDocumentController"
Cohesion: 0.40
Nodes (3): Document, TaskDocumentController, Controller

### Community 107 - "OrgMember"
Cohesion: 0.13
Nodes (11): OrgMember, App\Models\Task, Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement, makeStaffForDocumentCreate(), makeClientForEditTest(), makeStaffForEditTest() (+3 more)

### Community 108 - "DocumentFolder"
Cohesion: 0.21
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (8): staffOptionsByProject(), resolveCurrentOrganization(), KanbanController, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), assertDocumentViewParity()

### Community 111 - "ImportBatch"
Cohesion: 0.10
Nodes (12): AbandonStaleImportBatches, CleanupStalePendingMedia, ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure (+4 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.14
Nodes (5): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, DOMXPath

### Community 114 - "App\Models\Document"
Cohesion: 0.10
Nodes (14): App\Models\Document, Role, makeLinkerTestDocument(), Document, grantAttachPermission(), makeAttachTestDocument(), Document, Role (+6 more)

### Community 115 - "Document"
Cohesion: 0.11
Nodes (8): Document, DocumentPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\Builder, makeAttachableDocumentSet(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentSetForParity()

### Community 116 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.20
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\Request"
Cohesion: 0.18
Nodes (5): AccessControlController, GoogleAuthController, NotificationSettingsController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.09
Nodes (5): App\Models\Organization, App\Models\User, makeClientForUploadTest(), makeStaffForUploadTest(), User

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.15
Nodes (13): DocumentUploadService, Illuminate\Http\UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile, fakeDocumentFile() (+5 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.15
Nodes (5): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 149 - "Subtask"
Cohesion: 0.18
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (9): AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController (+1 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "config"
Cohesion: 0.25
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

### Community 172 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.23
Nodes (4): PastedMedia, TaskPriorityColor, Illuminate\Database\Eloquent\Model, makeStaffForNewMenuTest()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "Comment"
Cohesion: 0.18
Nodes (3): CommentController, Comment, CommentObserver

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 180 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 181 - "TaskDocumentController.php"
Cohesion: 0.27
Nodes (5): DocumentAlreadyAttachedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.09
Nodes (11): PermissionManagementController, Permission, up(), PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany, grantManageDocumentsForEditTest(), grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest() (+3 more)

### Community 185 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "DocumentDeleteTest.php"
Cohesion: 0.28
Nodes (8): grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), Document, Role, User, fakeUploadDoc(), UploadedFile

### Community 189 - "static"
Cohesion: 0.32
Nodes (4): bootBelongsToOrganization(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 194 - "Illuminate\Http\JsonResponse"
Cohesion: 0.22
Nodes (5): LinkPreviewController, RichTextAudioController, RichTextDocumentController, SubtaskController, Illuminate\Http\JsonResponse

### Community 199 - "DocumentController.php"
Cohesion: 0.23
Nodes (3): up(), DocumentAccessLevel, Illuminate\Http\Response

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\Request`, `TaskStatus.php`, `App\Models\User`, `ValidatesTaskAssignment.php`, `Illuminate\Http\UploadedFile`, `AuditLog`, `Subtask`, `UserManagementController`, `Illuminate\View\View`, `StoreProjectRequest`, `LoginRequest`, `Illuminate\Database\Eloquent\Model`, `Comment`, `ImportTemplateBuilder`, `NotificationSetting`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`, `.myTaskSection`, `DocumentController.php`, `.__invoke`, `Organization`, `BootstrapEnvironment`, `Role`, `OrgMember`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `App\Models\Document`, `Document`, `Priority.php`?**
  _High betweenness centrality (0.164) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `App\Models\Role`, `Illuminate\Http\Request`, `TaskStatus.php`, `App\Models\User`, `ValidatesTaskAssignment.php`, `Illuminate\Validation\Validator`, `UserManagementController`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CalendarController.php`, `.myTaskSection`, `DocumentController.php`, `.__invoke`, `User`, `Role`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `App\Models\Document`, `Document`, `DocumentController`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\Request`, `FileCategory.php`, `App\Models\User`, `ValidatesTaskAssignment.php`, `Illuminate\Http\UploadedFile`, `TagsImportBatch.php`, `Subtask`, `Illuminate\Database\Eloquent\Model`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CalendarController.php`, `static`, `Illuminate\Http\JsonResponse`, `.storePending`, `.myTaskSection`, `DocumentController.php`, `.storePending`, `.__invoke`, `Organization`, `TaskManagementController`, `Role`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `Document`, `Priority.php`, `DocumentController`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `OrgMember` (e.g. with `makeClientForDeleteTest()` and `makeClientForUploadTest()`) actually correct?**
  _`OrgMember` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._