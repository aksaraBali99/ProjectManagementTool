# Graph Report - ProjectManagementTool  (2026-10-21)

## Corpus Check
- 446 files · ~255,353 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1902 nodes · 5386 edges · 199 communities (167 shown, 32 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 355 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fd303943`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
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
- ProjectStatus.php
- Illuminate\Database\Eloquent\Relations\HasMany
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- CLAUDE.md
- copilot-instructions.md
- rich-text-editor.js
- _password-input.blade.php
- Pest.php
- Illuminate\Support\Collection
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
- AccessProfile
- Comment
- setup
- documents/create.blade.php
- app.js
- OrgMember
- _form.blade.php
- Organization
- .attach
- CodeLanguageClassSanitizer
- static
- ImportBatch
- LinkPreview
- FileStorageService
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- Illuminate\Database\Seeder
- FileCategory.php
- config
- Comment.php
- task-colors/edit.blade.php
- StoreTaskRequest
- FileStorageException
- NotificationSetting
- require
- UpdateUserRequest
- AppServiceProvider.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- RichText
- PerformanceBaseline
- Illuminate\Database\Eloquent\Model
- TaskManagementController
- keywords
- LoginRequest
- buildEmojiPicker
- config
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- PerformanceSeeder
- UpdateTaskStatusColorsRequest
- TagsImportBatch.php
- UserManagementController
- code-highlight.js
- web.php
- Document
- RuntimeException
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- StoreProjectRequest
- TaskIndexesTest.php
- UpdateTaskRequest
- .resolvePending
- post-create-project-cmd

## God Nodes (most connected - your core abstractions)
1. `User` - 334 edges
2. `Organization` - 206 edges
3. `OrgMember` - 163 edges
4. `Task` - 150 edges
5. `Role` - 111 edges
6. `Project` - 92 edges
7. `Department` - 84 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
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

## Communities (199 total, 32 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.15
Nodes (4): Collection, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.08
Nodes (14): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload() (+6 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditEventMailNotification.php"
Cohesion: 0.21
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

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

### Community 11 - "Role"
Cohesion: 0.07
Nodes (44): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), makeStaffOnCalendar(), makeEligibleStaffMember() (+36 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "ProjectStatus.php"
Cohesion: 0.12
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "Pest.php"
Cohesion: 0.09
Nodes (24): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+16 more)

### Community 54 - "Illuminate\Support\Collection"
Cohesion: 0.09
Nodes (18): CalendarController, Carbon, eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), DashboardController, Collection (+10 more)

### Community 86 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (18): DocumentUploadService, FileCategory, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fakeAudio() (+10 more)

### Community 87 - "User"
Cohesion: 0.04
Nodes (23): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+15 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.06
Nodes (16): AccessControlController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController (+8 more)

### Community 90 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 91 - "Comment"
Cohesion: 0.17
Nodes (4): CommentController, Comment, CommentPolicy, addComment()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "OrgMember"
Cohesion: 0.12
Nodes (9): OrgMember, makeClientForDeleteTest(), makeStaffForFolderTest(), makeStaffForUploadTest(), findKanbanCard(), DOMElement, joinOrg(), makeStaffForPairsParity() (+1 more)

### Community 107 - "Organization"
Cohesion: 0.05
Nodes (42): AnalyticsController, isAssignableStaffForProject(), Organization, Project, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeStaffForDocumentCreate() (+34 more)

### Community 108 - ".attach"
Cohesion: 0.25
Nodes (4): FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "static"
Cohesion: 0.15
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "Closure"
Cohesion: 0.20
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "Illuminate\Database\Seeder"
Cohesion: 0.14
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 119 - "FileCategory.php"
Cohesion: 0.22
Nodes (4): PastedMediaNamer, fileFor(), FileCategory, UploadedFile

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Comment.php"
Cohesion: 0.18
Nodes (3): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

### Community 133 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 134 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 139 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.13
Nodes (6): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.22
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 159 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.16
Nodes (5): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, makeStaffForNewMenuTest()

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "config"
Cohesion: 0.25
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.09
Nodes (5): AbandonStaleImportBatches, CleanupStalePendingMedia, SeedPerformanceData, PerformanceSeeder, Illuminate\Console\Command

### Community 178 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "Document"
Cohesion: 0.08
Nodes (16): CommentReactionController, DocumentController, DocumentFolderController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController (+8 more)

### Community 185 - "RuntimeException"
Cohesion: 0.50
Nodes (3): DocumentAlreadyAttachedException, self, RuntimeException

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 197 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

## Knowledge Gaps
- **131 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+126 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **32 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `NotificationSetting`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ProjectStatus.php`, `AuditLog`, `Subtask`, `RichText`, `PerformanceBaseline`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `LoginRequest`, `Pest.php`, `UserManagementController`, `Illuminate\Support\Collection`, `web.php`, `Document`, `Illuminate\Http\UploadedFile`, `Illuminate\View\View`, `Comment`, `Project.php`, `OrgMember`, `Organization`, `static`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Comment.php`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Role.php`, `ImportValidator`, `AccessProfileCache`, `StoreTaskRequest`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceBaseline`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `PerformanceSeeder`, `Pest.php`, `UserManagementController`, `Illuminate\Support\Collection`, `web.php`, `Document`, `User`, `Illuminate\View\View`, `Project.php`, `OrgMember`, `static`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Seeder`, `Comment.php`?**
  _High betweenness centrality (0.075) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Role.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Role`, `Illuminate\Database\Eloquent\Relations\HasMany`, `AuditLog`, `Subtask`, `RichText`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `Pest.php`, `web.php`, `Illuminate\Support\Collection`, `Document`, `.resolvePending`, `Illuminate\Http\UploadedFile`, `User`, `Illuminate\View\View`, `Comment`, `Project.php`, `OrgMember`, `Organization`, `.attach`, `static`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `FileCategory.php`, `Comment.php`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._