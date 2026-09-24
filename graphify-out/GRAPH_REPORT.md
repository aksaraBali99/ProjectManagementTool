# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 338 files · ~131,404 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1422 nodes · 3456 edges · 180 communities (143 shown, 37 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 222 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fc57d6d9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\JsonResponse
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Model
- Task
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- RichText
- LARAVEL_README.md
- AppServiceProvider.php
- Role
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
- TaskManagementController
- User
- UpdateTaskStatusColorsRequest
- ValidClientUser.php
- setup
- documents/create.blade.php
- FileCategory.php
- static
- _form.blade.php
- BootstrapEnvironment
- CalendarController.php
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- ProjectPolicy
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Organization
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Support\Collection
- Project
- Illuminate\View\View
- config
- OrgMember
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Document
- Role.php
- require
- Pest.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- LoginRequest
- Comment
- Illuminate\Validation\Validator
- UserManagementController
- AuditLog
- Illuminate\Database\Eloquent\Builder
- CommentPolicy
- Illuminate\Http\Request
- ValidatesTaskAssignment.php
- StoreProjectRequest
- .storePending
- post-create-project-cmd
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Foundation\Http\FormRequest
- Subtask
- keywords
- UpdateDepartmentRequest
- UpdateTaskPriorityColorsRequest
- UpdateProjectRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 191 edges
2. `Organization` - 104 edges
3. `Task` - 84 edges
4. `OrgMember` - 74 edges
5. `Project` - 60 edges
6. `Role` - 49 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeStaffForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php
- `makeClientForDocuments()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentVisibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (180 total, 37 thin omitted)

### Community 0 - "Illuminate\Http\JsonResponse"
Cohesion: 0.20
Nodes (5): RichTextAudioController, RichTextImageController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.23
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.11
Nodes (4): ImportRow, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 5 - "Task"
Cohesion: 0.15
Nodes (9): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription(), taskUpdatePayload() (+1 more)

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

### Community 11 - "RichText"
Cohesion: 0.15
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Role"
Cohesion: 0.10
Nodes (13): RoleManagementController, AccessPermission, Role, RolePolicy, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffOnKanban(), makeClientWithProjectAccessForAudio() (+5 more)

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
Cohesion: 0.07
Nodes (39): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+31 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 87 - "User"
Cohesion: 0.08
Nodes (10): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 92 - "ValidClientUser.php"
Cohesion: 0.22
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.09
Nodes (19): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, Closure, Illuminate\Http\UploadedFile (+11 more)

### Community 105 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.18
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 108 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.12
Nodes (10): ImportController, ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver (+2 more)

### Community 113 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (3): organization(), Illuminate\Database\Eloquent\Relations\BelongsTo, makeStaffForDocumentCreate()

### Community 114 - "Organization"
Cohesion: 0.14
Nodes (4): AccessControlController, DocumentController, KanbanController, Organization

### Community 116 - "Illuminate\Support\Collection"
Cohesion: 0.20
Nodes (6): resolveCurrentOrganization(), DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 118 - "Project"
Cohesion: 0.11
Nodes (9): DepartmentManagementController, StoreDepartmentRequest, Department, Project, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeProjectMember(), makeTaskOnDashboard() (+1 more)

### Community 119 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (9): AnalyticsController, AuthenticatedSessionController, GoogleAuthController, Controller, NotificationController, OrganizationManagementController, PermissionManagementController, SettingsController (+1 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.16
Nodes (3): NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "Document"
Cohesion: 0.15
Nodes (8): Document, DocumentPolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Pest.php"
Cohesion: 0.13
Nodes (13): DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase, buildImportTestFile(), createOwner(), richTextEditorContent(), richTextEditorNode() (+5 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Comment"
Cohesion: 0.26
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 159 - "Illuminate\Http\Request"
Cohesion: 0.21
Nodes (6): AuditTrailController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 160 - "ValidatesTaskAssignment.php"
Cohesion: 0.15
Nodes (3): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest

### Community 166 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `Document`, `Role.php`, `RichText`, `Pest.php`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `LoginRequest`, `UserManagementController`, `AuditLog`, `Illuminate\Database\Eloquent\Builder`, `CommentPolicy`, `Illuminate\Http\Request`, `ValidatesTaskAssignment.php`, `Subtask`, `ImportTemplateBuilder`, `TaskManagementController`, `Priority.php`, `ValidClientUser.php`, `BootstrapEnvironment`, `ImportBatch`, `ProjectPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Project`, `Illuminate\View\View`, `OrgMember`?**
  _High betweenness centrality (0.160) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\JsonResponse`, `ImportValidator`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `RichText`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Builder`, `ValidatesTaskAssignment.php`, `.storePending`, `Subtask`, `TaskManagementController`, `User`, `Priority.php`, `FileCategory.php`, `CalendarController.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Project`, `Illuminate\View\View`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Document`, `Role.php`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `Illuminate\Http\Request`, `ImportTemplateBuilder`, `TaskManagementController`, `User`, `Priority.php`, `CalendarController.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Project`, `Illuminate\View\View`, `OrgMember`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Project` (e.g. with `.storePending()` and `.storePending()`) actually correct?**
  _`Project` has 8 INFERRED edges - model-reasoned connections that need verification._