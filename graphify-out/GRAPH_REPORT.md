# Graph Report - ProjectManagementTool  (2026-09-26)

## Corpus Check
- 377 files · ~171,122 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1571 nodes · 3999 edges · 183 communities (148 shown, 35 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 253 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0cb775df`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- FileCategory.php
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Model
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- auth.php
- LARAVEL_README.md
- AppServiceProvider.php
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
- ProjectManagementController
- Document
- Organization
- setup
- documents/create.blade.php
- Illuminate\Database\Eloquent\Relations\HasMany
- RichText
- _form.blade.php
- BootstrapEnvironment
- Illuminate\View\View
- CodeLanguageClassSanitizer
- Illuminate\Support\Collection
- ImportBatch
- LinkPreviewService.php
- Comment
- Illuminate\Http\Request
- Illuminate\Http\UploadedFile
- AuditEventNotifier
- OrgMember
- LoginRequest
- config
- NotificationEventType.php
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- AuditLog
- NotificationSetting
- require
- Illuminate\Foundation\Http\FormRequest
- TagsImportBatch.php
- psr-4
- Task
- Subtask
- Illuminate\Validation\Validator
- HasAdminConfigurableColors.php
- UpdateProjectRequest
- static
- UserManagementController
- UpdateTaskStatusColorsRequest
- StoreProjectRequest
- Department
- UpdateRoleRequest
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- CommentMentionHighlightTest.php
- Role
- keywords
- Illuminate\Database\Eloquent\Relations\BelongsToMany

## God Nodes (most connected - your core abstractions)
1. `User` - 217 edges
2. `Organization` - 123 edges
3. `OrgMember` - 102 edges
4. `Task` - 102 edges
5. `Project` - 73 edges
6. `Role` - 63 edges
7. `Department` - 58 edges
8. `ImportValidator` - 42 edges
9. `AuditLog` - 34 edges
10. `Controller` - 33 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeEligibleStaffMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php
- `makeIneligibleProjectMember()` --calls--> `Role`  [INFERRED]
  tests/Feature/Comments/CommentMentionEligibilityTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (183 total, 35 thin omitted)

### Community 0 - "FileCategory.php"
Cohesion: 0.23
Nodes (6): PastedMediaNamer, Closure, RuntimeException, fileFor(), FileCategory, UploadedFile

### Community 1 - "ImportValidator"
Cohesion: 0.10
Nodes (7): DuplicateDetector, EmployeeIdGenerator, ImportFieldResolver, ImportIdCodec, ImportValidationContext, ImportValidator, Carbon\Carbon

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.21
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, RoleSeeder, UserSeeder, Illuminate\Database\Seeder

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (8): CommentReaction, organization(), ImportRow, PastedMedia, TaskPriorityColor, TaskStatusColor, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

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

### Community 11 - "auth.php"
Cohesion: 0.27
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
Cohesion: 0.20
Nodes (7): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, downloadTemplateSpreadsheet()

### Community 86 - "StoreTaskRequest"
Cohesion: 0.14
Nodes (3): StoreTaskRequest, UpdateTaskRequest, Illuminate\Contracts\Validation\Validator

### Community 87 - "User"
Cohesion: 0.05
Nodes (15): User, AuditLogPolicy, CommentPolicy, DepartmentPolicy, DocumentPolicy, OrganizationPolicy, ProjectPolicy, SubtaskPolicy (+7 more)

### Community 91 - "Document"
Cohesion: 0.14
Nodes (4): DocumentController, Document, Symfony\Component\HttpFoundation\StreamedResponse, uploadDocumentForDownload()

### Community 92 - "Organization"
Cohesion: 0.10
Nodes (19): isAssignableStaffForProject(), Organization, Project, makeTaskForAnalytics(), makeReactionClient(), makeStaffOnDashboard(), makeTaskOnDashboard(), makeClientWithProjectAccessForDownload() (+11 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 107 - "BootstrapEnvironment"
Cohesion: 0.11
Nodes (6): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, config(), prefix(), Illuminate\Console\Command

### Community 108 - "Illuminate\View\View"
Cohesion: 0.09
Nodes (12): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, ImportController, NotificationController (+4 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 110 - "Illuminate\Support\Collection"
Cohesion: 0.17
Nodes (10): CalendarController, staffOptionsByProject(), resolveCurrentOrganization(), DashboardController, Collection, KanbanController, Carbon, Illuminate\Support\Carbon (+2 more)

### Community 111 - "ImportBatch"
Cohesion: 0.19
Nodes (6): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary

### Community 112 - "LinkPreviewService.php"
Cohesion: 0.10
Nodes (7): LinkPreviewController, LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 114 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (9): CommentReactionController, RichTextAudioController, RichTextDocumentController, RichTextImageController, RichTextVideoController, SubtaskController, TaskDocumentController, Illuminate\Http\JsonResponse (+1 more)

### Community 115 - "Illuminate\Http\UploadedFile"
Cohesion: 0.06
Nodes (30): FileStorageException, self, FileStorageService, StoredFile, DateTimeInterface, DOMDocument, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile (+22 more)

### Community 118 - "OrgMember"
Cohesion: 0.17
Nodes (4): OrgMember, Illuminate\Support\Facades\Notification, makeStaffForDocumentCreate(), joinOrg()

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 134 - "AuditLog"
Cohesion: 0.12
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 139 - "NotificationSetting"
Cohesion: 0.17
Nodes (4): NotificationSettingsController, NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 143 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UpdateDepartmentRequest, UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 145 - "TagsImportBatch.php"
Cohesion: 0.29
Nodes (4): CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.15
Nodes (10): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription() (+2 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.13
Nodes (6): UpdateTaskPriorityColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "UpdateProjectRequest"
Cohesion: 0.16
Nodes (5): UpdateProjectRequest, ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 158 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 166 - "Department"
Cohesion: 0.09
Nodes (15): DepartmentManagementController, StoreDepartmentRequest, AccessPermission, Department, makeStaffOnCalendar(), makeEligibleStaffMember(), makeIneligibleProjectMember(), makeProjectMember() (+7 more)

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 178 - "Role"
Cohesion: 0.12
Nodes (7): RoleManagementController, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, toggleStaffManageDocuments(), makeClientWithProjectAccessForLinkPreview(), makeClientOnProject()

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 184 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.14
Nodes (3): Permission, PermissionSeeder, Illuminate\Database\Eloquent\Relations\BelongsToMany

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `AuditLog`, `Task.php`, `NotificationSetting`, `User.php`, `Role.php`, `Subtask`, `UpdateProjectRequest`, `UserManagementController`, `Department`, `Role`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `TaskManagementController`, `ProjectManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `BootstrapEnvironment`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\UploadedFile`, `AuditEventNotifier`, `OrgMember`, `LoginRequest`, `NotificationEventType.php`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Task.php`, `User.php`, `Role.php`, `Illuminate\Validation\Validator`, `UserManagementController`, `Department`, `ImportTemplateBuilder`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `TaskManagementController`, `ProjectManagementController`, `Document`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `Illuminate\Http\Request`, `OrgMember`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `FileCategory.php`, `ImportValidator`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\RedirectResponse`, `Task.php`, `Role.php`, `Subtask`, `Department`, `Role`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `User`, `TaskManagementController`, `Document`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `RichText`, `Illuminate\View\View`, `Illuminate\Support\Collection`, `ImportBatch`, `LinkPreviewService.php`, `Comment`, `Illuminate\Http\Request`, `OrgMember`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Project` (e.g. with `.resolvePending()` and `.storePending()`) actually correct?**
  _`Project` has 10 INFERRED edges - model-reasoned connections that need verification._