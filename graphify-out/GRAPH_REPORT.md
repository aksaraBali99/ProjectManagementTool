# Graph Report - ProjectManagementTool  (2026-09-26)

## Corpus Check
- 378 files · ~172,530 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1575 nodes · 4017 edges · 186 communities (149 shown, 37 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 255 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1d519835`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- FileCategory.php
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Department.php
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- auth.php
- LARAVEL_README.md
- AppServiceProvider.php
- Comment.php
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
- TaskManagementController
- Illuminate\Support\Collection
- Document
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- FileStorageException
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Http\Request
- ImportBatch
- LinkPreviewService.php
- Comment
- Illuminate\Http\JsonResponse
- Illuminate\Http\UploadedFile
- TaskManagementController.php
- CalendarController.php
- LoginRequest
- config
- CommentPolicy
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- AuditLog
- OrganizationPolicy
- require
- Role.php
- Illuminate\Foundation\Http\FormRequest
- TagsImportBatch.php
- psr-4
- Task
- Subtask
- Illuminate\Validation\Validator
- HasAdminConfigurableColors.php
- StoreProjectRequest
- CompanyRoleRules
- UserManagementController.php
- ProjectPolicy
- TaskStatus.php
- UserPolicy
- Department
- UpdateRoleRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- PermissionManagementController.php
- Illuminate\Database\Eloquent\Builder
- keywords
- config
- post-create-project-cmd
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- fileFor

## God Nodes (most connected - your core abstractions)
1. `User` - 219 edges
2. `Organization` - 125 edges
3. `OrgMember` - 104 edges
4. `Task` - 102 edges
5. `Project` - 75 edges
6. `Role` - 64 edges
7. `Department` - 60 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 33 edges

## Surprising Connections (you probably didn't know these)
- `makeReactionEligibleStaff()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Comments/CommentReactionTest.php → app/Models/AccessPermission.php
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (186 total, 37 thin omitted)

### Community 0 - "FileCategory.php"
Cohesion: 0.30
Nodes (3): PastedMediaNamer, Closure, RuntimeException

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.14
Nodes (8): Permission, DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (6): CommentReactionController, CommentReaction, organization(), ImportRow, PastedMedia, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Department.php"
Cohesion: 0.09
Nodes (3): TaskPriorityColor, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Notification

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

### Community 11 - "auth.php"
Cohesion: 0.24
Nodes (4): EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Symfony\Component\HttpFoundation\Response

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
Nodes (57): buildEmojiPicker(), CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport() (+49 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.10
Nodes (20): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, PhpOffice\PhpSpreadsheet\Spreadsheet (+12 more)

### Community 86 - "StoreTaskRequest"
Cohesion: 0.10
Nodes (4): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 87 - "User"
Cohesion: 0.09
Nodes (10): User, AuditLogPolicy, DepartmentPolicy, RolePolicy, TaskPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+2 more)

### Community 90 - "Illuminate\Support\Collection"
Cohesion: 0.32
Nodes (3): ProjectManagementController, Illuminate\Support\Collection, flattenCalendarCells()

### Community 91 - "Document"
Cohesion: 0.18
Nodes (5): Document, DocumentPolicy, uploadDocumentForDownload(), makeDocumentForList(), makeDocument()

### Community 92 - "Organization"
Cohesion: 0.12
Nodes (24): Organization, OrgMember, Project, Role, makeTaskForAnalytics(), makeReactionClient(), makeReactionEligibleStaff(), makeStaffOnDashboard() (+16 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.13
Nodes (4): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (10): AnalyticsController, AuthenticatedSessionController, Controller, DepartmentManagementController, ImportController, NotificationController, OrganizationManagementController, RoleManagementController (+2 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Http\Request"
Cohesion: 0.14
Nodes (8): AccessControlController, AuditTrailController, resolveCurrentOrganization(), DashboardController, Collection, DocumentController, Illuminate\Http\Request, Symfony\Component\HttpFoundation\StreamedResponse

### Community 111 - "ImportBatch"
Cohesion: 0.14
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.11
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Comment"
Cohesion: 0.14
Nodes (4): CommentController, Comment, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.16
Nodes (7): LinkPreviewController, RichTextAudioController, RichTextImageController, RichTextVideoController, SubtaskController, isAssignableStaffForProject(), Illuminate\Http\JsonResponse

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.13
Nodes (14): FileStorageService, StoredFile, Illuminate\Http\UploadedFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo(), UploadedFile (+6 more)

### Community 118 - "CalendarController.php"
Cohesion: 0.54
Nodes (3): CalendarController, Carbon, Illuminate\Support\Carbon

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.18
Nodes (4): GoogleAuthController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 134 - "AuditLog"
Cohesion: 0.06
Nodes (14): AuditLog, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier, NotificationEventType, NotificationSettingsResolver (+6 more)

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.26
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.11
Nodes (12): RichTextDocumentController, TaskDocumentController, Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate() (+4 more)

### Community 149 - "Subtask"
Cohesion: 0.16
Nodes (3): Subtask, SubtaskObserver, SubtaskPolicy

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.17
Nodes (5): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, Illuminate\Validation\Validator

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "StoreProjectRequest"
Cohesion: 0.11
Nodes (6): StoreProjectRequest, UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 158 - "CompanyRoleRules"
Cohesion: 0.16
Nodes (6): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), CompanyRoleRules, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 166 - "Department"
Cohesion: 0.13
Nodes (13): AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember(), makeEligibleStaff(), makeStaffForKanbanAssignee() (+5 more)

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 182 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 185 - "fileFor"
Cohesion: 0.67
Nodes (3): fileFor(), FileCategory, UploadedFile

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **37 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Department.php`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `OrganizationPolicy`, `Role.php`, `Comment.php`, `Task`, `Subtask`, `StoreProjectRequest`, `UserManagementController.php`, `ProjectPolicy`, `UserPolicy`, `Department`, `UpdateUserRequest`, `Illuminate\Database\Eloquent\Builder`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `Illuminate\Support\Collection`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `Illuminate\View\View`, `Illuminate\Http\Request`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\JsonResponse`, `TaskManagementController.php`, `LoginRequest`, `CommentPolicy`?**
  _High betweenness centrality (0.137) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Department.php`, `OrganizationPolicy`, `Role.php`, `Comment.php`, `CompanyRoleRules`, `UserManagementController.php`, `Department`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `StoreTaskRequest`, `TaskManagementController`, `Illuminate\Support\Collection`, `Document`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Http\Request`, `ImportBatch`, `TaskManagementController.php`, `CalendarController.php`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `FileCategory.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Department.php`, `Role.php`, `Comment.php`, `TagsImportBatch.php`, `Subtask`, `Department`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `TaskManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Http\Request`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\JsonResponse`, `TaskManagementController.php`, `CalendarController.php`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._