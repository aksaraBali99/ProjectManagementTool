# Graph Report - ProjectManagementTool  (2026-09-24)

## Corpus Check
- 359 files · ~154,585 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1531 nodes · 3735 edges · 195 communities (146 shown, 49 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 235 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `539f45e3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- ImportValidator
- .boardOrganizationIds
- Illuminate\Database\Seeder
- NotificationSettingsResolver
- OrgMember
- composer.json
- require-dev
- scripts
- dependencies
- Mermaid AI Skills
- App\Enums\FileCategory
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
- FileStorageException
- App\Models\Task
- Illuminate\Database\Eloquent\Relations\HasMany
- setup
- documents/create.blade.php
- User
- static
- _form.blade.php
- Pest.php
- Illuminate\View\View
- CodeLanguageClassSanitizer
- LoginRequest
- ImportBatch
- LinkPreview
- Department
- Illuminate\Http\JsonResponse
- ProjectManagementController.php
- UserManagementController
- web.php
- TaskManagementController
- config
- Illuminate\Database\Eloquent\Model
- task-colors/edit.blade.php
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Builder
- require
- App\Models\User
- Document
- Comment
- psr-4
- Task
- Illuminate\Http\UploadedFile
- Illuminate\Validation\Validator
- Role
- HasAdminConfigurableColors.php
- Illuminate\Contracts\Validation\ValidationRule
- OrganizationPolicy
- Project
- UpdateTaskStatusColorsRequest
- StoreTaskRequest
- Subtask
- Illuminate\Http\Request
- StoreProjectRequest
- Organization
- UpdateUserRequest
- 2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php
- AuditEventNotifier
- Illuminate\Foundation\Http\FormRequest
- self
- Illuminate\Support\Collection
- DepartmentManagementController.php
- keywords
- UpdateDepartmentRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- NotificationSetting
- AuditLogPolicy.php
- UpdateProjectRequest
- App\Services\StoredFile
- UpdateTaskRequest
- ProjectPolicy
- SubtaskPolicy
- UpdateUserPasswordRequest
- DocumentPolicy

## God Nodes (most connected - your core abstractions)
1. `User` - 186 edges
2. `Organization` - 97 edges
3. `Task` - 80 edges
4. `OrgMember` - 71 edges
5. `Project` - 58 edges
6. `Role` - 52 edges
7. `Department` - 45 edges
8. `ImportValidator` - 41 edges
9. `AuditLog` - 34 edges
10. `ImportBatch` - 30 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnDashboard()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/AccessPermission.php
- `givePersonalTaskAssignedRule()` --calls--> `NotificationSetting`  [INFERRED]
  tests/Feature/Notifications/NotificationDeliveryTest.php → app/Models/NotificationSetting.php
- `makeClientWithProjectAccessForDownload()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/Documents/DocumentDownloadTest.php → app/Models/OrgMember.php
- `makeClientWithProjectAccessForAudio()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/RichText/AudioUploadTest.php → app/Models/OrgMember.php
- `makeClientWithProjectAccessForDescription()` --calls--> `OrgMember`  [INFERRED]
  tests/Feature/RichText/DescriptionViewEditModeTest.php → app/Models/OrgMember.php

## Import Cycles
- None detected.

## Communities (195 total, 49 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.11
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): DatabaseSeeder, DepartmentSeeder, OrganizationSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 5 - "OrgMember"
Cohesion: 0.22
Nodes (3): OrgMember, makeStaffForDocumentCreate(), joinOrg()

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

### Community 11 - "App\Enums\FileCategory"
Cohesion: 0.18
Nodes (8): App\Enums\FileCategory, config(), prefix(), PastedMediaNamer, RuntimeException, fileFor(), FileCategory, UploadedFile

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
Nodes (5): tasks._comments, tasks._description-field, tasks._documents, tasks._subtasks, users._unsaved-changes-guard

### Community 47 - "rich-text-editor.js"
Cohesion: 0.05
Nodes (54): CHART_PALETTE, highlightRichText(), initLightboxDelegation(), initRichText(), mountRichText(), richTextMounts, whenNearViewport(), Audio (+46 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.23
Nodes (6): ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, PhpOffice\PhpSpreadsheet\Spreadsheet, PhpOffice\PhpSpreadsheet\Worksheet\Worksheet

### Community 86 - "RichText"
Cohesion: 0.17
Nodes (3): CommentController, RichText, Symfony\Component\HtmlSanitizer\HtmlSanitizer

### Community 87 - "User"
Cohesion: 0.08
Nodes (11): User, CommentPolicy, DepartmentPolicy, TaskPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+3 more)

### Community 88 - "AuditLog"
Cohesion: 0.14
Nodes (7): AuditLog, AuditEventDatabaseNotification, AuditEventMailNotification, Illuminate\Bus\Queueable, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Notifications\Messages\MailMessage, Illuminate\Notifications\Notification

### Community 90 - "FileStorageException"
Cohesion: 0.29
Nodes (3): FileStorageException, self, Throwable

### Community 91 - "App\Models\Task"
Cohesion: 0.08
Nodes (9): App\Models\Task, TaskPriorityColor, Illuminate\Contracts\Validation\Validator, emojiTaskPayload(), altTextTaskUpdate(), imageResizeTaskPayload(), makeTaskWithDescription(), Task (+1 more)

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 105 - "static"
Cohesion: 0.24
Nodes (5): bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 107 - "Pest.php"
Cohesion: 0.07
Nodes (20): AbandonStaleImportBatches, BootstrapEnvironment, CleanupStalePendingMedia, App\Models\ImportBatch, DateTimeInterface, DOMDocument, DOMElement, Illuminate\Console\Command (+12 more)

### Community 108 - "Illuminate\View\View"
Cohesion: 0.08
Nodes (16): AccessControlController, AnalyticsController, AuditTrailController, AuthenticatedSessionController, GoogleAuthController, Controller, DocumentController, Organization (+8 more)

### Community 109 - "CodeLanguageClassSanitizer"
Cohesion: 0.24
Nodes (4): CodeLanguageClassSanitizer, MediaDimensionAttributeSanitizer, Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig, Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface

### Community 111 - "ImportBatch"
Cohesion: 0.15
Nodes (8): ImportBatch, CompanyRoleSyncer, ImportCommitResolution, ImportCommitService, Closure, ImportCommitSummary, ImportFieldResolver, Carbon\Carbon

### Community 112 - "LinkPreview"
Cohesion: 0.10
Nodes (6): LinkPreview, LinkPreviewResult, LinkPreviewService, OpenGraphMetadataParser, UrlSsrfGuard, DOMXPath

### Community 113 - "Department"
Cohesion: 0.20
Nodes (3): Department, makeStaffOnDashboard(), makeTaskOnDashboard()

### Community 114 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (7): LinkPreviewController, Task, RichTextImageController, Task, RichTextVideoController, SubtaskController, Illuminate\Http\JsonResponse

### Community 120 - "config"
Cohesion: 0.22
Nodes (9): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, platform, preferred-install, sort-packages (+1 more)

### Community 122 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.10
Nodes (10): App\Models\Concerns\BelongsToOrganization, organization(), App\Models\Document, ImportRow, PastedMedia, TaskStatusColor, Document, Illuminate\Database\Eloquent\Model (+2 more)

### Community 133 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): DepartmentManagementController, NotificationSettingsController, TaskColorController, Illuminate\Http\RedirectResponse

### Community 140 - "require"
Cohesion: 0.22
Nodes (9): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, league/flysystem-aws-s3-v3, php, phpoffice/phpspreadsheet (+1 more)

### Community 141 - "App\Models\User"
Cohesion: 0.08
Nodes (12): App\Models\Organization, App\Models\Project, App\Models\User, makeClientWithProjectAccessForDownload(), makeClientWithProjectAccessForAudio(), User, makeClientWithProjectAccessForDescription(), User (+4 more)

### Community 143 - "Document"
Cohesion: 0.24
Nodes (3): RichTextDocumentController, TaskDocumentController, Document

### Community 145 - "Comment"
Cohesion: 0.24
Nodes (5): Comment, CommentObserver, currentImportBatchId(), shouldSuppressNotification(), taggedChanges()

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 147 - "Task"
Cohesion: 0.21
Nodes (5): Task, MentionedInCommentNotification, TaskObserver, Illuminate\Database\Eloquent\SoftDeletes, videoResizeTaskPayload()

### Community 149 - "Illuminate\Http\UploadedFile"
Cohesion: 0.12
Nodes (15): FileStorageService, Task, Illuminate\Http\UploadedFile, StoredFile, fakeAudio(), UploadedFile, fakePastedImage(), fakePastedVideo() (+7 more)

### Community 151 - "Illuminate\Validation\Validator"
Cohesion: 0.18
Nodes (5): validateCompanyRoles(), validateSuperAdminGrant(), StoreUserRequest, CompanyRoleRules, Illuminate\Validation\Validator

### Community 155 - "Role"
Cohesion: 0.09
Nodes (16): AccessPermission, Role, RolePolicy, UserSeeder, makeStaffOnCalendar(), makeClientForDocumentList(), makeDocumentForList(), makeStaffForDocumentList() (+8 more)

### Community 156 - "HasAdminConfigurableColors.php"
Cohesion: 0.39
Nodes (6): allColors(), badgeBackground(), badgeText(), colorRow(), forgetColorCache(), self

### Community 157 - "Illuminate\Contracts\Validation\ValidationRule"
Cohesion: 0.25
Nodes (4): ValidClientUser, ValidPhoneNumber, ValidProjectStaffUser, Illuminate\Contracts\Validation\ValidationRule

### Community 159 - "Project"
Cohesion: 0.25
Nodes (5): isAssignableStaffForProject(), Project, makeTaskForAnalytics(), makeProjectMember(), makeTaskForStartDateTest()

### Community 166 - "Illuminate\Http\Request"
Cohesion: 0.25
Nodes (7): RichTextAudioController, EnsureBelongsToOrganization, EnsurePasswordHasBeenChanged, EnsureUserIsActive, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 171 - "2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php"
Cohesion: 0.83
Nodes (3): down(), isSqlite(), up()

### Community 174 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.11
Nodes (6): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateRoleRequest, UpdateTaskPriorityColorsRequest, Illuminate\Foundation\Http\FormRequest

### Community 178 - "Illuminate\Support\Collection"
Cohesion: 0.23
Nodes (9): CalendarController, App\Http\Controllers\Concerns\ResolvesCurrentOrganization, resolveCurrentOrganization(), DashboardController, Collection, Carbon, Illuminate\Support\Carbon, Illuminate\Support\Collection (+1 more)

### Community 180 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 185 - "NotificationSetting"
Cohesion: 0.21
Nodes (3): NotificationSetting, NotificationSettingPolicy, givePersonalTaskAssignedRule()

## Knowledge Gaps
- **133 isolated node(s):** `Diagram editing & preview`, `Docs`, `Generate diagrams (GitHub Copilot required)`, `Install / update this pack`, `LM Tools — call these for every diagram interaction` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `NotificationSettingsResolver`, `Illuminate\Http\RedirectResponse`, `OrgMember`, `Illuminate\Database\Eloquent\Builder`, `App\Models\User`, `Role.php`, `Document`, `Role`, `Illuminate\Contracts\Validation\ValidationRule`, `OrganizationPolicy`, `Project`, `Subtask`, `AuditEventNotifier`, `Illuminate\Support\Collection`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `NotificationSetting`, `AuditLogPolicy.php`, `ProjectPolicy`, `SubtaskPolicy`, `DocumentPolicy`, `RichText`, `AuditLog`, `App\Models\Task`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Pest.php`, `Illuminate\View\View`, `LoginRequest`, `ImportBatch`, `LinkPreview`, `Department`, `ProjectManagementController.php`, `UserManagementController`, `web.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.127) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `NotificationSettingsResolver`, `Subtask`, `Pest.php`, `Illuminate\View\View`, `AuditEventNotifier`, `ImportBatch`, `Comment`, `Task`, `NotificationEventType.php`, `NotificationSetting`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `Illuminate\Database\Seeder`, `OrgMember`, `Illuminate\Http\RedirectResponse`, `App\Models\User`, `Role.php`, `Illuminate\Validation\Validator`, `Role`, `OrganizationPolicy`, `Project`, `Illuminate\Support\Collection`, `DepartmentManagementController.php`, `ImportTemplateBuilder`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `App\Models\Task`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\View\View`, `ImportBatch`, `Department`, `ProjectManagementController.php`, `UserManagementController`, `web.php`, `TaskManagementController`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Are the 21 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 21 INFERRED edges - model-reasoned connections that need verification._
- **Are the 20 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 10 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `OrgMember` (e.g. with `makeClientWithProjectAccessForDownload()` and `makeClientWithProjectAccessForAudio()`) actually correct?**
  _`OrgMember` has 5 INFERRED edges - model-reasoned connections that need verification._