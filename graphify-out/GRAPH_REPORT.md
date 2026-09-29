# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 409 files · ~209,817 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1733 nodes · 4622 edges · 200 communities (159 shown, 41 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 290 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `bd2767c1`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- App\Models\Task
- Illuminate\Database\Eloquent\Model
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- DocumentController.php
- LARAVEL_README.md
- AppServiceProvider.php
- App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment
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
- Project
- setup
- documents/create.blade.php
- app.js
- Illuminate\Database\Eloquent\Relations\HasMany
- _form.blade.php
- OrgMember
- Illuminate\Http\JsonResponse
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- RichText
- App\Models\Document
- Document
- FileStorageException
- App\Models\Project
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Priority.php
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
- CommentController
- StoreDepartmentRequest
- Illuminate\View\View
- HasAdminConfigurableColors.php
- config
- UpdateProjectRequest
- LoginRequest
- buildEmojiPicker
- FileCategory.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- CommentPolicy
- Illuminate\Database\Seeder
- Comment
- keywords
- NotificationSetting
- FileStorageService
- lightbox.js
- Role
- CalendarController.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- StoreProjectRequest
- static
- link-preview-extension.js
- link-preview-thumbnail.js
- UpdateRoleRequest
- LinkPreviewService.php
- UpdateTaskPriorityColorsRequest
- Illuminate\Http\Request
- DocumentAccessLevel.php
- UploadedFile

## God Nodes (most connected - your core abstractions)
1. `User` - 265 edges
2. `Organization` - 140 edges
3. `OrgMember` - 125 edges
4. `Task` - 100 edges
5. `Role` - 84 edges
6. `Project` - 71 edges
7. `Department` - 64 edges
8. `AuditLog` - 43 edges
9. `ImportValidator` - 42 edges
10. `Document` - 40 edges

## Surprising Connections (you probably didn't know these)
- `makeClientForDeleteTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentDeleteTest.php → app/Models/OrgMember.php
- `makeClientForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `makeStaffForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (200 total, 41 thin omitted)

### Community 0 - "Task"
Cohesion: 0.09
Nodes (13): RichTextDocumentController, Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate() (+5 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (6): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "DocumentController.php"
Cohesion: 0.23
Nodes (4): DocumentAlreadyAttachedException, self, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment"
Cohesion: 0.16
Nodes (5): App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, App\Http\Requests\Tasks\StoreTaskRequest, StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest, UpdateTaskRequest

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

### Community 86 - "Organization"
Cohesion: 0.10
Nodes (14): Organization, makeStaffForDocumentCreate(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeStaffForNewMenuTest(), makeClientForDocuments(), makeDocument() (+6 more)

### Community 87 - "User"
Cohesion: 0.07
Nodes (12): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+4 more)

### Community 88 - "TaskManagementController"
Cohesion: 0.27
Nodes (4): Organization, TaskManagementController, Project, Task

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 92 - "Project"
Cohesion: 0.08
Nodes (29): isAssignableStaffForProject(), AccessPermission, Department, Project, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+21 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 107 - "OrgMember"
Cohesion: 0.17
Nodes (5): OrgMember, App\Models\Role, CompanyRoleSyncer, findCommentCardBody(), DOMElement

### Community 108 - "Illuminate\Http\JsonResponse"
Cohesion: 0.09
Nodes (13): DocumentController, Organization, DocumentFolderController, RichTextAudioController, SubtaskController, Document, TaskDocumentController, DocumentFolder (+5 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (9): staffOptionsByProject(), resolveCurrentOrganization(), ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet(), assertDocumentViewParity() (+1 more)

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "App\Models\Document"
Cohesion: 0.11
Nodes (14): App\Models\Document, TaskDocumentLinker, Illuminate\Database\QueryException, Role, makeLinkerTestDocument(), Document, grantAttachPermission(), makeAttachTestDocument() (+6 more)

### Community 115 - "Document"
Cohesion: 0.13
Nodes (6): Document, DocumentPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\Builder, makeClientWithProjectAccessForDownload(), uploadDocumentForDownload()

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.27
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (5): GoogleAuthController, NotificationSettingsController, TaskColorController, UserManagementController, Illuminate\Http\RedirectResponse

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.07
Nodes (15): App\Models\Organization, App\Models\User, grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), Document, Role, User (+7 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.14
Nodes (15): Illuminate\Http\UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.14
Nodes (5): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 149 - "Subtask"
Cohesion: 0.20
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (14): AccessControlController, AnalyticsController, AuthenticatedSessionController, App\Http\Controllers\Concerns\BuildsAssigneeOptions, App\Http\Controllers\Concerns\ResolvesCurrentOrganization, Controller, DepartmentManagementController, ImportController (+6 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

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
Cohesion: 0.18
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 180 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Role"
Cohesion: 0.10
Nodes (19): PermissionManagementController, Permission, Role, up(), grantManageDocumentsForEditTest(), makeClientForEditTest(), makeDocumentForEditTest(), makeStaffForEditTest() (+11 more)

### Community 185 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 198 - "Illuminate\Http\Request"
Cohesion: 0.14
Nodes (7): AuditTrailController, CommentReactionController, DashboardController, Collection, RichTextImageController, RichTextVideoController, Illuminate\Http\Request

### Community 199 - "DocumentAccessLevel.php"
Cohesion: 0.24
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `App\Models\Task`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `AuditLog`, `Subtask`, `CommentController`, `Illuminate\View\View`, `UpdateProjectRequest`, `LoginRequest`, `CommentPolicy`, `ImportTemplateBuilder`, `NotificationSetting`, `Role`, `static`, `LinkPreviewService.php`, `Illuminate\Http\Request`, `DocumentAccessLevel.php`, `Organization`, `TaskManagementController`, `BootstrapEnvironment`, `Project`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `Illuminate\Http\JsonResponse`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Document`, `App\Models\Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `App\Models\Task`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `Illuminate\Validation\Validator`, `StoreDepartmentRequest`, `Illuminate\View\View`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Role`, `CalendarController.php`, `Illuminate\Http\Request`, `User`, `Project`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `Document`, `App\Models\Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `App\Models\Task`, `Illuminate\Database\Eloquent\Model`, `DocumentController.php`, `App\Models\User`, `TagsImportBatch.php`, `Subtask`, `CommentController`, `Illuminate\View\View`, `FileCategory.php`, `Role`, `CalendarController.php`, `static`, `LinkPreviewService.php`, `Illuminate\Http\Request`, `DocumentAccessLevel.php`, `Organization`, `Project`, `Illuminate\Database\Eloquent\Relations\HasMany`, `OrgMember`, `Illuminate\Http\JsonResponse`, `ImportBatch`, `LinkPreview`, `RichText`, `Document`, `App\Models\Project`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `OrgMember` (e.g. with `makeClientForDeleteTest()` and `makeClientForUploadTest()`) actually correct?**
  _`OrgMember` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._