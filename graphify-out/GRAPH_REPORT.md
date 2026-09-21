# Graph Report - ProjectManagementTool  (2026-09-21)

## Corpus Check
- 317 files · ~110,431 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1328 nodes · 3148 edges · 178 communities (137 shown, 41 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 211 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f7419d38`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Seeder
- ImportValidator
- .boardOrganizationIds
- ProjectManagementController
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- LARAVEL_README.md
- AppServiceProvider.php
- UpdateRoleRequest
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
- OrgMember
- User
- Role.php
- CommentPolicy
- MentionedInCommentNotification
- setup
- documents/create.blade.php
- Pest.php
- Illuminate\Database\Eloquent\Model
- _form.blade.php
- Illuminate\Support\Collection
- Illuminate\Http\JsonResponse
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- RichText
- CalendarController.php
- ProjectPolicy
- SubtaskObserver
- UserPolicy
- StoreDepartmentRequest
- Illuminate\View\View
- config
- Organization
- task-colors/edit.blade.php
- Illuminate\Http\Request
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- require
- Illuminate\Database\Eloquent\Builder
- UpdateTaskPriorityColorsRequest
- AuditLog
- psr-4
- BootstrapEnvironment
- Subtask
- Illuminate\Validation\Validator
- UpdateTaskStatusColorsRequest
- Task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- UpdateTaskRequest
- Closure
- NotificationSettingPolicy
- Project
- CompanyRoleRules
- DocumentPolicy.php
- post-create-project-cmd
- UpdateUserRequest
- RolePolicy
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- AuditLogPolicy.php
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 183 edges
2. `Organization` - 96 edges
3. `Task` - 69 edges
4. `OrgMember` - 62 edges
5. `Project` - 49 edges
6. `Department` - 45 edges
7. `Role` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientForDocumentList()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentListTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (178 total, 41 thin omitted)

### Community 0 - "Illuminate\Database\Seeder"
Cohesion: 0.14
Nodes (8): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 1 - "ImportValidator"
Cohesion: 0.12
Nodes (4): DuplicateDetector, ImportIdCodec, ImportValidationContext, ImportValidator

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
Cohesion: 0.05
Nodes (37): concurrently, @fontsource/inter, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, lowlight, dependencies, chart.js (+29 more)

### Community 10 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

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
Cohesion: 0.40
Nodes (4): tasks._comments, tasks._subtasks, users._unsaved-changes-guard, tasks._documents

### Community 47 - "rich-text-editor.js"
Cohesion: 0.14
Nodes (19): CHART_PALETTE, highlightRichText(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), highlightCodeBlocks(), lowlight (+11 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.16
Nodes (9): ImportController, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, Symfony\Component\HttpFoundation\StreamedResponse (+1 more)

### Community 86 - "OrgMember"
Cohesion: 0.14
Nodes (12): AccessPermission, Department, OrgMember, makeStaffOnCalendar(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeStaffOnKanban() (+4 more)

### Community 87 - "User"
Cohesion: 0.11
Nodes (8): User, DepartmentPolicy, OrganizationPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask(), createTaskWithAssignee()

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Pest.php"
Cohesion: 0.08
Nodes (23): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DOMDocument, DOMElement (+15 more)

### Community 105 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.24
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 107 - "Illuminate\Support\Collection"
Cohesion: 0.26
Nodes (6): resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.38
Nodes (3): CodeLanguageClassSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 113 - "RichText"
Cohesion: 0.14
Nodes (3): StoreTaskRequest, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 119 - "Illuminate\View\View"
Cohesion: 0.05
Nodes (21): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DepartmentManagementController, NotificationController (+13 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Organization"
Cohesion: 0.16
Nodes (7): Organization, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument(), makeStaffForDocuments()

### Community 133 - "Illuminate\Http\Request"
Cohesion: 0.26
Nodes (4): DocumentController, TaskDocumentController, Document, Illuminate\Http\Request

### Community 134 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.16
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 145 - "AuditLog"
Cohesion: 0.06
Nodes (16): AuditLog, Comment, AuditEventDatabaseNotification, AuditEventMailNotification, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges() (+8 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "BootstrapEnvironment"
Cohesion: 0.14
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, EmployeeIdGenerator, Illuminate\Console\Command

### Community 149 - "Subtask"
Cohesion: 0.23
Nodes (3): SubtaskController, Subtask, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.26
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Task"
Cohesion: 0.17
Nodes (6): Task, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, makeTaskWithDescription(), taskUpdatePayload()

### Community 157 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 159 - "Closure"
Cohesion: 0.09
Nodes (11): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser (+3 more)

### Community 164 - "Project"
Cohesion: 0.19
Nodes (5): TaskManagementController, isAssignableStaffForProject(), Project, makeTaskForAnalytics(), makeProjectMember()

### Community 165 - "CompanyRoleRules"
Cohesion: 0.18
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 168 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 173 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **126 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+121 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `User.php`, `ProjectManagementController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `LoginRequest`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Builder`, `AuditLog`, `BootstrapEnvironment`, `Subtask`, `Task`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Closure`, `NotificationSettingPolicy`, `Project`, `DocumentPolicy.php`, `RolePolicy`, `AuditLogPolicy.php`, `ImportTemplateBuilder`, `OrgMember`, `Role.php`, `Department.php`, `CommentPolicy`, `Pest.php`, `Illuminate\Support\Collection`, `Illuminate\Http\JsonResponse`, `ImportBatch`, `ProjectPolicy`, `UserPolicy`, `Illuminate\View\View`, `Organization`?**
  _High betweenness centrality (0.210) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `Illuminate\Database\Seeder`, `ImportValidator`, `.boardOrganizationIds`, `User.php`, `ProjectManagementController`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Project`, `CompanyRoleRules`, `ImportTemplateBuilder`, `OrgMember`, `User`, `Role.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `ImportBatch`, `Priority.php`, `CalendarController.php`, `StoreDepartmentRequest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Builder`, `Subtask`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Project`, `Task.php`, `OrgMember`, `Role.php`, `MentionedInCommentNotification`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Support\Collection`, `Illuminate\Http\JsonResponse`, `ImportBatch`, `Priority.php`, `RichText`, `CalendarController.php`, `Illuminate\View\View`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _126 weakly-connected nodes found - possible documentation gaps or missing edges._