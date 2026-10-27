# Graph Report - ProjectManagementTool  (2026-10-27)

## Corpus Check
- 460 files · ~266,921 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1957 nodes · 5568 edges · 211 communities (170 shown, 41 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 373 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `85d5a0a0`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentFolder
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Model
- AuditEventMailNotification.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- OrgMember
- LARAVEL_README.md
- ProjectManagementController
- Organization
- users/create.blade.php
- users/edit.blade.php
- tasks/edit.blade.php
- ProjectManagementTool
- projects/create.blade.php
- projects/edit.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- CLAUDE.md
- copilot-instructions.md
- rich-text-editor.js
- _password-input.blade.php
- ImportTemplateBuilder
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
- TaskStatus
- Comment
- setup
- documents/create.blade.php
- kanban._card
- app.js
- Illuminate\Support\Collection
- _form.blade.php
- Role
- TaskDocumentLinker.php
- CodeLanguageClassSanitizer
- FileStorageException
- BootstrapEnvironment
- LinkPreviewService.php
- TaskManagementController
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- ValidatesTaskAssignment.php
- ProjectStatus.php
- config
- Role.php
- task-colors/edit.blade.php
- StoreDepartmentRequest
- Pest.php
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- AppServiceProvider.php
- psr-4
- AuditEventNotifier
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- RichText
- static
- AuditLog
- LoginRequest
- keywords
- web.php
- buildEmojiPicker
- DocumentUploadService
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- StoreProjectRequest
- PerformanceSeeder
- Task.php
- FileStorageService
- UserManagementController
- code-highlight.js
- Document
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- TaskIndexesTest.php
- LinkPreviewController.php
- CalendarController.php
- UpdateTaskRequest
- DocumentDependencyService.php
- config
- RuntimeException
- UpdateDepartmentRequest
- UpdateTaskPriorityColorsRequest
- UpdateTaskStatusColorsRequest
- TaskViewableIdsParityTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 338 edges
2. `Organization` - 209 edges
3. `OrgMember` - 171 edges
4. `Task` - 167 edges
5. `Role` - 111 edges
6. `Project` - 93 edges
7. `Department` - 85 edges
8. `Document` - 73 edges
9. `AuditLog` - 45 edges
10. `DocumentFolder` - 44 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeMeasuredTasks()` --calls--> `Comment`  [INFERRED]
  tests/Feature/Performance/PageQueryCountTest.php → app/Models/Comment.php
- `makeLegacyTask()` --calls--> `Department`  [INFERRED]
  tests/Feature/Console/BackfillTaskCompletedAtTest.php → app/Models/Department.php
- `makeAttachableDocumentSet()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentAttachableInCompanyParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php

## Import Cycles
- None detected.

## Communities (211 total, 41 thin omitted)

### Community 0 - "DocumentFolder"
Cohesion: 0.09
Nodes (9): DocumentFolderController, DocumentFolder, DocumentFolderPolicy, Illuminate\Database\Eloquent\Builder, makeWarningTestFolder(), makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder() (+1 more)

### Community 1 - "ImportValidator"
Cohesion: 0.06
Nodes (11): ImportBatch, DuplicateDetector, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver (+3 more)

### Community 2 - "AccessProfileCache"
Cohesion: 0.15
Nodes (4): Collection, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.06
Nodes (22): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, addTaskAudit(), completedAtOf(), makeLegacyTask() (+14 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (7): CommentReaction, organization(), ImportRow, PastedMedia, TaskPriorityColor, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

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
Cohesion: 0.11
Nodes (18): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout (+10 more)

### Community 9 - "dependencies"
Cohesion: 0.04
Nodes (45): concurrently, @fontsource/inter, intl-tel-input, is-emoji-supported, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies (+37 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 11 - "OrgMember"
Cohesion: 0.11
Nodes (3): OrgMember, findKanbanCard(), DOMElement

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Organization"
Cohesion: 0.06
Nodes (37): Organization, Project, DepartmentSeeder, makeTaskForAnalytics(), makeReactionClient(), makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeClientForDeleteTest() (+29 more)

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

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 54 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 86 - "FileCategory.php"
Cohesion: 0.09
Nodes (21): PastedMediaNamer, StoredFile, Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeMediaFile(), UploadedFile, fileFor() (+13 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (21): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy (+13 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController (+3 more)

### Community 90 - "TaskStatus"
Cohesion: 0.22
Nodes (5): AnalyticsController, KanbanController, TaskStatus, up(), Illuminate\Database\Eloquent\Collection

### Community 91 - "Comment"
Cohesion: 0.21
Nodes (3): Comment, CommentObserver, addComment()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 105 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (11): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), DashboardController, Collection, Illuminate\Support\Collection, flattenCalendarCells() (+3 more)

### Community 107 - "Role"
Cohesion: 0.07
Nodes (45): PermissionManagementController, AccessPermission, Department, Permission, Role, up(), PermissionSeeder, makeStaffOnCalendar() (+37 more)

### Community 108 - "TaskDocumentLinker.php"
Cohesion: 0.22
Nodes (5): DocumentAlreadyAttachedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, makeLinkerTestDocument()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "FileStorageException"
Cohesion: 0.27
Nodes (3): FileStorageException, self, Throwable

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.11
Nodes (5): LinkPreview, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 115 - "Closure"
Cohesion: 0.18
Nodes (7): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FlushAccessProfileCache, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "ValidatesTaskAssignment.php"
Cohesion: 0.18
Nodes (3): SubtaskController, isAssignableStaffForProject(), StoreTaskRequest

### Community 119 - "ProjectStatus.php"
Cohesion: 0.12
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Role.php"
Cohesion: 0.12
Nodes (3): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

### Community 134 - "Pest.php"
Cohesion: 0.09
Nodes (21): DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, makeMeasuredTasks(), measurePageGrowth(), runImportValidationAndCommit(), buildImportTestFile(), countQueries() (+13 more)

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): AccessControlController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.16
Nodes (6): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 158 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootFlushesAccessProfiles(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 159 - "AuditLog"
Cohesion: 0.16
Nodes (4): AuditLog, NotificationSettingsResolver, NotificationEventType, assertTaskDocumentAttachedAuditEntry()

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (12): AbandonStaleImportBatches, BackfillTaskCompletedAt, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, DatabaseSeeder, OrganizationSeeder, PerformanceSeeder (+4 more)

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Document"
Cohesion: 0.09
Nodes (15): DocumentController, TaskDocumentController, Document, Attribute, DocumentAccessLevel, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Http\Request, Illuminate\Http\Response (+7 more)

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.14
Nodes (7): CommentReactionController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, TaskFolderController, Illuminate\Http\JsonResponse

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 199 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 203 - "RuntimeException"
Cohesion: 0.50
Nodes (3): FolderAlreadyLinkedException, self, RuntimeException

### Community 207 - "TaskViewableIdsParityTest.php"
Cohesion: 0.40
Nodes (4): assertViewParity(), makeClientForParity(), makeManagementForParity(), makeStaffForParity()

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `Task`, `Pest.php`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController`, `Organization`, `User.php`, `AuditEventNotifier`, `Subtask`, `RichText`, `static`, `AuditLog`, `LoginRequest`, `web.php`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `Task.php`, `ImportTemplateBuilder`, `UserManagementController`, `Document`, `NotificationSetting`, `DocumentDependencyService.php`, `TaskViewableIdsParityTest.php`, `Illuminate\View\View`, `TaskStatus`, `Comment`, `Illuminate\Support\Collection`, `Role`, `TaskDocumentLinker.php`, `BootstrapEnvironment`, `LinkPreviewService.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ProjectStatus.php`, `Role.php`?**
  _High betweenness centrality (0.164) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `DocumentFolder`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Model`, `StoreDepartmentRequest`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `ProjectManagementController`, `User.php`, `Illuminate\Validation\Validator`, `web.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `Task.php`, `ImportTemplateBuilder`, `UserManagementController`, `Document`, `CalendarController.php`, `TaskViewableIdsParityTest.php`, `User`, `Illuminate\View\View`, `TaskStatus`, `Illuminate\Support\Collection`, `Role`, `TaskDocumentLinker.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role.php`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `DocumentFolder`, `ImportValidator`, `Illuminate\Database\Eloquent\Model`, `Pest.php`, `OrgMember`, `User.php`, `Organization`, `Subtask`, `RichText`, `static`, `AuditLog`, `web.php`, `DocumentUploadService`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Task.php`, `Document`, `Illuminate\Http\JsonResponse`, `LinkPreviewController.php`, `CalendarController.php`, `DocumentDependencyService.php`, `TaskViewableIdsParityTest.php`, `FileCategory.php`, `User`, `TaskStatus`, `Comment`, `Illuminate\Support\Collection`, `Role`, `TaskDocumentLinker.php`, `LinkPreviewService.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `ValidatesTaskAssignment.php`, `Role.php`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._