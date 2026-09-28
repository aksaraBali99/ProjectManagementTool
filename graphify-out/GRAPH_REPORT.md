# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 398 files · ~195,550 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1675 nodes · 4444 edges · 204 communities (160 shown, 44 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 286 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `984bf849`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- Department
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Model
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Http\Request
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
- DocumentAccessLevel.php
- User
- TaskManagementController
- AuditEventMailNotification.php
- Task.php
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- app.js
- config
- _form.blade.php
- Document
- Illuminate\View\View
- CodeLanguageClassSanitizer
- CalendarController.php
- ImportBatch
- LinkPreviewService.php
- Comment
- Organization
- Illuminate\Http\UploadedFile
- AuditLog
- FileCategory.php
- BootstrapEnvironment
- config
- Closure
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- createRichTextEditor
- require
- FileStorageService
- TagsImportBatch.php
- psr-4
- NotificationEventType.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- ResolvesCurrentOrganization.php
- UserManagementController
- UpdateUserRequest
- HasAdminConfigurableColors.php
- NotificationSetting
- ProjectManagementController.php
- buildEmojiPicker
- UpdateTaskRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Support\Collection
- Illuminate\Database\Seeder
- UpdateTaskStatusColorsRequest
- keywords
- PermissionManagementController
- HasEnumValues.php
- lightbox.js
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Http\JsonResponse
- resizable-video.js
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- UpdateDepartmentRequest
- static
- link-preview-extension.js
- CompanyRoleRules
- DocumentFolder
- CommentPolicy
- OrgMember
- LoginRequest
- UpdateTaskPriorityColorsRequest
- .resolvePending
- .storePending
- code-highlight.js
- CommentMentionHighlightTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 264 edges
2. `Organization` - 146 edges
3. `OrgMember` - 124 edges
4. `Task` - 110 edges
5. `Role` - 85 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `Document` - 50 edges
9. `ImportValidator` - 42 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `makeEligibleStaffMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php
- `makeIneligibleProjectMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/AccessPermission.php
- `makeProjectMember()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentMentionTest.php → app/Models/AccessPermission.php
- `grantDepartmentAccessForParity()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskViewableIdsParityTest.php → app/Models/AccessPermission.php
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php

## Import Cycles
- None detected.

## Communities (204 total, 44 thin omitted)

### Community 0 - "Task"
Cohesion: 0.13
Nodes (11): TaskDocumentController, Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Department"
Cohesion: 0.09
Nodes (17): AccessPermission, Department, DepartmentPolicy, makeTaskForAnalytics(), makeStaffOnCalendar(), makeReactionEligibleStaff(), makeEligibleStaff(), makeStaffOnDashboard() (+9 more)

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): CommentReaction, organization(), Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

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

### Community 11 - "Illuminate\Http\Request"
Cohesion: 0.19
Nodes (4): CommentReactionController, RichTextDocumentController, isAssignableStaffForProject(), Illuminate\Http\Request

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "StoreTaskRequest"
Cohesion: 0.15
Nodes (3): StoreDepartmentRequest, StoreTaskRequest, Illuminate\Contracts\Validation\Validator

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

### Community 87 - "User"
Cohesion: 0.05
Nodes (15): User, AuditLogPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, RolePolicy, TaskPolicy, UserPolicy (+7 more)

### Community 90 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "app.js"
Cohesion: 0.13
Nodes (13): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), checkFileChipStatuses() (+5 more)

### Community 105 - "config"
Cohesion: 0.29
Nodes (4): config(), prefix(), Attribute, Illuminate\Database\Eloquent\Casts\Attribute

### Community 107 - "Document"
Cohesion: 0.18
Nodes (5): DocumentController, Document, DocumentDependencyService, DocumentAccessLevel, Illuminate\Http\Response

### Community 108 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (11): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController, OrganizationManagementController (+3 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 111 - "ImportBatch"
Cohesion: 0.10
Nodes (11): AbandonStaleImportBatches, CleanupStalePendingMedia, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary (+3 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.15
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Organization"
Cohesion: 0.07
Nodes (38): Organization, Project, Role, makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeReactionClient(), makeClientForDeleteTest() (+30 more)

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.15
Nodes (14): Illuminate\Http\UploadedFile, fakeUploadDoc(), UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile (+6 more)

### Community 116 - "AuditLog"
Cohesion: 0.21
Nodes (3): AuditLog, AuditEventNotifier, NotificationEventType

### Community 118 - "FileCategory.php"
Cohesion: 0.17
Nodes (5): PastedMedia, PastedMediaNamer, fileFor(), FileCategory, UploadedFile

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Closure"
Cohesion: 0.15
Nodes (9): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.17
Nodes (4): AccessControlController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "FileStorageException"
Cohesion: 0.23
Nodes (5): FileStorageException, self, RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse, Throwable

### Community 139 - "createRichTextEditor"
Cohesion: 0.38
Nodes (11): buildAltTextEditButton(), buildAltTextPrompt(), buildAudioUpload(), buildDocumentUpload(), buildDropQueue(), buildToolbar(), createRichTextEditor(), el() (+3 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Subtask"
Cohesion: 0.19
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 157 - "ResolvesCurrentOrganization.php"
Cohesion: 0.19
Nodes (4): staffOptionsByProject(), resolveCurrentOrganization(), DepartmentManagementController, KanbanController

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 168 - "buildEmojiPicker"
Cohesion: 0.53
Nodes (6): availableEmojis(), buildEmojiPicker(), flagsRenderable(), readRecentEmojiNames(), rememberEmoji(), searchEmojis()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (7): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells(), assertDocumentViewParity(), makeDocumentSetForParity()

### Community 174 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 178 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "lightbox.js"
Cohesion: 0.67
Nodes (3): initLightboxDelegation(), closeLightbox(), openLightbox()

### Community 184 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.11
Nodes (5): Permission, up(), Illuminate\Database\Eloquent\Relations\BelongsToMany, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest()

### Community 185 - "Illuminate\Http\JsonResponse"
Cohesion: 0.19
Nodes (5): DocumentFolderController, RichTextAudioController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 189 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 195 - "OrgMember"
Cohesion: 0.15
Nodes (3): OrgMember, makeStaffForDocumentCreate(), makeStaffForNewMenuTest()

### Community 200 - "code-highlight.js"
Cohesion: 0.67
Nodes (3): highlightCodeBlocks(), lowlight, toDom()

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Department`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Http\Request`, `Role.php`, `NotificationEventType.php`, `Subtask`, `ResolvesCurrentOrganization.php`, `UserManagementController`, `NotificationSetting`, `Project.php`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `static`, `DocumentFolder`, `CommentPolicy`, `OrgMember`, `LoginRequest`, `DocumentAccessLevel.php`, `Task.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `Illuminate\View\View`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Organization`, `AuditLog`, `FileCategory.php`, `BootstrapEnvironment`, `Closure`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Department`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `Role.php`, `StoreTaskRequest`, `ResolvesCurrentOrganization.php`, `UserManagementController`, `Project.php`, `ProjectManagementController.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Seeder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CompanyRoleRules`, `OrgMember`, `User`, `Task.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `Illuminate\View\View`, `CalendarController.php`, `ImportBatch`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Department`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `Role.php`, `Subtask`, `ResolvesCurrentOrganization.php`, `Project.php`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Http\JsonResponse`, `static`, `OrgMember`, `.resolvePending`, `.storePending`, `DocumentAccessLevel.php`, `User`, `TaskManagementController`, `Task.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Document`, `Illuminate\View\View`, `CalendarController.php`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Organization`, `FileCategory.php`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 51 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 51 INFERRED edges - model-reasoned connections that need verification._