# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 352 files · ~143,290 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1479 nodes · 3619 edges · 185 communities (141 shown, 44 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 230 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `54491b32`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Organization
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Builder
- TagsImportBatch.php
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
- StoreDepartmentRequest
- User
- AuditLog
- Priority.php
- ProjectStatus.php
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- Pest.php
- Closure
- _form.blade.php
- BootstrapEnvironment
- FileCategory.php
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- LinkPreview
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Http\JsonResponse
- CalendarController.php
- Illuminate\Support\Collection
- Illuminate\Http\Request
- Illuminate\View\View
- config
- static
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- LoginRequest
- Illuminate\Database\Eloquent\Model
- require
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- CommentPolicy
- psr-4
- Task
- Comment
- Illuminate\Validation\Validator
- UserManagementController
- AuditEventNotifier
- TaskManagementController
- DepartmentPolicy
- TaskManagementController.php
- UpdateTaskStatusColorsRequest
- NotificationEventType.php
- StoreProjectRequest
- UpdateTaskPriorityColorsRequest
- StoreTaskRequest
- ProjectPolicy
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- .storePending
- Illuminate\Foundation\Http\FormRequest
- Subtask
- NotificationSettingPolicy
- keywords
- .storePending
- AuditLogPolicy.php
- DocumentPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 197 edges
2. `Organization` - 108 edges
3. `Task` - 93 edges
4. `OrgMember` - 78 edges
5. `Project` - 66 edges
6. `Role` - 51 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `makeStaffOnKanban()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (185 total, 44 thin omitted)

### Community 0 - "Organization"
Cohesion: 0.08
Nodes (29): Department, Organization, OrgMember, Project, Role, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeStaffOnCalendar() (+21 more)

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.19
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 5 - "TagsImportBatch.php"
Cohesion: 0.60
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

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
Cohesion: 0.18
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
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, Symfony\Component\HttpFoundation\StreamedResponse

### Community 87 - "User"
Cohesion: 0.08
Nodes (12): isAssignableStaffForProject(), User, OrganizationPolicy, RolePolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+4 more)

### Community 88 - "AuditLog"
Cohesion: 0.13
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 90 - "Priority.php"
Cohesion: 0.09
Nodes (4): KanbanController, TaskColorController, TaskPriorityColor, TaskStatusColor

### Community 91 - "ProjectStatus.php"
Cohesion: 0.13
Nodes (4): UpdateProjectRequest, ValidClientUser, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Pest.php"
Cohesion: 0.06
Nodes (27): FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase (+19 more)

### Community 105 - "Closure"
Cohesion: 0.25
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidPhoneNumber, Closure, Symfony\Component\HttpFoundation\Response

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.10
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Illuminate\Console\Command

### Community 108 - "FileCategory.php"
Cohesion: 0.32
Nodes (4): RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.20
Nodes (5): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.20
Nodes (6): LinkPreviewController, RichTextDocumentController, RichTextVideoController, TaskDocumentController, Document, Illuminate\Http\JsonResponse

### Community 115 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 116 - "Illuminate\Support\Collection"
Cohesion: 0.20
Nodes (6): resolveCurrentOrganization(), DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 119 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (14): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, ImportController (+6 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "static"
Cohesion: 0.28
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (5): DepartmentManagementController, NotificationSettingsController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 139 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (3): AccessPermission, CompanyRoleSyncer, Illuminate\Database\Eloquent\Model

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.14
Nodes (3): Permission, PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.15
Nodes (9): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), taskUpdatePayload() (+1 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.23
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `ICONS`, `TOOLBAR`, `HEADING_OPTIONS`, `NATIVE_EMOJIS`, `EMOJI_CATEGORIES` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Organization`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Http\RedirectResponse`, `LoginRequest`, `RichText`, `Illuminate\Database\Eloquent\Model`, `User.php`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CommentPolicy`, `UserManagementController`, `AuditEventNotifier`, `TaskManagementController`, `DepartmentPolicy`, `TaskManagementController.php`, `NotificationEventType.php`, `ProjectPolicy`, `Subtask`, `NotificationSettingPolicy`, `AuditLogPolicy.php`, `DocumentPolicy`, `AuditLog`, `ProjectStatus.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Pest.php`, `BootstrapEnvironment`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Collection`, `Illuminate\View\View`?**
  _High betweenness centrality (0.116) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Organization`, `ImportValidator`, `Illuminate\Database\Eloquent\Builder`, `TagsImportBatch.php`, `RichText`, `Illuminate\Database\Eloquent\Model`, `User.php`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `TaskManagementController.php`, `.storePending`, `Subtask`, `.storePending`, `User`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `FileCategory.php`, `ImportBatch`, `LinkPreview`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\JsonResponse`, `CalendarController.php`, `Illuminate\Support\Collection`, `Illuminate\Http\Request`, `Illuminate\View\View`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `User.php`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `TaskManagementController`, `TaskManagementController.php`, `ImportTemplateBuilder`, `User`, `Priority.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ImportBatch`, `CalendarController.php`, `Illuminate\Support\Collection`, `Illuminate\View\View`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._