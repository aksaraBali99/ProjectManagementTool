# Graph Report - ProjectManagementTool  (2026-10-12)

## Corpus Check
- 442 files · ~249,934 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1891 nodes · 5354 edges · 190 communities (160 shown, 30 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 366 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `31ee52a4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Role.php
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Role
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
- OrgMember
- _form.blade.php
- Organization
- .attach
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Priority.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- FileCategory.php
- HasAdminConfigurableColors.php
- Illuminate\Database\Eloquent\Model
- config
- config
- auth.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- UserManagementController
- Comment
- require
- FileStorageService
- DocumentFolder
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- AppServiceProvider.php
- FileStorageException
- keywords
- LoginRequest
- buildEmojiPicker
- UpdateTaskRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Document
- PerformanceSeeder
- self
- UpdateUserRequest
- code-highlight.js
- FileStorageService.php
- Illuminate\Http\JsonResponse
- AccessPermission.php
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php

## God Nodes (most connected - your core abstractions)
1. `User` - 331 edges
2. `Organization` - 208 edges
3. `OrgMember` - 158 edges
4. `Task` - 146 edges
5. `Role` - 110 edges
6. `Project` - 92 edges
7. `Department` - 82 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeMeasuredTasks()` --calls--> `Subtask`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Subtask.php
- `makeMeasuredTasks()` --calls--> `Task`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Task.php
- `addOrgRole()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/OrgMember.php
- `assertEveryHelperAgreesForCurrentViewer()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/Permission.php

## Import Cycles
- None detected.

## Communities (190 total, 30 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (7): CalendarController, Carbon, DuplicateDetector, ImportFieldResolver, ImportValidationContext, ImportValidator, Illuminate\Support\Carbon

### Community 2 - "AccessProfileCache"
Cohesion: 0.16
Nodes (4): App\Enums\BoardAccessDeniedReason, Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.09
Nodes (13): TaskManagementController, Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+5 more)

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

### Community 11 - "Role"
Cohesion: 0.08
Nodes (31): PermissionManagementController, Permission, Role, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), makeClientForDeleteTest(), grantManageDocumentsForEditTest() (+23 more)

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
Cohesion: 0.14
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 86 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (18): DocumentUploadService, FileCategory, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+10 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (18): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+10 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (16): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DashboardController, Collection (+8 more)

### Community 90 - "BootstrapEnvironment"
Cohesion: 0.09
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
Cohesion: 0.05
Nodes (62): AccessPermission, Department, Organization, Project, self, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember() (+54 more)

### Community 108 - ".attach"
Cohesion: 0.29
Nodes (4): FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.06
Nodes (19): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), ProjectManagementController, bootBelongsToOrganization(), bootFlushesAccessProfiles(), AccessProfile (+11 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportController, ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportIdCodec

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "FileCategory.php"
Cohesion: 0.24
Nodes (5): PastedMediaNamer, Closure, fileFor(), FileCategory, UploadedFile

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.12
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 119 - "config"
Cohesion: 0.22
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "auth.php"
Cohesion: 0.16
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Symfony\Component\HttpFoundation\Response

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (5): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "Comment"
Cohesion: 0.13
Nodes (3): Comment, CommentObserver, CommentPolicy

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "DocumentFolder"
Cohesion: 0.13
Nodes (8): DocumentFolderController, TaskFolderController, DocumentFolder, DocumentDependencyService, makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.12
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.16
Nodes (6): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

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
Cohesion: 0.11
Nodes (9): DocumentController, TaskDocumentController, Document, up(), DocumentAccessLevel, Illuminate\Http\Request, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse (+1 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

### Community 182 - "FileStorageService.php"
Cohesion: 0.40
Nodes (3): DocumentAlreadyAttachedException, self, RuntimeException

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.11
Nodes (9): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, isAssignableStaffForProject() (+1 more)

### Community 185 - "AccessPermission.php"
Cohesion: 0.15
Nodes (4): App\Models\Concerns\FlushesAccessProfiles, CompanyRoleSyncer, findCommentCardBody(), DOMElement

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `UserManagementController`, `Role`, `Comment`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `StoreProjectRequest`, `DocumentFolder`, `AuditLog`, `Subtask`, `LoginRequest`, `Document`, `Pest.php`, `AccessPermission.php`, `Illuminate\Http\UploadedFile`, `Illuminate\View\View`, `BootstrapEnvironment`, `RichText`, `OrgMember`, `Organization`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `auth.php`?**
  _High betweenness centrality (0.176) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Http\RedirectResponse`, `UserManagementController`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `Illuminate\Validation\Validator`, `UpdateTaskRequest`, `Document`, `PerformanceSeeder`, `Pest.php`, `Illuminate\Database\Seeder`, `AccessPermission.php`, `User`, `Illuminate\View\View`, `BootstrapEnvironment`, `OrgMember`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Model`, `auth.php`?**
  _High betweenness centrality (0.076) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditEventMailNotification.php`, `Comment`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `DocumentFolder`, `AuditLog`, `Subtask`, `Document`, `Pest.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Http\UploadedFile`, `User`, `Illuminate\View\View`, `RichText`, `OrgMember`, `Organization`, `.attach`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 67 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 67 INFERRED edges - model-reasoned connections that need verification._