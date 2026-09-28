# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 394 files · ~189,965 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1665 nodes · 4401 edges · 189 communities (151 shown, 38 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 285 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `8e5c8a46`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Task
- ImportValidator
- .boardOrganizationIds
- PermissionManagementController
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
- RichText
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
- BootstrapEnvironment
- User
- TaskManagementController
- NotificationEventType.php
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- ProjectPolicy
- _form.blade.php
- AuditLog
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreview
- Comment
- Illuminate\Http\Request
- FileCategory.php
- Document
- DocumentAccessLevel.php
- LoginRequest
- config
- CommentPolicy
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- FileStorageException
- NotificationSetting
- require
- Role.php
- RolePolicy
- AuditEventNotifier
- psr-4
- ImportBatch.php
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- DocumentFolder
- TaskColorController.php
- UserManagementController.php
- HasAdminConfigurableColors.php
- DocumentFolderController
- DepartmentPolicy
- SubtaskPolicy
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- ProjectStatus.php
- PermissionSeeder.php
- StoreProjectRequest
- NotificationSettingPolicy
- .storePending
- .storePending
- post-create-project-cmd
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 262 edges
2. `Organization` - 144 edges
3. `OrgMember` - 120 edges
4. `Task` - 110 edges
5. `Role` - 84 edges
6. `Project` - 77 edges
7. `Department` - 62 edges
8. `Document` - 49 edges
9. `ImportValidator` - 42 edges
10. `AuditLog` - 40 edges

## Surprising Connections (you probably didn't know these)
- `makeDocumentSetForParity()` --calls--> `Document`  [INFERRED]
  tests/Feature/Documents/DocumentViewableIdsParityTest.php → app/Models/Document.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `grantManageDocuments()` --calls--> `Permission`  [INFERRED]
  tests/Feature/Documents/DocumentFolderTest.php → app/Models/Permission.php
- `up()` --calls--> `Role`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Role.php
- `makeClientWithProjectAccessForAudio()` --calls--> `Role`  [INFERRED]
  tests/Feature/RichText/AudioUploadTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (189 total, 38 thin omitted)

### Community 0 - "Task"
Cohesion: 0.09
Nodes (12): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+4 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.09
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Task.php"
Cohesion: 0.10
Nodes (5): Permission, up(), Illuminate\Database\Eloquent\Model, grantManageDocumentsForDeleteTest(), grantManageDocumentsForEditTest()

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

### Community 11 - "Closure"
Cohesion: 0.25
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, bootHidesInactiveFromNonAdmins(), Closure, Symfony\Component\HttpFoundation\Response

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "RichText"
Cohesion: 0.09
Nodes (5): StoreTaskRequest, UpdateTaskRequest, RichText, Illuminate\Contracts\Validation\Validator, Symfony\Component\HtmlSanitizer\HtmlSanitizer

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
Cohesion: 0.05
Nodes (57): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+49 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 87 - "User"
Cohesion: 0.08
Nodes (11): isAssignableStaffForProject(), User, AuditLogPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+3 more)

### Community 88 - "TaskManagementController"
Cohesion: 0.18
Nodes (3): config(), prefix(), TaskManagementController

### Community 92 - "Organization"
Cohesion: 0.07
Nodes (49): AccessPermission, Department, Organization, OrgMember, Project, Role, UserSeeder, makeTaskForAnalytics() (+41 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 107 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 108 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, ImportController, NotificationController (+4 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.07
Nodes (18): CalendarController, staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, ProjectManagementController, UpdateProjectRequest (+10 more)

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (10): CommentReactionController, DocumentController, LinkPreviewController, RichTextAudioController, RichTextDocumentController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse (+2 more)

### Community 115 - "FileCategory.php"
Cohesion: 0.08
Nodes (22): PastedMedia, FileStorageService, PastedMediaNamer, StoredFile, Illuminate\Http\UploadedFile, RuntimeException, fakeUploadDoc(), UploadedFile (+14 more)

### Community 116 - "Document"
Cohesion: 0.13
Nodes (10): Document, DocumentPolicy, DocumentDependencyService, Attribute, Illuminate\Database\Eloquent\Casts\Attribute, makeLinkOnlyDocumentForDeleteTest(), uploadDocumentForDownload(), makeDocumentForEditTest() (+2 more)

### Community 118 - "DocumentAccessLevel.php"
Cohesion: 0.23
Nodes (3): DocumentUploadService, up(), DocumentAccessLevel

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.17
Nodes (3): AccessControlController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "FileStorageException"
Cohesion: 0.26
Nodes (3): FileStorageException, self, Throwable

### Community 139 - "NotificationSetting"
Cohesion: 0.22
Nodes (3): NotificationSettingsController, NotificationSetting, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Role.php"
Cohesion: 0.09
Nodes (3): Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement

### Community 145 - "AuditEventNotifier"
Cohesion: 0.17
Nodes (6): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier, NotificationEventType

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "ImportBatch.php"
Cohesion: 0.24
Nodes (3): AbandonStaleImportBatches, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.10
Nodes (7): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.10
Nodes (6): StoreDepartmentRequest, UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "TaskColorController.php"
Cohesion: 0.20
Nodes (3): UpdateTaskStatusColorsRequest, TaskPriorityColor, TaskStatusColor

### Community 159 - "UserManagementController.php"
Cohesion: 0.14
Nodes (3): UserManagementController, UpdateUserPasswordRequest, UpdateUserRequest

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "ProjectStatus.php"
Cohesion: 0.19
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 174 - "PermissionSeeder.php"
Cohesion: 0.11
Nodes (9): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder, makeClientWithProjectAccessForAudio(), makeClientWithProjectAccessForDocumentUpload() (+1 more)

### Community 182 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Task`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `NotificationSetting`, `Role.php`, `RolePolicy`, `AuditEventNotifier`, `DocumentFolder`, `UserManagementController.php`, `DepartmentPolicy`, `SubtaskPolicy`, `ProjectStatus.php`, `PermissionSeeder.php`, `NotificationSettingPolicy`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `BootstrapEnvironment`, `TaskManagementController`, `NotificationEventType.php`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ProjectPolicy`, `AuditLog`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Http\Request`, `Document`, `DocumentAccessLevel.php`, `LoginRequest`, `CommentPolicy`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role.php`, `RichText`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController.php`, `PermissionSeeder.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `TaskManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Http\Request`, `Document`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role.php`, `AuditEventNotifier`, `Subtask`, `SubtaskPolicy`, `PermissionSeeder.php`, `.storePending`, `.storePending`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `TaskManagementController`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreview`, `Comment`, `Illuminate\Http\Request`, `FileCategory.php`, `Document`, `DocumentAccessLevel.php`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 12 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 12 INFERRED edges - model-reasoned connections that need verification._
- **Are the 50 inferred relationships involving `Role` (e.g. with `.createOwner()` and `.toggle()`) actually correct?**
  _`Role` has 50 INFERRED edges - model-reasoned connections that need verification._