# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 400 files · ~198,330 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1692 nodes · 4480 edges · 207 communities (161 shown, 46 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 282 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `26f17797`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Priority.php
- LARAVEL_README.md
- AppServiceProvider.php
- RichText
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
- App\Models\Concerns\BelongsToOrganization
- App\Models\Role
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- BootstrapEnvironment
- _form.blade.php
- Role
- Illuminate\View\View
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreviewService.php
- Comment
- Organization
- FileCategory.php
- TaskManagementController.php
- Illuminate\Http\Request
- NotificationSettingPolicy
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- AuditLog
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- DocumentLinkedTasksPopoverTest.php
- App\Models\Department
- HasAdminConfigurableColors.php
- DepartmentManagementController.php
- App\Models\Task
- SubtaskPolicy
- buildEmojiPicker
- StoreProjectRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Builder
- keywords
- .storePending
- App\Http\Controllers\Concerns\ResolvesCurrentOrganization
- lightbox.js
- Permission
- TaskPolicy
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- Controller
- CompanyRoleRules
- link-preview-extension.js
- CommentController
- DocumentFolder
- LoginRequest
- link-preview-thumbnail.js
- CommentPolicy
- DocumentPolicy.php
- DocumentFolderPolicy
- OrganizationPolicy
- UserPolicy
- TaskViewableIdsParityTest.php
- TaskColorController.php
- RolePolicy
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 258 edges
2. `Organization` - 141 edges
3. `OrgMember` - 123 edges
4. `Task` - 112 edges
5. `Role` - 84 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `Document` - 51 edges
9. `ImportValidator` - 42 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `grantManageDocumentsForLinkedTasksTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentLinkedTasksPopoverTest.php → app/Models/Permission.php
- `grantViewDocumentsForLinkedTasksTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentLinkedTasksPopoverTest.php → app/Models/Permission.php
- `makeStaffForNewMenuTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentsNewMenuTest.php → app/Models/OrgMember.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (207 total, 46 thin omitted)

### Community 0 - "Task"
Cohesion: 0.13
Nodes (10): User, Task, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "OrgMember"
Cohesion: 0.12
Nodes (10): OrgMember, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForUploadTest(), findKanbanCard() (+2 more)

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

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "RichText"
Cohesion: 0.10
Nodes (5): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

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
Cohesion: 0.09
Nodes (21): ImportController, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase (+13 more)

### Community 86 - "Document"
Cohesion: 0.11
Nodes (10): DocumentController, Organization, Document, DocumentDependencyService, Attribute, Controller, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Http\Response (+2 more)

### Community 87 - "User"
Cohesion: 0.09
Nodes (9): User, AuditLogPolicy, DepartmentPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask() (+1 more)

### Community 90 - "App\Models\Concerns\BelongsToOrganization"
Cohesion: 0.18
Nodes (3): App\Models\Concerns\BelongsToOrganization, PastedMedia, makeStaffForFolderTest()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 107 - "Role"
Cohesion: 0.16
Nodes (16): AccessPermission, Department, Role, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeReactionEligibleStaff() (+8 more)

### Community 108 - "Illuminate\View\View"
Cohesion: 0.16
Nodes (6): AuthenticatedSessionController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Organization"
Cohesion: 0.08
Nodes (24): Organization, Project, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeLinkOnlyDocumentForDeleteTest(), makeClientWithProjectAccessForDownload(), makeDocumentForEditTest() (+16 more)

### Community 115 - "FileCategory.php"
Cohesion: 0.15
Nodes (7): config(), prefix(), PastedMediaNamer, RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 116 - "TaskManagementController.php"
Cohesion: 0.22
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

### Community 118 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (8): AuditTrailController, CommentReactionController, LinkPreviewController, RichTextDocumentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse, Illuminate\Http\Request

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.14
Nodes (9): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 134 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.09
Nodes (6): App\Models\Organization, App\Models\User, Document, makeDocumentForLinkedTasksTest(), makeStaffForNewMenuTest(), User

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 145 - "AuditLog"
Cohesion: 0.18
Nodes (3): AuditLog, AuditEventNotifier, NotificationEventType

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.22
Nodes (5): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.15
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, UpdateUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (8): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "DocumentLinkedTasksPopoverTest.php"
Cohesion: 0.32
Nodes (7): Role, Task, grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest(), grantViewDocumentsForLinkedTasksTest(), makeTaskForLinkedTasksTest(), User

### Community 159 - "App\Models\Department"
Cohesion: 0.17
Nodes (4): App\Models\Department, App\Models\Document, App\Models\Project, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "DepartmentManagementController.php"
Cohesion: 0.13
Nodes (3): DepartmentManagementController, StoreDepartmentRequest, Illuminate\Contracts\Validation\Validator

### Community 165 - "App\Models\Task"
Cohesion: 0.12
Nodes (3): App\Models\Task, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.29
Nodes (4): ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertDocumentViewParity()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 181 - "App\Http\Controllers\Concerns\ResolvesCurrentOrganization"
Cohesion: 0.17
Nodes (7): AccessControlController, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, KanbanController

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.17
Nodes (7): PermissionManagementController, Permission, up(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), createOwnerForGrant(), grantManageDocuments()

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "Controller"
Cohesion: 0.20
Nodes (5): AnalyticsController, GoogleAuthController, Controller, RichTextImageController, RichTextVideoController

### Community 189 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 203 - "TaskViewableIdsParityTest.php"
Cohesion: 0.33
Nodes (5): assertViewParity(), grantDepartmentAccessForParity(), makeClientForParity(), makeManagementForParity(), makeStaffForParity()

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **46 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `RichText`, `AuditLog`, `NotificationEventType.php`, `Illuminate\Validation\Validator`, `UserManagementController`, `App\Models\Department`, `App\Models\Task`, `SubtaskPolicy`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Builder`, `ImportTemplateBuilder`, `App\Http\Controllers\Concerns\ResolvesCurrentOrganization`, `Permission`, `TaskPolicy`, `Controller`, `CommentController`, `LoginRequest`, `CommentPolicy`, `DocumentPolicy.php`, `DocumentFolderPolicy`, `OrganizationPolicy`, `UserPolicy`, `TaskViewableIdsParityTest.php`, `RolePolicy`, `Document`, `App\Models\Concerns\BelongsToOrganization`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `Role`, `ImportBatch`, `LinkPreviewService.php`, `Organization`, `TaskManagementController.php`, `Illuminate\Http\Request`, `NotificationSettingPolicy`, `Closure`?**
  _High betweenness centrality (0.120) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditEventMailNotification.php`, `RichText`, `App\Models\Task`, `SubtaskPolicy`, `.storePending`, `App\Http\Controllers\Concerns\ResolvesCurrentOrganization`, `TaskPolicy`, `Controller`, `CommentController`, `TaskViewableIdsParityTest.php`, `Document`, `TaskManagementController`, `App\Models\Concerns\BelongsToOrganization`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `CalendarController.php`, `ImportBatch`, `LinkPreviewService.php`, `Organization`, `FileCategory.php`, `TaskManagementController.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.053) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController`, `App\Models\Department`, `DepartmentManagementController.php`, `App\Models\Task`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `App\Http\Controllers\Concerns\ResolvesCurrentOrganization`, `Controller`, `CompanyRoleRules`, `OrganizationPolicy`, `TaskViewableIdsParityTest.php`, `App\Models\Concerns\BelongsToOrganization`, `App\Models\Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Role`, `Illuminate\View\View`, `CalendarController.php`, `ImportBatch`, `TaskManagementController.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._