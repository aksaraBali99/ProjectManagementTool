# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 406 files · ~204,222 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1718 nodes · 4581 edges · 192 communities (156 shown, 36 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 293 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f7bf5645`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- FileStorageService
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
- Organization
- User
- TaskManagementController
- UpdateUserRequest
- BootstrapEnvironment
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- App\Models\Role
- _form.blade.php
- Illuminate\Database\Eloquent\Model
- CommentPolicy
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreview
- Comment
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\Request
- FileStorageException
- TaskStatus.php
- Illuminate\Http\JsonResponse
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileCategory.php
- createRichTextEditor
- require
- OrgMember
- Illuminate\Http\UploadedFile
- FileStorageException.php
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- DocumentFolderPolicy
- Illuminate\View\View
- UserPolicy
- NotificationSetting
- Priority.php
- TaskColorController.php
- buildEmojiPicker
- link-preview-thumbnail.js
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- AuditEventNotifier
- keywords
- lightbox.js
- Role
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- static
- link-preview-extension.js
- DocumentFolder
- LoginRequest
- DocumentAccessLevel.php

## God Nodes (most connected - your core abstractions)
1. `User` - 271 edges
2. `Organization` - 149 edges
3. `OrgMember` - 125 edges
4. `Task` - 104 edges
5. `Role` - 89 edges
6. `Project` - 73 edges
7. `Department` - 64 edges
8. `Document` - 53 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `assertTaskDocumentAttachedAuditEntry()` --calls--> `AuditLog`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentLinkerAuditTest.php → app/Models/AuditLog.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeEligibleStaffMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php
- `makeIneligibleProjectMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php
- `makeProjectMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (192 total, 36 thin omitted)

### Community 0 - "Task"
Cohesion: 0.09
Nodes (13): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+5 more)

### Community 1 - "ImportValidator"
Cohesion: 0.09
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.12
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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

### Community 14 - "StoreDepartmentRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "Organization"
Cohesion: 0.06
Nodes (42): AccessPermission, Department, Organization, Project, Illuminate\Support\Facades\Notification, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember() (+34 more)

### Community 87 - "User"
Cohesion: 0.08
Nodes (12): isAssignableStaffForProject(), User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+4 more)

### Community 88 - "TaskManagementController"
Cohesion: 0.27
Nodes (4): Organization, Task, TaskManagementController, Project

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "App\Models\Role"
Cohesion: 0.12
Nodes (7): App\Models\Document, App\Models\Project, App\Models\Role, App\Models\Task, findCommentCardBody(), DOMElement, assertTaskDocumentAttachedAuditEntry()

### Community 107 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.10
Nodes (12): Illuminate\Database\Eloquent\Model, makeStaffForNewMenuTest(), grantAttachPermission(), makeAttachTestDocument(), Document, Role, User, grantAttachDocumentsPermission() (+4 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.10
Nodes (10): AbandonStaleImportBatches, CleanupStalePendingMedia, ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure (+2 more)

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.12
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (8): DocumentAlreadyAttachedException, self, DocumentController, Document, DocumentDependencyService, TaskDocumentLinker, DocumentAccessLevel, Illuminate\Http\Request

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 118 - "TaskStatus.php"
Cohesion: 0.22
Nodes (3): App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, App\Http\Requests\Tasks\StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest

### Community 119 - "Illuminate\Http\JsonResponse"
Cohesion: 0.13
Nodes (7): LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.31
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (7): AuthenticatedSessionController, GoogleAuthController, DepartmentManagementController, NotificationSettingsController, OrganizationManagementController, Illuminate\Http\RedirectResponse, Illuminate\Http\Response

### Community 134 - "FileCategory.php"
Cohesion: 0.12
Nodes (6): config(), prefix(), PastedMedia, PastedMediaNamer, Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "OrgMember"
Cohesion: 0.10
Nodes (4): App\Models\Organization, OrgMember, App\Models\User, makeStaffForDocumentCreate()

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.11
Nodes (17): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio(), UploadedFile (+9 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.15
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (9): AccessControlController, AnalyticsController, AuditTrailController, CommentReactionController, Controller, NotificationController, RoleManagementController, SettingsController (+1 more)

### Community 164 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 165 - "Priority.php"
Cohesion: 0.05
Nodes (12): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self, StoreProjectRequest, UpdateProjectRequest (+4 more)

### Community 166 - "TaskColorController.php"
Cohesion: 0.14
Nodes (5): TaskColorController, UpdateTaskPriorityColorsRequest, UpdateTaskStatusColorsRequest, TaskPriorityColor, TaskStatusColor

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.12
Nodes (14): App\Http\Controllers\Concerns\BuildsAssigneeOptions, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController (+6 more)

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "AuditEventNotifier"
Cohesion: 0.20
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Role"
Cohesion: 0.10
Nodes (23): PermissionManagementController, Permission, Role, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), makeLinkOnlyDocumentForDeleteTest() (+15 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 193 - "DocumentFolder"
Cohesion: 0.22
Nodes (5): DocumentFolderController, Document, TaskDocumentController, DocumentFolder, Controller

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **36 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `OrgMember`, `FileStorageException.php`, `NotificationEventType.php`, `Subtask`, `UserManagementController`, `DocumentFolderPolicy`, `Illuminate\View\View`, `UserPolicy`, `NotificationSetting`, `Priority.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `AuditEventNotifier`, `ImportTemplateBuilder`, `Role`, `static`, `LoginRequest`, `DocumentAccessLevel.php`, `Organization`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `Illuminate\Database\Eloquent\Model`, `CommentPolicy`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.127) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `StoreDepartmentRequest`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Role`, `User`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `Illuminate\Database\Eloquent\Model`, `CalendarController.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `FileCategory.php`, `OrgMember`, `FileStorageException.php`, `Subtask`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `AuditEventNotifier`, `Role`, `static`, `DocumentAccessLevel.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `App\Models\Role`, `Illuminate\Database\Eloquent\Model`, `CalendarController.php`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\Request`, `TaskStatus.php`, `Illuminate\Http\JsonResponse`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 20 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._