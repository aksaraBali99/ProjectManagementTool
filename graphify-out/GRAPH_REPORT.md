# Graph Report - ProjectManagementTool  (2026-09-25)

## Corpus Check
- 360 files · ~155,113 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1518 nodes · 3760 edges · 184 communities (144 shown, 40 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 234 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0c6cfb8a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- CommentPolicy
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- TaskStatus.php
- NotificationEventType.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- ImportController.php
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
- AuditLog
- NotificationSettingPolicy
- Organization
- setup
- documents/create.blade.php
- Role
- LoginRequest
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- ProjectManagementController
- ImportBatch
- DocumentAccessLevel.php
- TaskObserver
- .resolvePending
- Illuminate\Http\Request
- AuditEventNotifier
- TaskManagementController
- config
- Illuminate\Database\Eloquent\Model
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Builder
- require
- Document
- Comment
- psr-4
- Task
- FileCategory.php
- Illuminate\Validation\Validator
- OrgMember
- HasAdminConfigurableColors.php
- StoreProjectRequest
- UserManagementController.php
- UserManagementController
- SubtaskPolicy
- .storePending
- Illuminate\Http\JsonResponse
- .storePending
- .storePending
- DocumentPolicy.php
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- post-create-project-cmd
- Illuminate\Foundation\Http\FormRequest
- .__invoke
- StoreDepartmentRequest
- keywords
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- NotificationSetting
- ValidatesTaskAssignment.php

## God Nodes (most connected - your core abstractions)
1. `User` - 200 edges
2. `Organization` - 110 edges
3. `Task` - 99 edges
4. `OrgMember` - 84 edges
5. `Project` - 68 edges
6. `Role` - 53 edges
7. `Department` - 45 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 32 edges

## Surprising Connections (you probably didn't know these)
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php
- `makeStaffForDocumentCreate()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentCreateTest.php → app/Models/Role.php
- `makeClientWithProjectAccessForDownload()` --calls--> `Role`  [INFERRED]
  tests/Feature/Documents/DocumentDownloadTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (184 total, 40 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.19
Nodes (7): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

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

### Community 11 - "ImportController.php"
Cohesion: 0.18
Nodes (3): DocumentController, ImportController, Symfony\Component\HttpFoundation\StreamedResponse

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
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Foundation\Testing\TestCase (+12 more)

### Community 86 - "RichText"
Cohesion: 0.21
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.07
Nodes (11): User, AuditLogPolicy, DepartmentPolicy, OrganizationPolicy, ProjectPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User (+3 more)

### Community 88 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 92 - "Organization"
Cohesion: 0.09
Nodes (3): OrganizationManagementController, Organization, Illuminate\Database\Eloquent\Relations\HasMany

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Role"
Cohesion: 0.11
Nodes (9): RoleManagementController, Role, RolePolicy, makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList(), makeClientForDocuments(), makeDocument() (+1 more)

### Community 105 - "LoginRequest"
Cohesion: 0.10
Nodes (10): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.13
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.18
Nodes (5): AnalyticsController, DepartmentManagementController, NotificationController, SettingsController, Illuminate\View\View

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 111 - "ImportBatch"
Cohesion: 0.17
Nodes (7): ImportBatch, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "DocumentAccessLevel.php"
Cohesion: 0.09
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "TaskObserver"
Cohesion: 0.27
Nodes (4): currentImportBatchId(), shouldSuppressNotification(), taggedChanges(), TaskObserver

### Community 115 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (8): AuditTrailController, resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Illuminate\Http\Request, Illuminate\Support\Collection, flattenCalendarCells()

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.09
Nodes (7): organization(), ImportRow, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.13
Nodes (7): AccessControlController, AuthenticatedSessionController, GoogleAuthController, Controller, PermissionManagementController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Document"
Cohesion: 0.18
Nodes (4): RichTextDocumentController, TaskDocumentController, Document, uploadDocumentForDownload()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.11
Nodes (10): Task, MentionedInCommentNotification, TaskPolicy, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 149 - "FileCategory.php"
Cohesion: 0.07
Nodes (25): config(), prefix(), FileStorageException, self, FileStorageService, PastedMediaNamer, StoredFile, Closure (+17 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.10
Nodes (7): UpdateRoleRequest, UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "OrgMember"
Cohesion: 0.10
Nodes (22): AccessPermission, Department, OrgMember, Project, makeTaskForAnalytics(), makeStaffOnCalendar(), makeProjectMember(), makeStaffOnDashboard() (+14 more)

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 165 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): SubtaskController, Subtask, SubtaskObserver, Illuminate\Http\JsonResponse

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 172 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 178 - ".__invoke"
Cohesion: 0.50
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 185 - "NotificationSetting"
Cohesion: 0.24
Nodes (3): NotificationSettingsController, NotificationSetting, givePersonalTaskAssignedRule()

### Community 189 - "ValidatesTaskAssignment.php"
Cohesion: 0.13
Nodes (4): isAssignableStaffForProject(), StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `CommentPolicy`, `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`, `NotificationEventType.php`, `User.php`, `Role.php`, `Document`, `Task`, `OrgMember`, `StoreProjectRequest`, `UserManagementController.php`, `UserManagementController`, `SubtaskPolicy`, `Illuminate\Http\JsonResponse`, `DocumentPolicy.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `NotificationSetting`, `ValidatesTaskAssignment.php`, `RichText`, `AuditLog`, `NotificationSettingPolicy`, `Task.php`, `Organization`, `Role`, `LoginRequest`, `BootstrapEnvironment`, `Illuminate\View\View`, `ProjectManagementController`, `ImportBatch`, `DocumentAccessLevel.php`, `Illuminate\Http\Request`, `AuditEventNotifier`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Builder`, `ImportController.php`, `User.php`, `Role.php`, `Document`, `Comment`, `FileCategory.php`, `OrgMember`, `SubtaskPolicy`, `.storePending`, `Illuminate\Http\JsonResponse`, `.storePending`, `.storePending`, `.__invoke`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `RichText`, `Task.php`, `Illuminate\View\View`, `ImportBatch`, `DocumentAccessLevel.php`, `TaskObserver`, `.resolvePending`, `Illuminate\Http\Request`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `ImportController.php`, `User.php`, `Role.php`, `Illuminate\Validation\Validator`, `OrgMember`, `.__invoke`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ValidatesTaskAssignment.php`, `User`, `Task.php`, `Role`, `Illuminate\View\View`, `ProjectManagementController`, `ImportBatch`, `Illuminate\Http\Request`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.053) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._