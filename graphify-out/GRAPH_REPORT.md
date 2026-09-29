# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 408 files · ~207,198 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1714 nodes · 4606 edges · 196 communities (160 shown, 36 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c7445cf0`
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
- FileCategory.php
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
- Role
- setup
- documents/create.blade.php
- app.js
- App\Models\Task
- _form.blade.php
- AccessPermission.php
- DocumentFolder
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- RichText
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Document
- FileStorageException
- Illuminate\Database\Eloquent\Model
- Illuminate\Http\Request
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- StoreDepartmentRequest
- createRichTextEditor
- require
- App\Models\User
- Illuminate\Http\UploadedFile
- NotificationSetting
- psr-4
- AuditLog
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
- FileStorageService
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Project
- Illuminate\Database\Seeder
- Comment
- keywords
- UpdateTaskStatusColorsRequest
- FileStorageService.php
- lightbox.js
- Permission
- StoreProjectRequest
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- grantAttachDocumentsPermission
- CompanyRoleRules
- link-preview-extension.js
- link-preview-thumbnail.js
- CommentMentionHighlightTest.php
- DocumentAccessLevel.php

## God Nodes (most connected - your core abstractions)
1. `User` - 275 edges
2. `Organization` - 153 edges
3. `OrgMember` - 128 edges
4. `Task` - 116 edges
5. `Role` - 91 edges
6. `Project` - 79 edges
7. `Department` - 64 edges
8. `Document` - 58 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `grantAttachDocumentsPermission()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentPickerTest.php → app/Models/Permission.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (196 total, 36 thin omitted)

### Community 0 - "Task"
Cohesion: 0.10
Nodes (11): Task, TaskObserver, TaskPolicy, TaskDocumentLinker, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (8): CalendarController, DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Carbon, Carbon\Carbon, Illuminate\Support\Carbon

### Community 3 - "OrgMember"
Cohesion: 0.18
Nodes (6): OrgMember, App\Models\Role, makeStaffForDocumentCreate(), makeStaffForNewMenuTest(), makeStaffForUploadTest(), joinOrg()

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
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

### Community 11 - "FileCategory.php"
Cohesion: 0.16
Nodes (6): PastedMedia, PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "StoreTaskRequest"
Cohesion: 0.14
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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
Nodes (14): AnalyticsController, Organization, Illuminate\Database\Eloquent\Relations\HasMany, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument() (+6 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (15): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, RolePolicy, UserPolicy (+7 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.12
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 92 - "Role"
Cohesion: 0.10
Nodes (23): AccessPermission, Department, Role, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeReactionEligibleStaff() (+15 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 105 - "App\Models\Task"
Cohesion: 0.12
Nodes (7): App\Models\Document, App\Models\Task, makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makePickerDocument(), Document

### Community 108 - "DocumentFolder"
Cohesion: 0.20
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (10): staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+2 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportBatch, CompanyRoleSyncer, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 115 - "Document"
Cohesion: 0.15
Nodes (9): DocumentController, Document, DocumentAccessLevel, Illuminate\Http\Response, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentSetForParity() (+1 more)

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 118 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 119 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (12): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, Document (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.23
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (6): AccessControlController, GoogleAuthController, DepartmentManagementController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (15): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 145 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.14
Nodes (6): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.24
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (9): AuditTrailController, AuthenticatedSessionController, Controller, ImportController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController (+1 more)

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 165 - "ProjectStatus.php"
Cohesion: 0.12
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Project"
Cohesion: 0.09
Nodes (15): ProjectManagementController, isAssignableStaffForProject(), Project, ProjectPolicy, makeTaskForAnalytics(), makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForUploadTest() (+7 more)

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "Comment"
Cohesion: 0.13
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 181 - "FileStorageService.php"
Cohesion: 0.33
Nodes (4): DocumentAlreadyAttachedException, self, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.13
Nodes (12): PermissionManagementController, Permission, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), grantManageDocumentsForLinkedTasksTest(), grantPermissionForLinkedTasksTest() (+4 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "grantAttachDocumentsPermission"
Cohesion: 0.67
Nodes (3): Role, grantAttachDocumentsPermission(), User

### Community 189 - "CompanyRoleRules"
Cohesion: 0.18
Nodes (5): bootBelongsToOrganization(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **36 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `FileCategory.php`, `App\Models\User`, `NotificationSetting`, `AuditLog`, `Subtask`, `UserManagementController`, `Illuminate\View\View`, `ProjectStatus.php`, `LoginRequest`, `Project`, `ImportTemplateBuilder`, `Permission`, `CompanyRoleRules`, `DocumentAccessLevel.php`, `Organization`, `TaskManagementController`, `BootstrapEnvironment`, `Role`, `App\Models\Task`, `AccessPermission.php`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.134) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `UserManagementController`, `Illuminate\View\View`, `Project`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Permission`, `CompanyRoleRules`, `User`, `TaskManagementController`, `Role`, `App\Models\Task`, `AccessPermission.php`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `FileCategory.php`, `App\Models\User`, `AuditLog`, `Subtask`, `Illuminate\View\View`, `Project`, `Comment`, `Permission`, `CompanyRoleRules`, `DocumentAccessLevel.php`, `Organization`, `User`, `TaskManagementController`, `Role`, `App\Models\Task`, `AccessPermission.php`, `DocumentFolder`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `RichText`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._