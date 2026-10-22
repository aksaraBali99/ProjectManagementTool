# Graph Report - ProjectManagementTool  (2026-10-22)

## Corpus Check
- 452 files · ~260,137 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1920 nodes · 5441 edges · 199 communities (169 shown, 30 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 356 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `43297a3f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentFolder
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
- OrgMember
- LARAVEL_README.md
- ProjectManagementController.php
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
- AccessProfile
- departments/create.blade.php
- departments/edit.blade.php
- organizations/create.blade.php
- organizations/edit.blade.php
- roles/edit.blade.php
- permissions.blade.php
- tasks/create.blade.php
- FileCategory.php
- User
- Illuminate\View\View
- Organization
- Comment
- Illuminate\Database\Eloquent\Model
- setup
- documents/create.blade.php
- app.js
- Illuminate\Support\Collection
- _form.blade.php
- Role
- .attach
- CodeLanguageClassSanitizer
- Permission
- ImportBatch
- LinkPreview
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- Department.php
- StoreProjectRequest
- config
- task-colors/edit.blade.php
- StoreTaskRequest
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- AppServiceProvider.php
- psr-4
- AuditLog
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- RichText
- static
- CalendarController.php
- RolePolicy
- keywords
- post-create-project-cmd
- buildEmojiPicker
- DocumentListTest.php
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateTaskPriorityColorsRequest
- PerformanceSeeder
- Task.php
- AuditEventNotifier
- UserManagementController
- code-highlight.js
- Document
- Illuminate\Http\Request
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- TaskIndexesTest.php
- UpdateTaskRequest
- AccessPermission.php

## God Nodes (most connected - your core abstractions)
1. `User` - 337 edges
2. `Organization` - 206 edges
3. `OrgMember` - 167 edges
4. `Task` - 151 edges
5. `Role` - 111 edges
6. `Project` - 92 edges
7. `Department` - 84 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocumentsForEditTest()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentEditTest.php → app/Models/Permission.php

## Import Cycles
- None detected.

## Communities (199 total, 30 thin omitted)

### Community 0 - "DocumentFolder"
Cohesion: 0.18
Nodes (8): DocumentFolderController, TaskFolderController, DocumentFolder, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.17
Nodes (3): Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.09
Nodes (14): Task, TaskObserver, TaskPolicy, DocumentDependencyService, Illuminate\Database\Eloquent\SoftDeletes, uploadDocumentForDownload(), emojiTaskPayload(), altTextTaskUpdate() (+6 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
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
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 9 - "dependencies"
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "OrgMember"
Cohesion: 0.10
Nodes (8): OrgMember, CompanyRoleSyncer, makeStaffForFolderTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest(), joinOrg(), makeStaffForPairsParity(), makeStaffForEligibility()

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
Nodes (5): users._unsaved-changes-guard, tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks

### Community 47 - "rich-text-editor.js"
Cohesion: 0.12
Nodes (30): Audio, FileChip, ResizableImage, basenameNoExtension(), buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildClipboardMediaPaste() (+22 more)

### Community 50 - "Pest.php"
Cohesion: 0.09
Nodes (25): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+17 more)

### Community 54 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 86 - "FileCategory.php"
Cohesion: 0.06
Nodes (29): config(), prefix(), FileStorageException, self, FileStorageService, PastedMediaNamer, StoredFile, Attribute (+21 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (25): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory (+17 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (9): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, NotificationController, OrganizationManagementController, RoleManagementController, SettingsController (+1 more)

### Community 90 - "Organization"
Cohesion: 0.11
Nodes (8): resolveCurrentOrganization(), DepartmentManagementController, KanbanController, Organization, makeLinkOnlyDocumentForDeleteTest(), makeLinkerTestDocument(), makeAttachTestDocument(), makePickerDocument()

### Community 91 - "Comment"
Cohesion: 0.15
Nodes (3): Comment, CommentPolicy, addComment()

### Community 92 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.11
Nodes (4): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "Illuminate\Support\Collection"
Cohesion: 0.15
Nodes (13): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), DashboardController, Collection, Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity() (+5 more)

### Community 107 - "Role"
Cohesion: 0.07
Nodes (51): isAssignableStaffForProject(), AccessPermission, Department, Project, Role, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember() (+43 more)

### Community 108 - ".attach"
Cohesion: 0.21
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Permission"
Cohesion: 0.11
Nodes (16): PermissionManagementController, Permission, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), createOwnerForGrant(), grantManageDocuments() (+8 more)

### Community 111 - "ImportBatch"
Cohesion: 0.11
Nodes (8): AbandonStaleImportBatches, ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "Closure"
Cohesion: 0.19
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "StoreProjectRequest"
Cohesion: 0.15
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (5): AccessControlController, GoogleAuthController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "AuditLog"
Cohesion: 0.11
Nodes (6): AuditLog, AuditEventDatabaseNotification, NotificationEventType, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 149 - "Subtask"
Cohesion: 0.21
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (7): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 158 - "static"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 159 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 166 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "DocumentListTest.php"
Cohesion: 0.50
Nodes (3): makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (12): CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, LoginRequest, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PerformanceSeeder (+4 more)

### Community 178 - "AuditEventNotifier"
Cohesion: 0.20
Nodes (5): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Document"
Cohesion: 0.11
Nodes (9): DocumentController, Document, DocumentUploadService, FileCategory, up(), DocumentAccessLevel, Illuminate\Http\Response, Symfony\Component\HttpFoundation\StreamedResponse (+1 more)

### Community 184 - "Illuminate\Http\Request"
Cohesion: 0.12
Nodes (10): CommentReactionController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, TaskDocumentController (+2 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 197 - "AccessPermission.php"
Cohesion: 0.16
Nodes (5): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement, findKanbanCard(), DOMElement

## Knowledge Gaps
- **131 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+126 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController.php`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `AuditLog`, `Subtask`, `RichText`, `static`, `RolePolicy`, `DocumentListTest.php`, `PerformanceSeeder`, `Task.php`, `Pest.php`, `UserManagementController`, `Document`, `NotificationSetting`, `AccessPermission.php`, `Illuminate\View\View`, `Organization`, `Comment`, `Illuminate\Support\Collection`, `Role`, `Permission`, `ImportBatch`, `LinkPreview`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Department.php`, `StoreProjectRequest`, `Role.php`?**
  _High betweenness centrality (0.156) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `StoreTaskRequest`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController.php`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Validation\Validator`, `CalendarController.php`, `DocumentListTest.php`, `PerformanceSeeder`, `Task.php`, `Pest.php`, `UserManagementController`, `Document`, `AccessPermission.php`, `User`, `Illuminate\View\View`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `Role`, `Permission`, `ImportBatch`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Department.php`, `Role.php`?**
  _High betweenness centrality (0.075) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `DocumentFolder`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditEventMailNotification.php`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `AuditLog`, `Subtask`, `RichText`, `static`, `CalendarController.php`, `Task.php`, `Pest.php`, `Document`, `Illuminate\Http\Request`, `AccessPermission.php`, `FileCategory.php`, `User`, `Illuminate\View\View`, `Organization`, `Comment`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `Role`, `.attach`, `ImportBatch`, `LinkPreview`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Department.php`, `Role.php`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._