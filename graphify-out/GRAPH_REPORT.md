# Graph Report - ProjectManagementTool  (2026-09-22)

## Corpus Check
- 329 files · ~122,892 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1396 nodes · 3358 edges · 178 communities (139 shown, 39 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 219 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `26ce3c64`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Organization
- StoreDepartmentRequest
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- DepartmentPolicy
- LARAVEL_README.md
- AppServiceProvider.php
- ResolvesCurrentOrganization.php
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
- AuditLog
- User
- NotificationSetting.php
- Illuminate\Database\Eloquent\Model
- TaskManagementTest.php
- setup
- documents/create.blade.php
- FileCategory.php
- Closure
- _form.blade.php
- BootstrapEnvironment
- CommentPolicy
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- SubtaskPolicy
- RichText
- CalendarController.php
- Illuminate\Database\Eloquent\Relations\HasMany
- Subtask
- Project
- Illuminate\View\View
- config
- OrgMember
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Foundation\Http\FormRequest
- Role.php
- require
- Illuminate\Database\Eloquent\Builder
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- Comment
- Illuminate\Validation\Validator
- UpdateTaskRequest
- Task
- StoreProjectRequest
- Illuminate\Support\Collection
- static
- UpdateTaskPriorityColorsRequest
- StoreTaskRequest
- RolePolicy
- Illuminate\Http\JsonResponse
- UpdateUserPasswordRequest
- UpdateUserRequest
- post-create-project-cmd
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- AuditLogPolicy.php
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 187 edges
2. `Organization` - 100 edges
3. `Task` - 79 edges
4. `OrgMember` - 68 edges
5. `Project` - 55 edges
6. `Role` - 47 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeClientWithProjectAccessForAudio()` --calls--> `Role`  [INFERRED]
  tests/Feature/RichText/AudioUploadTest.php → app/Models/Role.php
- `makeClientWithProjectAccess()` --calls--> `Role`  [INFERRED]
  tests/Feature/RichText/ImageUploadTest.php → app/Models/Role.php
- `makeClientOnProject()` --calls--> `Role`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/Role.php
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (178 total, 39 thin omitted)

### Community 0 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Organization"
Cohesion: 0.10
Nodes (17): DocumentController, AccessPermission, Document, Organization, Role, makeStaffOnCalendar(), makeStaffOnDashboard(), makeStaffForDocumentCreate() (+9 more)

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

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ResolvesCurrentOrganization.php"
Cohesion: 0.20
Nodes (5): AccessControlController, resolveCurrentOrganization(), DashboardController, Collection, KanbanController

### Community 15 - "users/create.blade.php"
Cohesion: 0.50
Nodes (3): users._form, users._inline-validation, users._unsaved-changes-guard

### Community 16 - "users/edit.blade.php"
Cohesion: 0.40
Nodes (4): users._form, users._inline-validation, users._password-input, users._unsaved-changes-guard

### Community 17 - "tasks/edit.blade.php"
Cohesion: 0.40
Nodes (4): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.09
Nodes (36): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+28 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "AuditLog"
Cohesion: 0.14
Nodes (5): AuditLog, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver, NotificationEventType

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, DocumentPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, makeProjectMember() (+2 more)

### Community 90 - "NotificationSetting.php"
Cohesion: 0.16
Nodes (6): AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 91 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.31
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.06
Nodes (29): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument (+21 more)

### Community 105 - "Closure"
Cohesion: 0.18
Nodes (6): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.17
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, ImageDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (9): ImportController, ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon (+1 more)

### Community 113 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "CalendarController.php"
Cohesion: 0.44
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 118 - "Project"
Cohesion: 0.13
Nodes (10): TaskManagementController, Department, Project, Illuminate\Contracts\Validation\Validator, makeTaskForAnalytics(), makeTaskOnDashboard(), makeClientWithProjectAccessForAudio(), makeClientWithProjectAccess() (+2 more)

### Community 119 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (10): AnalyticsController, AuditTrailController, AuthenticatedSessionController, Controller, DepartmentManagementController, NotificationController, OrganizationManagementController, RoleManagementController (+2 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (15): GoogleAuthController, NotificationSettingsController, PermissionManagementController, TaskColorController, UserManagementController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive (+7 more)

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateTaskStatusColorsRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Comment"
Cohesion: 0.29
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateRoleRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "Task"
Cohesion: 0.11
Nodes (9): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription() (+1 more)

### Community 158 - "Illuminate\Support\Collection"
Cohesion: 0.30
Nodes (3): ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 159 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 167 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (6): RichTextAudioController, RichTextImageController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 170 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 179 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **132 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+127 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **39 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportValidator`, `.boardOrganizationIds`, `Organization`, `Illuminate\Http\RedirectResponse`, `Role.php`, `DepartmentPolicy`, `Illuminate\Database\Eloquent\Builder`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `ImportCommitService.php`, `Task`, `Illuminate\Support\Collection`, `RolePolicy`, `Illuminate\Http\JsonResponse`, `AuditLogPolicy.php`, `ImportTemplateBuilder`, `AuditLog`, `Task.php`, `TaskManagementTest.php`, `FileCategory.php`, `Closure`, `BootstrapEnvironment`, `CommentPolicy`, `ImportBatch`, `SubtaskPolicy`, `RichText`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `Project`, `Illuminate\View\View`, `OrgMember`?**
  _High betweenness centrality (0.167) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Role.php`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportCommitService.php`, `Illuminate\Validation\Validator`, `Illuminate\Support\Collection`, `ImportTemplateBuilder`, `User`, `Task.php`, `Illuminate\Database\Eloquent\Model`, `TaskManagementTest.php`, `ImportBatch`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Project`, `Illuminate\View\View`, `OrgMember`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ImportValidator`, `Illuminate\Http\RedirectResponse`, `Role.php`, `Illuminate\Database\Eloquent\Builder`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Priority.php`, `Illuminate\Http\JsonResponse`, `Task.php`, `Illuminate\Database\Eloquent\Model`, `FileCategory.php`, `ImportBatch`, `SubtaskPolicy`, `RichText`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Subtask`, `Project`, `Illuminate\View\View`, `OrgMember`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _132 weakly-connected nodes found - possible documentation gaps or missing edges._