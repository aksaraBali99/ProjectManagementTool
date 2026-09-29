# Graph Report - ProjectManagementTool  (2026-09-29)

## Corpus Check
- 407 files · ~205,081 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1709 nodes · 4597 edges · 195 communities (157 shown, 38 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 296 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `bbaa62f6`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Role.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- FileStorageService
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
- Department.php
- _form.blade.php
- OrgMember
- CommentPolicy
- CodeLanguageClassSanitizer
- .__invoke
- ImportBatch
- LinkPreview
- Comment
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Document
- FileStorageException
- Illuminate\Http\Request
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileCategory.php
- createRichTextEditor
- require
- Illuminate\Http\UploadedFile
- ImportController.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- UserManagementController
- Illuminate\Database\Eloquent\Model
- Illuminate\View\View
- HasAdminConfigurableColors.php
- config
- StoreProjectRequest
- UpdateTaskStatusColorsRequest
- buildEmojiPicker
- link-preview-thumbnail.js
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- TagsImportBatch.php
- keywords
- ValidatesTaskAssignment.php
- CommentMentionHighlightTest.php
- lightbox.js
- Role
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- CompanyRoleRules
- link-preview-extension.js
- DocumentFolder
- LoginRequest
- DocumentAccessLevel.php

## God Nodes (most connected - your core abstractions)
1. `User` - 278 edges
2. `Organization` - 155 edges
3. `OrgMember` - 130 edges
4. `Task` - 120 edges
5. `Role` - 93 edges
6. `Project` - 79 edges
7. `Department` - 64 edges
8. `Document` - 63 edges
9. `AuditLog` - 43 edges
10. `ImportValidator` - 42 edges

## Surprising Connections (you probably didn't know these)
- `assertTaskDocumentAttachedAuditEntry()` --calls--> `AuditLog`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentLinkerAuditTest.php → app/Models/AuditLog.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (195 total, 38 thin omitted)

### Community 0 - "Task"
Cohesion: 0.09
Nodes (14): Task, TaskObserver, TaskPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+6 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.21
Nodes (6): AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

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
Cohesion: 0.07
Nodes (30): AccessPermission, Department, Organization, DepartmentSeeder, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+22 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (16): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, NotificationSettingPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+8 more)

### Community 91 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (14): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks() (+6 more)

### Community 107 - "OrgMember"
Cohesion: 0.11
Nodes (8): OrgMember, makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForNewMenuTest(), findKanbanCard(), DOMElement, joinOrg()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - ".__invoke"
Cohesion: 0.62
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

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
Cohesion: 0.11
Nodes (10): DocumentAlreadyAttachedException, self, DocumentController, LinkPreviewController, TaskDocumentController, Document, TaskDocumentLinker, DocumentAccessLevel (+2 more)

### Community 116 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 119 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (11): AuthenticatedSessionController, GoogleAuthController, CommentReactionController, Controller, DashboardController, Collection, RichTextAudioController, RichTextDocumentController (+3 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.10
Nodes (6): NotificationSettingsController, OrganizationManagementController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 134 - "FileCategory.php"
Cohesion: 0.20
Nodes (6): PastedMediaNamer, Closure, RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (15): StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.13
Nodes (6): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.17
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 159 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (8): AccessControlController, AuditTrailController, DepartmentManagementController, KanbanController, NotificationController, RoleManagementController, SettingsController, Illuminate\View\View

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

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.18
Nodes (9): staffOptionsByProject(), resolveCurrentOrganization(), ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet(), assertDocumentViewParity() (+1 more)

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 177 - "TagsImportBatch.php"
Cohesion: 0.26
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Role"
Cohesion: 0.08
Nodes (35): AnalyticsController, PermissionManagementController, isAssignableStaffForProject(), Permission, Project, Role, up(), PermissionSeeder (+27 more)

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "CompanyRoleRules"
Cohesion: 0.15
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 193 - "DocumentFolder"
Cohesion: 0.21
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `User.php`, `AuditLog`, `UserManagementController`, `Illuminate\View\View`, `StoreProjectRequest`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Role`, `CompanyRoleRules`, `DocumentFolder`, `LoginRequest`, `DocumentAccessLevel.php`, `Organization`, `TaskManagementController`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `OrgMember`, `CommentPolicy`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Role.php`, `Illuminate\Http\RedirectResponse`, `User.php`, `StoreTaskRequest`, `UserManagementController`, `Illuminate\Database\Eloquent\Model`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Role`, `CompanyRoleRules`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Department.php`, `OrgMember`, `.__invoke`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `FileCategory.php`, `AuditEventMailNotification.php`, `User.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\View\View`, `TagsImportBatch.php`, `ValidatesTaskAssignment.php`, `Role`, `CompanyRoleRules`, `DocumentAccessLevel.php`, `Organization`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.__invoke`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Document`, `Task.php`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 51 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 51 INFERRED edges - model-reasoned connections that need verification._