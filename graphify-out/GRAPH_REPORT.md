# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 353 files · ~144,663 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1490 nodes · 3653 edges · 183 communities (142 shown, 41 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 230 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c510b990`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Project
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Builder
- Pest.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- RichText
- LARAVEL_README.md
- AppServiceProvider.php
- Role.php
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
- StoreTaskRequest
- User
- AuditLog
- App\Models\Task
- UpdateProjectRequest
- Organization
- setup
- documents/create.blade.php
- App\Enums\FileCategory
- Illuminate\Http\Request
- _form.blade.php
- BootstrapEnvironment
- web.php
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- LinkPreviewService.php
- DepartmentManagementController.php
- Illuminate\Http\JsonResponse
- App\Http\Controllers\Concerns\ResolvesCurrentOrganization
- ProjectManagementController.php
- ValidatesTaskAssignment.php
- Illuminate\View\View
- config
- static
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- LoginRequest
- App\Models\Document
- require
- OrgMember
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- CommentPolicy
- psr-4
- TaskObserver
- FileStorageException
- Illuminate\Validation\Validator
- UserManagementController
- OrganizationManagementController.php
- Task
- DocumentController
- TaskManagementController.php
- UpdateTaskStatusColorsRequest
- CommentController.php
- StoreProjectRequest
- UserPolicy
- UpdateRoleRequest
- RolePolicy
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- StoredFile
- Illuminate\Foundation\Http\FormRequest
- Subtask
- NotificationSettingPolicy
- keywords
- AuditLogPolicy.php
- Document

## God Nodes (most connected - your core abstractions)
1. `User` - 197 edges
2. `Organization` - 105 edges
3. `Task` - 93 edges
4. `OrgMember` - 78 edges
5. `Project` - 66 edges
6. `Role` - 52 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeClientWithProjectAccessForDownload()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentDownloadTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php

## Import Cycles
- None detected.

## Communities (183 total, 41 thin omitted)

### Community 0 - "Project"
Cohesion: 0.09
Nodes (25): AccessPermission, Department, Project, Role, UserSeeder, makeTaskForAnalytics(), makeStaffOnCalendar(), makeProjectMember() (+17 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (6): DuplicateDetector, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (7): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "Pest.php"
Cohesion: 0.13
Nodes (13): DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase, buildImportTestFile(), createOwner(), richTextEditorContent(), richTextEditorNode() (+5 more)

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

### Community 11 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

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
Cohesion: 0.06
Nodes (46): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+38 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.17
Nodes (8): ImportController, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "StoreTaskRequest"
Cohesion: 0.10
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 87 - "User"
Cohesion: 0.09
Nodes (9): User, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask() (+1 more)

### Community 88 - "AuditLog"
Cohesion: 0.05
Nodes (16): AuditLog, Comment, organization(), ImportRow, AuditEventDatabaseNotification, AuditEventMailNotification, CommentObserver, AuditEventNotifier (+8 more)

### Community 90 - "App\Models\Task"
Cohesion: 0.07
Nodes (5): App\Models\Project, App\Models\Task, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 91 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 92 - "Organization"
Cohesion: 0.09
Nodes (3): OrganizationManagementController, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "App\Enums\FileCategory"
Cohesion: 0.11
Nodes (17): App\Enums\FileCategory, config(), prefix(), FileStorageService, Illuminate\Http\UploadedFile, StoredFile, fileFor(), FileCategory (+9 more)

### Community 105 - "Illuminate\Http\Request"
Cohesion: 0.14
Nodes (10): DashboardController, Collection, RichTextDocumentController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Illuminate\Support\Collection (+2 more)

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.10
Nodes (5): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, EmployeeIdGenerator, Illuminate\Console\Command

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.21
Nodes (5): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.12
Nodes (5): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService, UrlSsrfGuard

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.21
Nodes (6): RichTextAudioController, RichTextImageController, RichTextVideoController, TaskDocumentController, Closure, Illuminate\Http\JsonResponse

### Community 115 - "App\Http\Controllers\Concerns\ResolvesCurrentOrganization"
Cohesion: 0.25
Nodes (6): CalendarController, App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), KanbanController, Carbon, Illuminate\Support\Carbon

### Community 119 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (9): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, NotificationController, PermissionManagementController, RoleManagementController, SettingsController (+1 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (7): AccessControlController, GoogleAuthController, NotificationSettingsController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "App\Models\Document"
Cohesion: 0.38
Nodes (3): App\Models\Document, Document, uploadDocumentForDownload()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "OrgMember"
Cohesion: 0.10
Nodes (7): App\Models\Organization, OrgMember, App\Models\User, CompanyRoleSyncer, makeStaffForDocumentCreate(), makeClientWithProjectAccessForDownload(), User

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "TaskObserver"
Cohesion: 0.27
Nodes (4): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), TaskObserver

### Community 149 - "FileStorageException"
Cohesion: 0.32
Nodes (3): FileStorageException, self, Throwable

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 157 - "Task"
Cohesion: 0.11
Nodes (9): TaskManagementController, Task, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), taskUpdatePayload() (+1 more)

### Community 158 - "DocumentController"
Cohesion: 0.33
Nodes (3): DocumentController, Organization, Controller

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): UploadImportRequest, UpdateTaskPriorityColorsRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.19
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 184 - "Document"
Cohesion: 0.22
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

## Knowledge Gaps
- **133 isolated node(s):** `CHART_PALETTE`, `richTextMounts`, `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Project`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `LoginRequest`, `Pest.php`, `RichText`, `OrgMember`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `UserManagementController`, `Task`, `UserPolicy`, `RolePolicy`, `Subtask`, `ImportTemplateBuilder`, `NotificationSettingPolicy`, `AuditLogPolicy.php`, `Document`, `AuditLog`, `App\Models\Task`, `UpdateProjectRequest`, `Organization`, `Illuminate\Http\Request`, `BootstrapEnvironment`, `ImportBatch`, `LinkPreviewService.php`, `ProjectManagementController.php`, `ValidatesTaskAssignment.php`, `Illuminate\View\View`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Project`, `ImportValidator`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `RichText`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskObserver`, `DocumentController`, `TaskManagementController.php`, `CommentController.php`, `Subtask`, `AuditLog`, `App\Models\Task`, `Illuminate\Http\Request`, `web.php`, `ImportBatch`, `LinkPreviewService.php`, `Illuminate\Http\JsonResponse`, `App\Http\Controllers\Concerns\ResolvesCurrentOrganization`, `ValidatesTaskAssignment.php`, `Illuminate\View\View`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Project`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `OrganizationManagementController.php`, `Task`, `TaskManagementController.php`, `ImportTemplateBuilder`, `Document`, `StoreTaskRequest`, `User`, `App\Models\Task`, `Illuminate\Http\Request`, `ImportBatch`, `DepartmentManagementController.php`, `App\Http\Controllers\Concerns\ResolvesCurrentOrganization`, `ProjectManagementController.php`, `Illuminate\View\View`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `CHART_PALETTE`, `richTextMounts`, `Diagram editing & preview` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._