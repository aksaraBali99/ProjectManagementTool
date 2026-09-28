# Graph Report - ProjectManagementTool  (2026-09-28)

## Corpus Check
- 383 files · ~179,081 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1614 nodes · 4119 edges · 192 communities (152 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 256 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7105d4e9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- web.php
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- App\Models\Role
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- auth.php
- LARAVEL_README.md
- AppServiceProvider.php
- TaskManagementController.php
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
- StoreDepartmentRequest
- User
- TaskManagementController
- Illuminate\Database\Eloquent\Model
- Document
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- FileStorageException
- _form.blade.php
- AuditEventMailNotification.php
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- Illuminate\Http\Request
- App\Enums\FileCategory
- fakePastedImage
- AuditLog
- LoginRequest
- config
- CommentPolicy
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- NotificationEventType.php
- NotificationSetting
- require
- App\Models\Organization
- .view
- AuditEventNotifier
- psr-4
- Task
- Subtask
- Illuminate\Validation\Validator
- Illuminate\Foundation\Http\FormRequest
- StoreProjectRequest
- static
- UserManagementController
- HasAdminConfigurableColors.php
- TaskObserver
- App\Models\Task
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateProjectRequest
- ProjectPolicy
- Permission
- OpenGraphMetadataParser
- UpdateTaskPriorityColorsRequest
- config
- .storePending
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UpdateTaskStatusColorsRequest
- OrgMember
- 2026_09_28_000000_add_upload_columns_to_documents_table.php
- keywords
- fakeUploadDoc
- fakeImage
- fakeVideo

## God Nodes (most connected - your core abstractions)
1. `User` - 228 edges
2. `Organization` - 111 edges
3. `OrgMember` - 104 edges
4. `Task` - 88 edges
5. `Project` - 66 edges
6. `Department` - 60 edges
7. `Role` - 59 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 35 edges
10. `Document` - 32 edges

## Surprising Connections (you probably didn't know these)
- `up()` --calls--> `Permission`  [INFERRED]
  database/migrations/2026_09_28_000001_remove_client_view_documents_permission.php → app/Models/Permission.php
- `makeStaffForDocumentList()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/OrgMember.php
- `makeClientForDocumentList()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/OrgMember.php
- `makeStaffForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php
- `makeClientForUploadTest()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentUploadFromLocalTest.php → app/Models/OrgMember.php

## Import Cycles
- None detected.

## Communities (192 total, 40 thin omitted)

### Community 0 - "web.php"
Cohesion: 0.12
Nodes (7): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, PastedMediaNamer, StoredFile, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 1 - "ImportValidator"
Cohesion: 0.14
Nodes (3): DuplicateDetector, ImportValidationContext, ImportValidator

### Community 2 - ".boardOrganizationIds"
Cohesion: 0.24
Nodes (3): App\Enums\BoardAccessDeniedReason, Collection, BoardAccessDeniedReason

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (4): CommentReaction, organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "TaskManagementController.php"
Cohesion: 0.13
Nodes (7): App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment, isAssignableStaffForProject(), App\Http\Requests\Tasks\StoreTaskRequest, StoreTaskRequest, App\Http\Requests\Tasks\UpdateTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

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
Cohesion: 0.05
Nodes (14): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, RolePolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Builder (+6 more)

### Community 88 - "TaskManagementController"
Cohesion: 0.31
Nodes (4): Organization, Project, Task, TaskManagementController

### Community 90 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.19
Nodes (5): App\Models\Concerns\BelongsToOrganization, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 91 - "Document"
Cohesion: 0.16
Nodes (9): App\Enums\DocumentAccessLevel, Document, DocumentUploadService, StoredFile, Attribute, up(), DocumentAccessLevel, Illuminate\Database\Eloquent\Casts\Attribute (+1 more)

### Community 92 - "Organization"
Cohesion: 0.08
Nodes (35): AnalyticsController, DepartmentManagementController, AccessPermission, Department, Organization, Project, Role, DepartmentSeeder (+27 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "FileStorageException"
Cohesion: 0.23
Nodes (4): FileStorageException, self, RuntimeException, Throwable

### Community 107 - "AuditEventMailNotification.php"
Cohesion: 0.20
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 108 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (5): NotificationController, ProjectManagementController, RoleManagementController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.16
Nodes (12): CalendarController, App\Http\Controllers\Concerns\BuildsAssigneeOptions, staffOptionsByProject(), App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, KanbanController (+4 more)

### Community 111 - "ImportBatch"
Cohesion: 0.07
Nodes (15): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, ImportController, ImportBatch, EmployeeIdGenerator, ImportCommitResolution, ImportCommitService (+7 more)

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.14
Nodes (4): LinkPreview, LinkPreviewResult, LinkPreviewService, UrlSsrfGuard

### Community 113 - "Comment"
Cohesion: 0.12
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.12
Nodes (11): AuditTrailController, CommentReactionController, Controller, LinkPreviewController, RichTextAudioController, Task, RichTextDocumentController, RichTextImageController (+3 more)

### Community 115 - "App\Enums\FileCategory"
Cohesion: 0.16
Nodes (11): App\Enums\FileCategory, FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fileFor(), FileCategory, UploadedFile, fakeAudio() (+3 more)

### Community 116 - "fakePastedImage"
Cohesion: 0.67
Nodes (3): fakePastedImage(), fakePastedVideo(), UploadedFile

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (7): AccessControlController, AuthenticatedSessionController, GoogleAuthController, NotificationSettingsController, OrganizationManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 139 - "NotificationSetting"
Cohesion: 0.23
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\Organization"
Cohesion: 0.07
Nodes (7): App\Models\Organization, App\Models\Project, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForUploadTest(), makeStaffForUploadTest()

### Community 143 - ".view"
Cohesion: 0.24
Nodes (4): DocumentController, Organization, DocumentPolicy, Controller

### Community 145 - "AuditEventNotifier"
Cohesion: 0.22
Nodes (5): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), AuditEventNotifier

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.14
Nodes (10): TaskDocumentController, Task, MentionedInCommentNotification, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 149 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 158 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 160 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 166 - "App\Models\Task"
Cohesion: 0.15
Nodes (6): App\Models\Task, Illuminate\Support\Facades\Notification, findCommentCardBody(), DOMElement, findKanbanCard(), DOMElement

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 177 - "Permission"
Cohesion: 0.29
Nodes (4): PermissionManagementController, Permission, up(), Role

### Community 186 - "OrgMember"
Cohesion: 0.21
Nodes (4): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), joinOrg()

### Community 187 - "2026_09_28_000000_add_upload_columns_to_documents_table.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 188 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `users._unsaved-changes-guard`, `users._unsaved-changes-guard`, `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `web.php`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `App\Models\Role`, `Illuminate\Http\RedirectResponse`, `NotificationEventType.php`, `NotificationSetting`, `App\Models\Organization`, `TaskManagementController.php`, `.view`, `Subtask`, `UserManagementController`, `Priority.php`, `App\Models\Task`, `UpdateUserRequest`, `ProjectPolicy`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `TaskManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\Request`, `AuditLog`, `LoginRequest`, `CommentPolicy`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `web.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `App\Models\Role`, `AuditEventNotifier`, `Subtask`, `Priority.php`, `TaskObserver`, `App\Models\Task`, `.storePending`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `Illuminate\Database\Eloquent\Model`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `App\Models\Role`, `App\Models\Organization`, `TaskManagementController.php`, `Illuminate\Validation\Validator`, `Illuminate\Foundation\Http\FormRequest`, `UserManagementController`, `Priority.php`, `App\Models\Task`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `OrgMember`, `User`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 16 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `OrgMember` (e.g. with `makeClientForDocumentList()` and `makeStaffForDocumentList()`) actually correct?**
  _`OrgMember` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 9 INFERRED edges - model-reasoned connections that need verification._