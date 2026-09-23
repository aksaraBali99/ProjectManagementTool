# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 334 files · ~128,060 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1414 nodes · 3429 edges · 175 communities (140 shown, 35 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 222 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `87b02aa9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AuditLog
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- StoreTaskRequest
- RichText
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- Subtask
- LARAVEL_README.md
- AppServiceProvider.php
- OrgMember
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
- Illuminate\Http\JsonResponse
- RolePolicy
- Organization
- setup
- documents/create.blade.php
- FileCategory.php
- StoreProjectRequest
- _form.blade.php
- BootstrapEnvironment
- CommentPolicy
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Model
- CalendarController.php
- Illuminate\Database\Eloquent\Relations\HasMany
- ValidatesTaskAssignment.php
- Illuminate\Support\Collection
- Illuminate\View\View
- config
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Document
- Department.php
- require
- DepartmentPolicy
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- Comment
- Illuminate\Validation\Validator
- UpdateTaskStatusColorsRequest
- Closure
- UserManagementController
- post-create-project-cmd
- CompanyRoleRules
- LoginRequest
- autoload-dev
- Task
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Foundation\Http\FormRequest
- AuditLogPolicy.php
- DocumentPolicy.php

## God Nodes (most connected - your core abstractions)
1. `User` - 191 edges
2. `Organization` - 104 edges
3. `Task` - 82 edges
4. `OrgMember` - 72 edges
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
- `createOwner()` --calls--> `Role`  [INFERRED]
  tests/Pest.php → app/Models/Role.php
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php

## Import Cycles
- None detected.

## Communities (175 total, 35 thin omitted)

### Community 0 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, organization(), ImportRow, AuditEventDatabaseNotification, AuditEventMailNotification, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.15
Nodes (7): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "StoreTaskRequest"
Cohesion: 0.11
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 5 - "RichText"
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

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

### Community 11 - "Subtask"
Cohesion: 0.21
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "OrgMember"
Cohesion: 0.17
Nodes (4): OrgMember, CompanyRoleSyncer, makeStaffForDocumentCreate(), joinOrg()

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
Cohesion: 0.08
Nodes (38): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+30 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.21
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, NotificationSettingPolicy, OrganizationPolicy, ProjectPolicy, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 90 - "Illuminate\Http\JsonResponse"
Cohesion: 0.31
Nodes (4): RichTextAudioController, RichTextImageController, RichTextVideoController, Illuminate\Http\JsonResponse

### Community 92 - "Organization"
Cohesion: 0.07
Nodes (26): DepartmentManagementController, AccessPermission, Department, Organization, Project, Role, UserSeeder, makeTaskForAnalytics() (+18 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.06
Nodes (31): config(), prefix(), FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument (+23 more)

### Community 105 - "StoreProjectRequest"
Cohesion: 0.13
Nodes (5): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.18
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

### Community 112 - "Illuminate\Http\Request"
Cohesion: 0.25
Nodes (4): AuditTrailController, DashboardController, Collection, Illuminate\Http\Request

### Community 113 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.31
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 114 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 118 - "Illuminate\Support\Collection"
Cohesion: 0.29
Nodes (4): resolveCurrentOrganization(), ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 119 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AccessControlController, AnalyticsController, AuthenticatedSessionController, Controller, DocumentController, KanbanController, NotificationController, OrganizationManagementController (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.11
Nodes (6): GoogleAuthController, NotificationSettingsController, TaskColorController, NotificationSetting, Illuminate\Http\RedirectResponse, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 149 - "Comment"
Cohesion: 0.26
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.12
Nodes (6): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "Closure"
Cohesion: 0.26
Nodes (6): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, ValidPhoneNumber, Closure, Symfony\Component\HttpFoundation\Response

### Community 158 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 159 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 164 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 167 - "Task"
Cohesion: 0.12
Nodes (9): Task, MentionedInCommentNotification, TaskObserver, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription() (+1 more)

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `AuditLog`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `RichText`, `Department.php`, `Subtask`, `DepartmentPolicy`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `ImportCommitService.php`, `UserManagementController`, `LoginRequest`, `Task`, `ImportTemplateBuilder`, `AuditLogPolicy.php`, `DocumentPolicy.php`, `TaskManagementController`, `RolePolicy`, `Organization`, `FileCategory.php`, `StoreProjectRequest`, `BootstrapEnvironment`, `CommentPolicy`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ValidatesTaskAssignment.php`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.165) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `StoreTaskRequest`, `Illuminate\Http\RedirectResponse`, `Department.php`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CompanyRoleRules`, `ImportTemplateBuilder`, `TaskManagementController`, `User`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Model`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `AuditLog`, `ImportValidator`, `RichText`, `Illuminate\Http\RedirectResponse`, `Document`, `Department.php`, `Subtask`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ImportCommitService.php`, `TaskManagementController`, `User`, `Project.php`, `Illuminate\Http\JsonResponse`, `Organization`, `FileCategory.php`, `ImportBatch`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Model`, `CalendarController.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ValidatesTaskAssignment.php`, `Illuminate\View\View`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Project` (e.g. with `.storePending()` and `.storePending()`) actually correct?**
  _`Project` has 8 INFERRED edges - model-reasoned connections that need verification._