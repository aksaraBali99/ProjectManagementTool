# Graph Report - ProjectManagementTool  (2026-10-30)

## Corpus Check
- 460 files · ~267,648 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1958 nodes · 5574 edges · 211 communities (167 shown, 44 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 375 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `418feb3e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Pest.php
- ImportValidator
- AccessProfileCache
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- OrgMember
- LARAVEL_README.md
- Permission
- AccessPermission.php
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
- TaskManagementController.php
- User
- Illuminate\View\View
- TaskStatus
- TagsImportBatch.php
- setup
- documents/create.blade.php
- kanban._card
- app.js
- TaskObserver
- _form.blade.php
- Organization
- .attach
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- Illuminate\Console\Command
- LinkPreview
- static
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- StoreTaskRequest
- ProjectStatus.php
- config
- task-colors/edit.blade.php
- StoreDepartmentRequest
- ImportBatch
- Illuminate\Http\RedirectResponse
- require
- UpdateUserRequest
- FileCategory.php
- psr-4
- Illuminate\Database\Eloquent\Model
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- Comment
- Illuminate\Database\Seeder
- NotificationEventType.php
- LoginRequest
- keywords
- ValidatesTaskAssignment.php
- buildEmojiPicker
- RichText
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- ProjectManagementController
- PerformanceSeeder
- PerformanceBaseline
- AppServiceProvider.php
- AuditEventNotifier
- code-highlight.js
- Document
- Illuminate\Http\Request
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- NotificationSetting
- ImportBatch.php
- CommentPolicy
- .__invoke
- Department.php
- UpdateTaskRequest
- BackfillTaskCompletedAtTest.php
- BackfillTaskCompletedAt
- UpdateRoleRequest
- UpdateTaskPriorityColorsRequest
- TaskIndexesTest.php
- UpdateProjectRequest
- MentionedInCommentNotification
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 338 edges
2. `Organization` - 209 edges
3. `OrgMember` - 171 edges
4. `Task` - 167 edges
5. `Role` - 111 edges
6. `Project` - 93 edges
7. `Department` - 85 edges
8. `Document` - 73 edges
9. `AuditLog` - 46 edges
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
- `assertEveryHelperAgreesForCurrentViewer()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/Permission.php

## Import Cycles
- None detected.

## Communities (211 total, 44 thin omitted)

### Community 0 - "Pest.php"
Cohesion: 0.09
Nodes (21): DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, makeMeasuredTasks(), measurePageGrowth(), runImportValidationAndCommit(), buildImportTestFile(), countQueries() (+13 more)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.15
Nodes (4): FlushAccessProfileCache, Collection, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.07
Nodes (20): TaskManagementController, Task, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\SoftDeletes, makeTaskForAnalytics(), makeKanbanCapTask(), expectedCompletedColumnOrder(), makeKanbanOrderTask() (+12 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.16
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

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
Nodes (9): OrgMember, makeClientForEditTest(), makeStaffForEditTest(), makeStaffForFolderTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest(), joinOrg(), makeStaffForPairsParity() (+1 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "Permission"
Cohesion: 0.10
Nodes (18): PermissionManagementController, Permission, up(), PermissionSeeder, revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), createOwnerForGrant() (+10 more)

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

### Community 87 - "User"
Cohesion: 0.05
Nodes (20): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, TaskPolicy (+12 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.07
Nodes (16): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, DashboardController, Collection, ImportController (+8 more)

### Community 90 - "TaskStatus"
Cohesion: 0.19
Nodes (5): TaskStatus, TaskColorController, UpdateTaskStatusColorsRequest, up(), referenceTaskListOrder()

### Community 91 - "TagsImportBatch.php"
Cohesion: 0.47
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "Organization"
Cohesion: 0.06
Nodes (55): AccessPermission, Department, Organization, Project, Role, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember() (+47 more)

### Community 108 - ".attach"
Cohesion: 0.19
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.18
Nodes (11): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), Illuminate\Support\Collection, flattenCalendarCells(), assertAttachableParity(), makeAttachableDocumentSet() (+3 more)

### Community 111 - "Illuminate\Console\Command"
Cohesion: 0.24
Nodes (3): CleanupStalePendingMedia, SeedPerformanceData, Illuminate\Console\Command

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "static"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 115 - "Closure"
Cohesion: 0.21
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 119 - "ProjectStatus.php"
Cohesion: 0.11
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 134 - "ImportBatch"
Cohesion: 0.17
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 139 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (5): GoogleAuthController, DepartmentManagementController, NotificationSettingsController, UserManagementController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "FileCategory.php"
Cohesion: 0.05
Nodes (31): config(), prefix(), FileStorageException, self, DocumentUploadService, FileCategory, FileStorageService, PastedMediaNamer (+23 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.07
Nodes (5): PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, expectedKanbanColumnOrder()

### Community 149 - "Subtask"
Cohesion: 0.21
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "Comment"
Cohesion: 0.18
Nodes (3): Comment, CommentObserver, addComment()

### Community 158 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 182 - "Document"
Cohesion: 0.10
Nodes (14): Document, DocumentPolicy, DocumentDependencyService, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest(), makeDocumentForIconTest(), makeClientForDocuments() (+6 more)

### Community 184 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (19): CommentReactionController, DocumentController, DocumentFolderController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController (+11 more)

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 192 - ".__invoke"
Cohesion: 0.62
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 199 - "Department.php"
Cohesion: 0.10
Nodes (3): CompanyRoleSyncer, Illuminate\Contracts\Validation\Validator, makeClientForDeleteTest()

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Pest.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `User.php`, `AccessPermission.php`, `Permission`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`, `Subtask`, `Comment`, `NotificationEventType.php`, `LoginRequest`, `RichText`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceBaseline`, `ImportTemplateBuilder`, `AuditEventNotifier`, `Document`, `Illuminate\Http\Request`, `NotificationSetting`, `CommentPolicy`, `Department.php`, `TaskManagementController.php`, `Illuminate\View\View`, `Organization`, `Illuminate\Support\Collection`, `Illuminate\Console\Command`, `LinkPreview`, `static`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ProjectStatus.php`, `Role.php`?**
  _High betweenness centrality (0.157) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `AccessProfileCache`, `Task`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `User.php`, `AccessPermission.php`, `Permission`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Validation\Validator`, `Illuminate\Database\Seeder`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `PerformanceSeeder`, `PerformanceBaseline`, `ImportTemplateBuilder`, `Document`, `Illuminate\Http\Request`, `.__invoke`, `Department.php`, `BackfillTaskCompletedAtTest.php`, `TaskManagementController.php`, `User`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `Illuminate\Console\Command`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Role.php`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Pest.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `ImportBatch`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `Permission`, `AccessPermission.php`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`, `Subtask`, `Comment`, `ValidatesTaskAssignment.php`, `RichText`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `Illuminate\Http\Request`, `CommentPolicy`, `.__invoke`, `Department.php`, `BackfillTaskCompletedAtTest.php`, `MentionedInCommentNotification`, `TaskManagementController.php`, `User`, `Illuminate\View\View`, `TaskObserver`, `Organization`, `.attach`, `Illuminate\Support\Collection`, `LinkPreview`, `static`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Role.php`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 22 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._