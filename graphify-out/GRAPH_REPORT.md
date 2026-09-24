# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 357 files · ~149,288 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1522 nodes · 3701 edges · 186 communities (145 shown, 41 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 235 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d3965708`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- NotificationSettingPolicy
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- AuditLog
- App\Models\Project
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- web.php
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
- RichText
- User
- NotificationSetting.php
- App\Models\Task
- UpdateProjectRequest
- Organization
- setup
- documents/create.blade.php
- User
- static
- _form.blade.php
- Pest.php
- Illuminate\View\View
- CodeLanguageClassSanitizer
- LoginRequest
- ImportBatch
- TaskManagementController.php
- FileStorageException
- Illuminate\Http\JsonResponse
- Illuminate\Support\Collection
- UserManagementController
- DocumentController
- Project
- config
- Illuminate\Database\Eloquent\Relations\BelongsTo
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Builder
- CommentPolicy
- require
- App\Models\User
- SubtaskPolicy
- Comment
- psr-4
- Task
- Illuminate\Http\UploadedFile
- Illuminate\Validation\Validator
- OrgMember
- HasAdminConfigurableColors.php
- ProjectPolicy
- UserPolicy
- UpdateTaskStatusColorsRequest
- StoreTaskRequest
- Subtask
- Illuminate\Http\Request
- StoreProjectRequest
- Document
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- UpdateRoleRequest
- Illuminate\Foundation\Http\FormRequest
- self
- CalendarController.php
- RolePolicy
- keywords
- .withNextFilename
- AuditLogPolicy.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- App\Services\StoredFile

## God Nodes (most connected - your core abstractions)
1. `User` - 186 edges
2. `Organization` - 97 edges
3. `Task` - 80 edges
4. `OrgMember` - 71 edges
5. `Project` - 58 edges
6. `Role` - 52 edges
7. `Department` - 45 edges
8. `ImportValidator` - 41 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 30 edges

## Surprising Connections (you probably didn't know these)
- `makeClientWithProjectAccessForDescription()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/RichText/DescriptionViewEditModeTest.php → app/Models/OrgMember.php
- `makeClientWithProjectAccessForDescription()` --calls--> `Role`  [INFERRED]
  tests/Feature/RichText/DescriptionViewEditModeTest.php → app/Models/Role.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (186 total, 41 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (6): ImportRow, DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (5): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "AuditLog"
Cohesion: 0.14
Nodes (5): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 5 - "App\Models\Project"
Cohesion: 0.14
Nodes (5): App\Models\Project, makeClientWithProjectAccessForDescription(), User, makeClientWithProjectAccess(), User

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

### Community 11 - "web.php"
Cohesion: 0.18
Nodes (8): App\Enums\FileCategory, config(), prefix(), RuntimeException, Symfony\Component\HttpFoundation\StreamedResponse, fileFor(), FileCategory, UploadedFile

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Role.php"
Cohesion: 0.22
Nodes (5): App\Models\Document, Document, Illuminate\Support\Facades\Notification, uploadDocumentForDownload(), User

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.33
Nodes (5): tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks, users._unsaved-changes-guard

### Community 47 - "rich-text-editor.js"
Cohesion: 0.06
Nodes (49): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+41 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.22
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 86 - "RichText"
Cohesion: 0.19
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.09
Nodes (11): isAssignableStaffForProject(), User, DepartmentPolicy, OrganizationPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, makeProjectMember() (+3 more)

### Community 88 - "NotificationSetting.php"
Cohesion: 0.12
Nodes (7): AuditEventDatabaseNotification, AuditEventMailNotification, MentionedInCommentNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 90 - "App\Models\Task"
Cohesion: 0.09
Nodes (9): App\Models\Task, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), Task (+1 more)

### Community 91 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 92 - "Organization"
Cohesion: 0.06
Nodes (11): DepartmentManagementController, StoreDepartmentRequest, Department, Organization, DepartmentSeeder, UserSeeder, Illuminate\Database\Eloquent\Relations\HasMany, makeTaskForAnalytics() (+3 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 107 - "Pest.php"
Cohesion: 0.07
Nodes (18): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, App\Models\ImportBatch, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Console\Command (+10 more)

### Community 108 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (13): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, KanbanController, NotificationController, OrganizationManagementController (+5 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "TaskManagementController.php"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (6): LinkPreviewController, Task, RichTextImageController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 115 - "Illuminate\Support\Collection"
Cohesion: 0.20
Nodes (7): App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 118 - "DocumentController"
Cohesion: 0.27
Nodes (5): DocumentController, Organization, Task, RichTextVideoController, Controller

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (5): AccessPermission, App\Models\Concerns\BelongsToOrganization, organization(), PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (5): GoogleAuthController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

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
Cohesion: 0.19
Nodes (5): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, videoResizeTaskPayload()

### Community 149 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (14): FileStorageService, Illuminate\Http\UploadedFile, StoredFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), fakeDocumentFile() (+6 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "OrgMember"
Cohesion: 0.10
Nodes (22): OrgMember, Role, CompanyRoleSyncer, makeStaffOnCalendar(), makeStaffForDocumentCreate(), makeClientWithProjectAccessForDownload(), makeClientForDocumentList(), makeStaffForDocumentList() (+14 more)

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 164 - "StoreTaskRequest"
Cohesion: 0.14
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 166 - "Illuminate\Http\Request"
Cohesion: 0.20
Nodes (8): RichTextAudioController, RichTextDocumentController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 168 - "Document"
Cohesion: 0.20
Nodes (4): Document, DocumentPolicy, makeDocumentForList(), makeDocument()

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 178 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `NotificationSettingPolicy`, `ImportValidator`, `.boardOrganizationIds`, `AuditLog`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`, `CommentPolicy`, `App\Models\User`, `Role.php`, `SubtaskPolicy`, `Task`, `OrgMember`, `ProjectPolicy`, `UserPolicy`, `Subtask`, `Document`, `RolePolicy`, `AuditLogPolicy.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `App\Models\Task`, `UpdateProjectRequest`, `Organization`, `Pest.php`, `Illuminate\View\View`, `LoginRequest`, `ImportBatch`, `TaskManagementController.php`, `Illuminate\Support\Collection`, `UserManagementController`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.100) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `Role.php`, `Illuminate\Validation\Validator`, `OrgMember`, `Document`, `CalendarController.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `App\Models\Task`, `Illuminate\View\View`, `ImportBatch`, `TaskManagementController.php`, `Illuminate\Support\Collection`, `UserManagementController`, `Project`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`, `web.php`, `App\Models\User`, `Role.php`, `SubtaskPolicy`, `Subtask`, `Illuminate\Http\Request`, `Document`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `NotificationSetting.php`, `App\Models\Task`, `Organization`, `Illuminate\View\View`, `ImportBatch`, `TaskManagementController.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Support\Collection`, `Project`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.042) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `OrgMember` (e.g. with `makeClientWithProjectAccessForDownload()` and `makeClientWithProjectAccessForAudio()`) actually correct?**
  _`OrgMember` has 5 INFERRED edges - model-reasoned connections that need verification._