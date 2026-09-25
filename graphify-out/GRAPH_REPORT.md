# Graph Report - ProjectManagementTool  (2026-09-25)

## Corpus Check
- 361 files · ~155,553 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1521 nodes · 3775 edges · 184 communities (142 shown, 42 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 232 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `a0391280`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- DocumentController
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\HasMany
- Document
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Http\UploadedFile
- LARAVEL_README.md
- AppServiceProvider.php
- App\Models\Role
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
- RichText
- User
- AuditLog
- TaskManagementController
- Priority.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- setup
- documents/create.blade.php
- Illuminate\Support\Collection
- static
- _form.blade.php
- CommentPolicy
- Illuminate\View\View
- CodeLanguageClassSanitizer
- LoginRequest
- ImportBatch
- TaskManagementController.php
- StoreTaskRequest
- Illuminate\Http\JsonResponse
- BootstrapEnvironment
- UserManagementController
- FileCategory.php
- NotificationSetting.php
- config
- Illuminate\Database\Eloquent\Model
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Builder
- Illuminate\Http\Request
- require
- User.php
- StoreProjectRequest
- Comment
- psr-4
- Task
- UpdateTaskPriorityColorsRequest
- Illuminate\Validation\Validator
- Organization
- HasAdminConfigurableColors.php
- UpdateProjectRequest
- NotificationSettingPolicy
- LinkPreviewController.php
- AuditEventNotifier
- StoreDepartmentRequest
- Subtask
- OrganizationPolicy
- SubtaskPolicy
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateTaskStatusColorsRequest
- Illuminate\Foundation\Http\FormRequest
- UpdateTaskRequest
- CalendarController.php
- RolePolicy
- Collection
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 203 edges
2. `Organization` - 103 edges
3. `Task` - 99 edges
4. `OrgMember` - 84 edges
5. `Project` - 68 edges
6. `Role` - 52 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `makeStaffOnCalendar()` --references--> `User`  [EXTRACTED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/User.php
- `makeProjectMember()` --references--> `User`  [EXTRACTED]
  tests/Feature/Comments/CommentMentionTest.php → app/Models/User.php

## Import Cycles
- None detected.

## Communities (184 total, 42 thin omitted)

### Community 0 - "DocumentController"
Cohesion: 0.47
Nodes (3): DocumentController, Controller, Organization

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 2 - ".boardOrganizationIds"
Cohesion: 0.25
Nodes (3): App\Enums\BoardAccessDeniedReason, BoardAccessDeniedReason, Collection

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "Document"
Cohesion: 0.24
Nodes (4): TaskDocumentController, Document, uploadDocumentForDownload(), makeDocumentForList()

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

### Community 11 - "Illuminate\Http\UploadedFile"
Cohesion: 0.06
Nodes (30): FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase (+22 more)

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
Nodes (5): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._description-field, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.05
Nodes (54): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+46 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.08
Nodes (11): User, AuditLogPolicy, DepartmentPolicy, DocumentPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+3 more)

### Community 88 - "AuditLog"
Cohesion: 0.18
Nodes (3): AuditLog, NotificationSettingsResolver, NotificationEventType

### Community 92 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (4): organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Illuminate\Support\Collection"
Cohesion: 0.21
Nodes (7): App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 105 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 108 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (13): AccessControlController, AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, KanbanController, NotificationController (+5 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "TaskManagementController.php"
Cohesion: 0.12
Nodes (3): OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.20
Nodes (5): RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 115 - "BootstrapEnvironment"
Cohesion: 0.10
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Illuminate\Console\Command

### Community 118 - "FileCategory.php"
Cohesion: 0.23
Nodes (6): PastedMediaNamer, Closure, RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 119 - "NotificationSetting.php"
Cohesion: 0.16
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.21
Nodes (4): App\Models\Document, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (5): DepartmentManagementController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (8): AuditTrailController, RichTextDocumentController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\StreamedResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "Comment"
Cohesion: 0.29
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.10
Nodes (11): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload() (+3 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "Organization"
Cohesion: 0.07
Nodes (32): RoleManagementController, AccessPermission, Department, Organization, OrgMember, Project, Role, CompanyRoleSyncer (+24 more)

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "UpdateProjectRequest"
Cohesion: 0.15
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 159 - "LinkPreviewController.php"
Cohesion: 0.17
Nodes (4): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 178 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 185 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DocumentController`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`, `Document`, `Illuminate\Http\Request`, `Illuminate\Http\UploadedFile`, `User.php`, `App\Models\Role`, `Task`, `Organization`, `UpdateProjectRequest`, `NotificationSettingPolicy`, `LinkPreviewController.php`, `AuditEventNotifier`, `OrganizationPolicy`, `SubtaskPolicy`, `ImportTemplateBuilder`, `RolePolicy`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `AuditLog`, `TaskManagementController`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `CommentPolicy`, `Illuminate\View\View`, `LoginRequest`, `ImportBatch`, `TaskManagementController.php`, `BootstrapEnvironment`, `UserManagementController`?**
  _High betweenness centrality (0.119) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\RedirectResponse`, `Document`, `Illuminate\Http\Request`, `User.php`, `App\Models\Role`, `Illuminate\Validation\Validator`, `StoreDepartmentRequest`, `OrganizationPolicy`, `CalendarController.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `Priority.php`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `ImportBatch`, `TaskManagementController.php`, `UserManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Http\RedirectResponse`, `Document`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\Request`, `App\Models\Role`, `Organization`, `LinkPreviewController.php`, `SubtaskPolicy`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `TaskManagementController`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `ImportBatch`, `TaskManagementController.php`, `Illuminate\Http\JsonResponse`, `FileCategory.php`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 16 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._