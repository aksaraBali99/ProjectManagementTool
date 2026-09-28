# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 397 files · ~191,941 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1676 nodes · 4435 edges · 191 communities (154 shown, 37 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 282 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `28f721e3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Department
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Task.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Closure
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
- DocumentController.php
- User
- TaskManagementController
- AuditLog
- Role.php
- Organization
- setup
- documents/create.blade.php
- app.js
- BootstrapEnvironment
- _form.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- Document
- Comment
- Illuminate\Http\Request
- FileCategory.php
- DocumentFolder
- NotificationEventType.php
- LoginRequest
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- UserManagementController
- require
- User.php
- NotificationSetting
- TagsImportBatch.php
- psr-4
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- CompanyRoleRules
- RichText
- UpdateUserRequest
- HasAdminConfigurableColors.php
- CalendarController.php
- Priority.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- buildEmojiPicker
- AuditEventNotifier
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- AuditEventMailNotification.php
- Illuminate\Database\Seeder
- CommentPolicy
- keywords
- UpdateProjectRequest
- file-chip-extension.js
- lightbox.js
- Permission
- code-highlight.js
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- link-preview-extension.js
- OrgMember

## God Nodes (most connected - your core abstractions)
1. `User` - 262 edges
2. `Organization` - 141 edges
3. `OrgMember` - 120 edges
4. `Task` - 110 edges
5. `Role` - 84 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `ImportValidator` - 42 edges
9. `Document` - 41 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `makeIneligibleProjectMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeEligibleStaffMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (191 total, 37 thin omitted)

### Community 0 - "Task"
Cohesion: 0.13
Nodes (10): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Department"
Cohesion: 0.09
Nodes (21): DepartmentManagementController, AccessPermission, Department, DepartmentSeeder, UserSeeder, makeStaffOnCalendar(), makeEligibleStaffMember(), makeProjectMember() (+13 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Task.php"
Cohesion: 0.09
Nodes (5): App\Models\Document, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

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

### Community 11 - "Closure"
Cohesion: 0.12
Nodes (6): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

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
Cohesion: 0.13
Nodes (29): Audio, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste(), buildDocumentUpload() (+21 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "DocumentController.php"
Cohesion: 0.18
Nodes (6): DocumentController, Controller, Document, Illuminate\Http\Response, Organization, Symfony\Component\HttpFoundation\StreamedResponse

### Community 87 - "User"
Cohesion: 0.05
Nodes (16): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, SubtaskPolicy, TaskPolicy (+8 more)

### Community 90 - "AuditLog"
Cohesion: 0.17
Nodes (3): AuditLog, AuditEventDatabaseNotification, Illuminate\Notifications\Notification

### Community 92 - "Organization"
Cohesion: 0.08
Nodes (33): AnalyticsController, Organization, Project, Role, makeTaskForAnalytics(), makeIneligibleProjectMember(), makeReactionClient(), makeStaffForDocumentCreate() (+25 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.12
Nodes (13): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), checkFileChipStatuses() (+5 more)

### Community 105 - "BootstrapEnvironment"
Cohesion: 0.09
Nodes (8): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Attribute, Illuminate\Console\Command, Illuminate\Database\Eloquent\Casts\Attribute

### Community 108 - "Illuminate\View\View"
Cohesion: 0.13
Nodes (6): ImportController, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.13
Nodes (10): staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, Illuminate\Support\Collection (+2 more)

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "Document"
Cohesion: 0.05
Nodes (18): Document, LinkPreview, DocumentPolicy, DocumentDependencyService, DocumentUploadService, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser (+10 more)

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (10): AuditTrailController, CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskDocumentController (+2 more)

### Community 115 - "FileCategory.php"
Cohesion: 0.09
Nodes (19): FileStorageService, PastedMediaNamer, StoredFile, Illuminate\Http\UploadedFile, RuntimeException, fileFor(), FileCategory, UploadedFile (+11 more)

### Community 116 - "DocumentFolder"
Cohesion: 0.23
Nodes (3): DocumentFolderController, DocumentFolder, DocumentFolderPolicy

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.27
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (7): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "NotificationSetting"
Cohesion: 0.25
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.25
Nodes (3): SubtaskController, Subtask, SubtaskObserver

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskStatusColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "CompanyRoleRules"
Cohesion: 0.15
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 165 - "Priority.php"
Cohesion: 0.09
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "AuditEventMailNotification.php"
Cohesion: 0.36
Nodes (4): AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.28
Nodes (4): DatabaseSeeder, OrganizationSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Permission"
Cohesion: 0.19
Nodes (6): PermissionManagementController, Permission, up(), PermissionSeeder, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest()

### Community 185 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 195 - "OrgMember"
Cohesion: 0.13
Nodes (5): OrgMember, findCommentCardBody(), DOMElement, fakeUploadDoc(), UploadedFile

## Knowledge Gaps
- **133 isolated node(s):** `CHART_PALETTE`, `richTextMounts`, `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Department`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `UserManagementController`, `Closure`, `User.php`, `NotificationSetting`, `CompanyRoleRules`, `UpdateUserRequest`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `AuditEventNotifier`, `CommentPolicy`, `ImportTemplateBuilder`, `Permission`, `OrgMember`, `TaskManagementController`, `AuditLog`, `Role.php`, `Organization`, `BootstrapEnvironment`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Document`, `Comment`, `Illuminate\Http\Request`, `FileCategory.php`, `DocumentFolder`, `NotificationEventType.php`, `LoginRequest`?**
  _High betweenness centrality (0.171) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Department`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `User.php`, `TagsImportBatch.php`, `Subtask`, `CompanyRoleRules`, `RichText`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `DocumentController.php`, `User`, `TaskManagementController`, `Role.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `Document`, `Comment`, `Illuminate\Http\Request`, `FileCategory.php`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Department`, `Illuminate\Http\RedirectResponse`, `Task.php`, `UserManagementController`, `User.php`, `StoreDepartmentRequest`, `CompanyRoleRules`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `OrgMember`, `User`, `TaskManagementController`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Document`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 50 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 50 INFERRED edges - model-reasoned connections that need verification._