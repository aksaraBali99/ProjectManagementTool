# Graph Report - ProjectManagementTool  (2026-10-12)

## Corpus Check
- 442 files · ~249,934 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1891 nodes · 5349 edges · 195 communities (158 shown, 37 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 351 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d94a43c3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task.php
- ImportValidator
- .visibleOrganizationIds
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Model
- LARAVEL_README.md
- StoreProjectRequest
- Illuminate\Database\Eloquent\Relations\HasMany
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
- Pest.php
- Illuminate\Database\Seeder
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- Illuminate\Http\UploadedFile
- User
- Illuminate\View\View
- BootstrapEnvironment
- RichText
- TaskStatus.php
- setup
- documents/create.blade.php
- app.js
- Illuminate\Database\Eloquent\Builder
- _form.blade.php
- Organization
- .attach
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- FileCategory.php
- HasAdminConfigurableColors.php
- NotificationSettingPolicy
- config
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- PermissionManagementController.php
- Comment
- require
- Role.php
- FileStorageService
- DocumentFolder
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- AccessProfileCache
- FileStorageException
- AuditEventNotifier
- CommentPolicy
- DocumentUploadService
- LoginRequest
- buildEmojiPicker
- UpdateTaskRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Document
- PerformanceSeeder
- DocumentFolderPolicy
- SubtaskPolicy
- UpdateUserRequest
- code-highlight.js
- RuntimeException
- Illuminate\Http\JsonResponse
- AccessProfileParityTest.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- post-create-project-cmd
- autoload-dev
- findKanbanCard

## God Nodes (most connected - your core abstractions)
1. `User` - 331 edges
2. `Organization` - 206 edges
3. `OrgMember` - 161 edges
4. `Task` - 146 edges
5. `Role` - 110 edges
6. `Project` - 92 edges
7. `Department` - 84 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffAssigneeForDocumentUpload()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/RichText/DocumentUploadTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php

## Import Cycles
- None detected.

## Communities (195 total, 37 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (7): CalendarController, Carbon, DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Illuminate\Support\Carbon

### Community 3 - "Task"
Cohesion: 0.07
Nodes (15): TaskManagementController, Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), makeStaffAssigneeForDocumentUpload() (+7 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (5): CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.13
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

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

### Community 11 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (21): Permission, TaskPriorityColor, up(), Illuminate\Database\Eloquent\Model, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), makeLinkOnlyDocumentForDeleteTest(), grantManageDocumentsForEditTest() (+13 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

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
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "Pest.php"
Cohesion: 0.09
Nodes (24): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+16 more)

### Community 54 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 86 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (16): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage() (+8 more)

### Community 87 - "User"
Cohesion: 0.07
Nodes (12): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+4 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (17): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, Collection (+9 more)

### Community 90 - "BootstrapEnvironment"
Cohesion: 0.07
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, Illuminate\Console\Command

### Community 91 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.10
Nodes (18): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+10 more)

### Community 107 - "Organization"
Cohesion: 0.06
Nodes (68): isAssignableStaffForProject(), AccessPermission, Department, Organization, OrgMember, Project, Role, makeTaskForAnalytics() (+60 more)

### Community 108 - ".attach"
Cohesion: 0.29
Nodes (4): FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.06
Nodes (20): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), ProjectManagementController, bootBelongsToOrganization(), bootFlushesAccessProfiles(), AccessProfile (+12 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (7): ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "FileCategory.php"
Cohesion: 0.22
Nodes (4): PastedMediaNamer, fileFor(), FileCategory, UploadedFile

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.17
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.10
Nodes (6): NotificationSettingsController, OrganizationManagementController, UserManagementController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "Comment"
Cohesion: 0.14
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Role.php"
Cohesion: 0.07
Nodes (4): CompanyRoleSyncer, findCommentCardBody(), DOMElement, makeLinkerTestDocument()

### Community 145 - "DocumentFolder"
Cohesion: 0.12
Nodes (9): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentDependencyService, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "AccessProfileCache"
Cohesion: 0.16
Nodes (3): AppServiceProvider, AccessProfileCache, Illuminate\Support\ServiceProvider

### Community 158 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "UpdateTaskRequest"
Cohesion: 0.10
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Document"
Cohesion: 0.10
Nodes (9): DocumentController, RichTextDocumentController, TaskDocumentController, Document, up(), DocumentAccessLevel, Illuminate\Http\Request, Illuminate\Http\Response (+1 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 182 - "RuntimeException"
Cohesion: 0.50
Nodes (3): DocumentAlreadyAttachedException, self, RuntimeException

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.14
Nodes (7): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 185 - "AccessProfileParityTest.php"
Cohesion: 0.60
Nodes (4): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays()

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 189 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task.php`, `ImportValidator`, `.visibleOrganizationIds`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Illuminate\Database\Eloquent\Model`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `StoreProjectRequest`, `DocumentFolder`, `NotificationEventType.php`, `AccessProfileCache`, `AuditEventNotifier`, `CommentPolicy`, `DocumentUploadService`, `LoginRequest`, `Document`, `DocumentFolderPolicy`, `SubtaskPolicy`, `Pest.php`, `AccessProfileParityTest.php`, `Illuminate\View\View`, `BootstrapEnvironment`, `RichText`, `Illuminate\Database\Eloquent\Builder`, `Organization`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `NotificationSettingPolicy`, `Closure`?**
  _High betweenness centrality (0.161) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Task.php`, `ImportValidator`, `.visibleOrganizationIds`, `Task`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `Illuminate\Validation\Validator`, `UpdateTaskRequest`, `Document`, `PerformanceSeeder`, `Pest.php`, `Illuminate\Database\Seeder`, `AccessProfileParityTest.php`, `User`, `Illuminate\View\View`, `BootstrapEnvironment`, `Illuminate\Support\Collection`, `ImportBatch`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Task.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Illuminate\Database\Eloquent\Model`, `Role.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `CommentPolicy`, `DocumentUploadService`, `Document`, `SubtaskPolicy`, `Pest.php`, `Illuminate\Http\JsonResponse`, `Illuminate\View\View`, `RichText`, `Illuminate\Database\Eloquent\Builder`, `Organization`, `.attach`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `FileCategory.php`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 67 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 67 INFERRED edges - model-reasoned connections that need verification._