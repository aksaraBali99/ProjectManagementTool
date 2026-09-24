# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 337 files · ~130,545 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1419 nodes · 3448 edges · 177 communities (137 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 222 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fd04f008`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AuditLog
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
- DepartmentPolicy
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
- Priority.php
- UpdateTaskStatusColorsRequest
- RolePolicy
- OrgMember
- setup
- documents/create.blade.php
- FileCategory.php
- ValidClientUser.php
- _form.blade.php
- BootstrapEnvironment
- AuditEventNotifier
- CodeLanguageClassSanitizer
- HasAdminConfigurableColors.php
- ImportBatch
- StoreProjectRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ResolvesCurrentOrganization.php
- Illuminate\Database\Eloquent\Relations\HasMany
- Illuminate\Support\Collection
- Organization
- Illuminate\View\View
- config
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- DocumentPolicy.php
- Department.php
- require
- Pest.php
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- UserPolicy
- psr-4
- AuditLogPolicy.php
- Comment
- Illuminate\Validation\Validator
- UserManagementController
- NotificationSetting
- Illuminate\Database\Eloquent\Builder
- static
- LoginRequest
- CommentPolicy
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- Illuminate\Foundation\Http\FormRequest
- Subtask
- SubtaskPolicy
- keywords
- UpdateDepartmentRequest
- UpdateTaskPriorityColorsRequest
- UpdateProjectRequest

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
- `buildImportTestFile()` --calls--> `ImportSheetSchema`  [INFERRED]
  tests/Pest.php → app/Services/Import/ImportSheetSchema.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (177 total, 40 thin omitted)

### Community 0 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.22
Nodes (5): DatabaseSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.24
Nodes (3): TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model

### Community 5 - "Task"
Cohesion: 0.05
Nodes (20): CommentController, RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), StoreTaskRequest (+12 more)

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
Nodes (9): User, OrganizationPolicy, ProjectPolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, assignExistingTask() (+1 more)

### Community 92 - "OrgMember"
Cohesion: 0.14
Nodes (17): OrgMember, Project, makeTaskForAnalytics(), makeProjectMember(), makeStaffForDocumentCreate(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+9 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "FileCategory.php"
Cohesion: 0.07
Nodes (23): config(), prefix(), FileStorageException, self, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, FileStorageService (+15 more)

### Community 105 - "ValidClientUser.php"
Cohesion: 0.22
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

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
Cohesion: 0.11
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

### Community 139 - "Department.php"
Cohesion: 0.17
Nodes (3): App\Models\Task, Illuminate\Support\Facades\Notification, videoResizeTaskPayload()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "Pest.php"
Cohesion: 0.13
Nodes (13): DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase, buildImportTestFile(), createOwner(), richTextEditorContent(), richTextEditorNode() (+5 more)

### Community 143 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.09
Nodes (4): Document, Permission, Illuminate\Database\Eloquent\Relations\BelongsToMany, makeDocument()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "NotificationSetting"
Cohesion: 0.15
Nodes (5): NotificationSetting, NotificationSettingPolicy, NotificationSettingsResolver, NotificationEventType, givePersonalTaskAssignedRule()

### Community 159 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 177 - "Subtask"
Cohesion: 0.29
Nodes (5): Subtask, currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), SubtaskObserver

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `AuditLog`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Http\RedirectResponse`, `Task`, `DocumentPolicy.php`, `Department.php`, `DepartmentPolicy`, `Pest.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `UserPolicy`, `AuditLogPolicy.php`, `UserManagementController`, `NotificationSetting`, `Illuminate\Database\Eloquent\Builder`, `LoginRequest`, `CommentPolicy`, `ImportTemplateBuilder`, `SubtaskPolicy`, `TaskManagementController`, `Priority.php`, `RolePolicy`, `OrgMember`, `ValidClientUser.php`, `BootstrapEnvironment`, `AuditEventNotifier`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Organization`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `AuditLog`, `ImportValidator`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Database\Eloquent\Builder`, `SubtaskPolicy`, `TaskManagementController`, `User`, `Priority.php`, `OrgMember`, `FileCategory.php`, `ImportBatch`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Illuminate\Validation\Validator`, `UserManagementController`, `ImportTemplateBuilder`, `TaskManagementController`, `User`, `Priority.php`, `OrgMember`, `ImportBatch`, `ResolvesCurrentOrganization.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\View\View`, `User.php`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Project` (e.g. with `.storePending()` and `.storePending()`) actually correct?**
  _`Project` has 8 INFERRED edges - model-reasoned connections that need verification._