# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 337 files · ~130,642 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1420 nodes · 3447 edges · 180 communities (141 shown, 39 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 222 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `921548bc`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\JsonResponse
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
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
- Task.php
- UpdateTaskStatusColorsRequest
- RolePolicy
- OrgMember
- setup
- documents/create.blade.php
- FileCategory.php
- StoreUserRequest
- _form.blade.php
- BootstrapEnvironment
- OrganizationPolicy
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- ProjectPolicy
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ResolvesCurrentOrganization.php
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Support\Collection
- Organization
- Illuminate\View\View
- config
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Document
- Role.php
- require
- Pest.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- AuditLogPolicy.php
- Comment
- Illuminate\Validation\Validator
- UserManagementController
- AuditLog
- Illuminate\Database\Eloquent\Builder
- UpdateRoleRequest
- LoginRequest
- StoreTaskRequest
- UpdateTaskRequest
- TagsImportBatch.php
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
4. `OrgMember` - 73 edges
5. `Project` - 60 edges
6. `Role` - 49 edges
7. `Department` - 45 edges
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

## Communities (180 total, 39 thin omitted)

### Community 0 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (6): RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (5): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "Task"
Cohesion: 0.13
Nodes (10): TaskDocumentController, Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

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
Cohesion: 0.18
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 12 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "Role"
Cohesion: 0.29
Nodes (3): PermissionManagementController, RoleManagementController, Role

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
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 87 - "User"
Cohesion: 0.09
Nodes (9): User, CommentPolicy, DepartmentPolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask() (+1 more)

### Community 88 - "Task.php"
Cohesion: 0.08
Nodes (4): TaskPriorityColor, TaskStatusColor, Illuminate\Contracts\Validation\Validator, Illuminate\Database\Eloquent\Model

### Community 92 - "OrgMember"
Cohesion: 0.12
Nodes (13): OrgMember, Project, makeTaskForAnalytics(), makeProjectMember(), makeTaskOnDashboard(), makeStaffForDocumentCreate(), makeClientWithProjectAccessForAudio(), makeClientWithProjectAccessForDescription() (+5 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.06
Nodes (24): config(), prefix(), FileStorageException, self, StoreProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser (+16 more)

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.14
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 113 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.14
Nodes (3): organization(), ImportRow, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 114 - "ResolvesCurrentOrganization.php"
Cohesion: 0.18
Nodes (7): AccessControlController, CalendarController, resolveCurrentOrganization(), DocumentController, KanbanController, Carbon, Illuminate\Support\Carbon

### Community 116 - "Illuminate\Support\Collection"
Cohesion: 0.22
Nodes (5): DashboardController, Collection, ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 118 - "Organization"
Cohesion: 0.08
Nodes (11): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, Organization, DepartmentSeeder, UserSeeder, makeStaffOnCalendar() (+3 more)

### Community 119 - "Illuminate\View\View"
Cohesion: 0.10
Nodes (12): AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController, OrganizationManagementController (+4 more)

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): NotificationSettingsController, Illuminate\Http\RedirectResponse, Illuminate\Http\Request

### Community 134 - "Document"
Cohesion: 0.14
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
Cohesion: 0.19
Nodes (3): Comment, CommentObserver, AuditEventNotifier

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.31
Nodes (4): validateCompanyRoles(), validateSuperAdminGrant(), CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "AuditLog"
Cohesion: 0.07
Nodes (13): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, NotificationEventType, NotificationSettingsResolver, NotificationEventType (+5 more)

### Community 159 - "LoginRequest"
Cohesion: 0.10
Nodes (10): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 165 - "TagsImportBatch.php"
Cohesion: 0.83
Nodes (3): currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 166 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.25
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **39 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\JsonResponse`, `ImportValidator`, `.boardOrganizationIds`, `ImportCommitService.php`, `Illuminate\Http\RedirectResponse`, `Document`, `Role.php`, `RichText`, `Pest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `AuditLogPolicy.php`, `UserManagementController`, `AuditLog`, `Illuminate\Database\Eloquent\Builder`, `LoginRequest`, `Subtask`, `ImportTemplateBuilder`, `TaskManagementController`, `Task.php`, `RolePolicy`, `OrgMember`, `FileCategory.php`, `BootstrapEnvironment`, `OrganizationPolicy`, `ImportBatch`, `ProjectPolicy`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Organization`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.158) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `Illuminate\Http\JsonResponse`, `ImportValidator`, `ImportCommitService.php`, `Illuminate\Http\RedirectResponse`, `RichText`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Comment`, `Illuminate\Database\Eloquent\Builder`, `Subtask`, `TaskManagementController`, `User`, `Task.php`, `OrgMember`, `FileCategory.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Document`, `Role.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `ImportTemplateBuilder`, `TaskManagementController`, `Task.php`, `OrgMember`, `OrganizationPolicy`, `ImportBatch`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Project` (e.g. with `.storePending()` and `.storePending()`) actually correct?**
  _`Project` has 8 INFERRED edges - model-reasoned connections that need verification._