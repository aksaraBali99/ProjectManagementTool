# Graph Report - ProjectManagementTool  (2026-10-12)

## Corpus Check
- 444 files · ~254,754 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1894 nodes · 5375 edges · 196 communities (162 shown, 34 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 355 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `010398f4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Department.php
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
- FileCategory.php
- User
- Illuminate\View\View
- AccessProfile
- Comment
- setup
- documents/create.blade.php
- app.js
- _form.blade.php
- Organization
- .attach
- CodeLanguageClassSanitizer
- static
- ImportBatch
- LinkPreviewController.php
- DocumentFolder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Closure
- HasAdminConfigurableColors.php
- OrgMember
- ProjectManagementController
- config
- Project
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- CalendarController.php
- CommentPolicy
- require
- User.php
- UpdateUserRequest
- AppServiceProvider.php
- psr-4
- NotificationSetting
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- DocumentDependencyService.php
- AccessProfileParityTest.php
- Illuminate\Database\Eloquent\Model
- TaskManagementController
- keywords
- LoginRequest
- buildEmojiPicker
- post-create-project-cmd
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Document
- PerformanceSeeder
- UpdateTaskStatusColorsRequest
- AuditEventNotifier
- UserManagementController
- code-highlight.js
- web.php
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- UpdateTaskPriorityColorsRequest
- StoreTaskRequest
- UpdateTaskRequest
- CompanyRoleRules

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
- `grantDept()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Permissions/AccessProfileParityTest.php → app/Models/AccessPermission.php
- `makeStaffWhoCanCreateTasksForLinkPreview()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/RichText/LinkPreviewTest.php → app/Models/AccessPermission.php
- `makeStaffForDetach()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskDocumentDetachOwnershipTest.php → app/Models/AccessPermission.php
- `makeStaffWithDeptAccessForFolderTest()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskFolderAttachTest.php → app/Models/AccessPermission.php
- `makePreviewViewer()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskPreviewTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (196 total, 34 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.08
Nodes (6): BootstrapEnvironment, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - "AccessProfileCache"
Cohesion: 0.14
Nodes (5): FlushAccessProfileCache, Collection, CompanyRoleSyncer, AccessProfileCache, BoardAccessDeniedReason

### Community 3 - "Task"
Cohesion: 0.09
Nodes (13): RichTextDocumentController, Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate() (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (6): CommentReaction, bootBelongsToOrganization(), organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "AuditLog"
Cohesion: 0.13
Nodes (8): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification, assertTaskDocumentAttachedAuditEntry()

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
Cohesion: 0.10
Nodes (27): PermissionManagementController, Permission, Role, up(), revokeCommentPermissionFromStaff(), grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest(), createOwnerForGrant() (+19 more)

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "StoreProjectRequest"
Cohesion: 0.15
Nodes (5): StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

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
Cohesion: 0.12
Nodes (13): eligibleAssigneesByProjectAndDepartment(), eligibleAssigneesByTask(), eligibleAssigneesForPairs(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, UpdateProjectRequest (+5 more)

### Community 86 - "FileCategory.php"
Cohesion: 0.05
Nodes (32): config(), prefix(), FileStorageException, self, DocumentUploadService, FileCategory, FileStorageService, PastedMediaNamer (+24 more)

### Community 87 - "User"
Cohesion: 0.05
Nodes (16): User, AuditLogPolicy, DepartmentPolicy, DocumentFolderPolicy, DocumentPolicy, OrganizationPolicy, RolePolicy, UserPolicy (+8 more)

### Community 88 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController (+4 more)

### Community 90 - "AccessProfile"
Cohesion: 0.16
Nodes (3): AccessProfile, Collection, self

### Community 91 - "Comment"
Cohesion: 0.12
Nodes (5): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer, addComment()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.11
Nodes (16): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+8 more)

### Community 107 - "Organization"
Cohesion: 0.07
Nodes (38): AccessPermission, Department, Organization, makeTaskForAnalytics(), makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember() (+30 more)

### Community 108 - ".attach"
Cohesion: 0.19
Nodes (7): DocumentAlreadyAttachedException, self, FolderAlreadyLinkedException, self, TaskDocumentLinker, Illuminate\Database\QueryException, RuntimeException

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "static"
Cohesion: 0.22
Nodes (5): bootFlushesAccessProfiles(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 111 - "ImportBatch"
Cohesion: 0.18
Nodes (6): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver

### Community 112 - "LinkPreviewController.php"
Cohesion: 0.09
Nodes (7): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "DocumentFolder"
Cohesion: 0.19
Nodes (7): DocumentFolderController, TaskFolderController, DocumentFolder, makeLinkerFolderTestFolder(), makeAttachTestFolder(), makeDisplayTestFolder(), makePickerFolder()

### Community 115 - "Closure"
Cohesion: 0.21
Nodes (5): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 116 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 118 - "OrgMember"
Cohesion: 0.13
Nodes (11): OrgMember, makeStaffForDocumentCreate(), makeClientForDeleteTest(), makeClientForEditTest(), makeStaffForEditTest(), makeStaffForFolderTest(), makeStaffForNewMenuTest(), makeStaffForUploadTest() (+3 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Project"
Cohesion: 0.07
Nodes (20): isAssignableStaffForProject(), Project, ProjectPolicy, Illuminate\Contracts\Validation\Validator, makeReactionClient(), makeClientWithProjectAccessForDownload(), makeClientForDocumentList(), makeDocumentForList() (+12 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (5): DepartmentManagementController, NotificationSettingsController, TaskColorController, StoreDepartmentRequest, Illuminate\Http\RedirectResponse

### Community 134 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "NotificationSetting"
Cohesion: 0.14
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 149 - "Subtask"
Cohesion: 0.19
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.16
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "AccessProfileParityTest.php"
Cohesion: 0.47
Nodes (5): addOrgRole(), assertEveryHelperAgrees(), assertEveryHelperAgreesForCurrentViewer(), bothWays(), grantDept()

### Community 159 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.12
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 165 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 169 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Document"
Cohesion: 0.17
Nodes (8): DocumentController, TaskDocumentController, Document, DocumentAccessLevel, Illuminate\Http\Request, Illuminate\Http\Response, uploadDocumentForDownload(), makeDocumentForIconTest()

### Community 174 - "PerformanceSeeder"
Cohesion: 0.05
Nodes (13): AbandonStaleImportBatches, CleanupStalePendingMedia, PerformanceBaseline, SeedPerformanceData, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PerformanceSeeder (+5 more)

### Community 178 - "AuditEventNotifier"
Cohesion: 0.17
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 181 - "code-highlight.js"
Cohesion: 0.33
Nodes (5): highlightCodeBlocks(), lowlight, toDom(), chipLabel(), enhanceLinkPreviews()

### Community 184 - "Illuminate\Http\JsonResponse"
Cohesion: 0.16
Nodes (6): CommentReactionController, RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 186 - "resizable-video.js"
Cohesion: 0.33
Nodes (4): chipLabel(), LinkPreview, ResizableVideo, Video

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

## Knowledge Gaps
- **131 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+126 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **34 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Department.php`, `ImportValidator`, `AccessProfileCache`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Role`, `CommentPolicy`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `StoreProjectRequest`, `NotificationSetting`, `Subtask`, `DocumentDependencyService.php`, `AccessProfileParityTest.php`, `TaskManagementController`, `LoginRequest`, `Document`, `PerformanceSeeder`, `AuditEventNotifier`, `Pest.php`, `UserManagementController`, `Illuminate\Support\Collection`, `web.php`, `FileCategory.php`, `Illuminate\View\View`, `Comment`, `Task.php`, `Role.php`, `Organization`, `static`, `ImportBatch`, `LinkPreviewController.php`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `ProjectManagementController`, `Project`?**
  _High betweenness centrality (0.155) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Department.php`, `ImportValidator`, `AccessProfileCache`, `Illuminate\Http\RedirectResponse`, `CalendarController.php`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `AccessProfileParityTest.php`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `Document`, `PerformanceSeeder`, `Pest.php`, `UserManagementController`, `Illuminate\Support\Collection`, `web.php`, `CompanyRoleRules`, `User`, `Illuminate\View\View`, `Task.php`, `Role.php`, `ImportBatch`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `ProjectManagementController`, `Project`?**
  _High betweenness centrality (0.083) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Department.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `CalendarController.php`, `AuditLog`, `CommentPolicy`, `Role`, `User.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `DocumentDependencyService.php`, `Illuminate\Database\Eloquent\Model`, `TaskManagementController`, `Document`, `Pest.php`, `Illuminate\Support\Collection`, `web.php`, `Illuminate\Http\JsonResponse`, `FileCategory.php`, `User`, `Illuminate\View\View`, `Comment`, `Task.php`, `Role.php`, `Organization`, `.attach`, `static`, `ImportBatch`, `LinkPreviewController.php`, `DocumentFolder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Closure`, `Project`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Are the 19 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 19 INFERRED edges - model-reasoned connections that need verification._
- **Are the 68 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 68 INFERRED edges - model-reasoned connections that need verification._