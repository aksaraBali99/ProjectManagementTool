# Graph Report - ProjectManagementTool  (2026-08-25)

## Corpus Check
- 273 files · ~88,125 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1110 nodes · 2600 edges · 154 communities (127 shown, 27 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 162 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `79fc9eea`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- UpdateUserRequest
- ImportValidator
- .boardOrganizationIds
- OrgMember
- Organization
- Illuminate\Database\Eloquent\Relations\HasMany
- composer.json
- require-dev
- scripts
- package.json
- Mermaid AI Skills
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
- app.js
- _password-input.blade.php
- ImportTemplateBuilder
- StoreUserRequest
- User
- Task
- Illuminate\Foundation\Http\FormRequest
- StoreProjectRequest
- setup
- Illuminate\Database\Eloquent\Model
- CommentPolicy
- _form.blade.php
- Controller
- TaskManagementController
- Role
- LoginRequest
- UserManagementController.php
- UpdateRoleRequest
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Task.php
- Illuminate\Http\Request
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Department
- config
- SubtaskPolicy
- CalendarController.php
- UpdateTaskPriorityColorsRequest
- Illuminate\Support\Collection
- Illuminate\Validation\Validator
- require
- ProjectPolicy
- UpdateDepartmentRequest
- UpdateProjectRequest
- psr-4
- keywords
- test
- NotificationDeliveryTest.php

## God Nodes (most connected - your core abstractions)
1. `User` - 152 edges
2. `Organization` - 90 edges
3. `Task` - 60 edges
4. `OrgMember` - 56 edges
5. `Role` - 44 edges
6. `Project` - 43 edges
7. `Department` - 42 edges
8. `ImportValidator` - 33 edges
9. `AuditLog` - 30 edges
10. `Controller` - 27 edges

## Surprising Connections (you probably didn't know these)
- `makeStaffOnCalendar()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/AccessPermission.php
- `makeStaffOnKanban()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Kanban/KanbanTest.php → app/Models/AccessPermission.php
- `makeStaffWithDepartmentAccess()` --calls--> `AccessPermission`  [INFERRED]
  tests/Feature/Tasks/TaskManagementTest.php → app/Models/AccessPermission.php
- `makeStaffOnCalendar()` --calls--> `Role`  [INFERRED]
  tests/Feature/Calendar/CalendarTest.php → app/Models/Role.php
- `makeStaffOnDashboard()` --calls--> `Role`  [INFERRED]
  tests/Feature/Dashboard/DashboardTest.php → app/Models/Role.php

## Import Cycles
- None detected.

## Communities (154 total, 27 thin omitted)

### Community 1 - "ImportValidator"
Cohesion: 0.13
Nodes (5): DuplicateDetector, EmployeeIdGenerator, ImportIdCodec, ImportValidationContext, ImportValidator

### Community 3 - "OrgMember"
Cohesion: 0.15
Nodes (14): OrgMember, Project, makeTaskForAnalytics(), makeStaffOnCalendar(), makeProjectMember(), makeTaskOnDashboard(), makeClientForDocumentList(), makeDocumentForList() (+6 more)

### Community 4 - "Organization"
Cohesion: 0.09
Nodes (9): AccessControlController, DepartmentManagementController, DocumentController, KanbanController, NotificationController, OrganizationManagementController, SettingsController, Organization (+1 more)

### Community 6 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 7 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+7 more)

### Community 9 - "package.json"
Cohesion: 0.07
Nodes (29): concurrently, @fontsource/inter, frappe-gantt, intl-tel-input, @laravel/multiplex, laravel-vite-plugin, dependencies, chart.js (+21 more)

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
Cohesion: 0.50
Nodes (3): tasks._comments, tasks._subtasks, tasks._documents

### Community 47 - "app.js"
Cohesion: 0.20
Nodes (12): buildDisplayRows(), CALENDAR_VIEW_DAYS, CHART_PALETTE, DAY_ABBREVIATIONS, DAY_VIEW_MODE_WITH_WEEKDAY, formatLocalDate(), formatPopupDate(), getCalendarViewStart() (+4 more)

### Community 50 - "ImportTemplateBuilder"
Cohesion: 0.11
Nodes (15): ImportController, ImportBatch, ImportSheetSchema, ImportSpreadsheetParser, ImportTemplateBuilder, Worksheet, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile (+7 more)

### Community 87 - "User"
Cohesion: 0.12
Nodes (7): User, AuditLogPolicy, OrganizationPolicy, UserPolicy, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 90 - "Task"
Cohesion: 0.05
Nodes (17): CommentController, SubtaskController, TaskDocumentController, isAssignableStaffForProject(), Comment, Document, Subtask, Task (+9 more)

### Community 91 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): UploadImportRequest, StoreOrganizationRequest, UpdateOrganizationRequest, UpdateUserPasswordRequest, Illuminate\Foundation\Http\FormRequest

### Community 93 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 101 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.23
Nodes (4): Permission, Illuminate\Database\Eloquent\Model, makeClientOnProject(), makeStaffWithDepartmentAccess()

### Community 107 - "Controller"
Cohesion: 0.25
Nodes (4): AnalyticsController, AuthenticatedSessionController, Controller, TaskColorController

### Community 109 - "Role"
Cohesion: 0.17
Nodes (5): RoleManagementController, Role, RolePolicy, Illuminate\Database\Eloquent\Builder, makeStaffForDocumentCreate()

### Community 110 - "LoginRequest"
Cohesion: 0.09
Nodes (13): EnsureBelongsToOrganization, EnsureUserIsActive, LoginRequest, bootBelongsToOrganization(), bootHidesInactiveFromNonAdmins(), ValidClientUser, ValidPhoneNumber, Closure (+5 more)

### Community 115 - "Task.php"
Cohesion: 0.09
Nodes (15): allColors(), badgeBackground(), badgeText(), colorRow(), self, values(), allColors(), badgeBackground() (+7 more)

### Community 116 - "Illuminate\Http\Request"
Cohesion: 0.18
Nodes (5): AuditTrailController, DashboardController, Collection, PermissionManagementController, Illuminate\Http\Request

### Community 117 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (3): GoogleAuthController, NotificationSettingsController, Illuminate\Http\RedirectResponse

### Community 118 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.05
Nodes (16): AuditLog, organization(), ImportRow, NotificationSetting, AuditEventDatabaseNotification, AuditEventMailNotification, NotificationSettingPolicy, AuditEventNotifier (+8 more)

### Community 119 - "Department"
Cohesion: 0.05
Nodes (14): StoreDepartmentRequest, StoreTaskRequest, UpdateTaskRequest, AccessPermission, Department, DepartmentPolicy, DatabaseSeeder, DepartmentSeeder (+6 more)

### Community 120 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 139 - "Illuminate\Validation\Validator"
Cohesion: 0.19
Nodes (5): UpdateTaskStatusColorsRequest, validateCompanyRoles(), validateSuperAdminGrant(), CompanyRoleRules, Illuminate\Validation\Validator

### Community 140 - "require"
Cohesion: 0.29
Nodes (7): require, giggsey/libphonenumber-for-php, laravel/framework, laravel/socialite, laravel/tinker, php, phpoffice/phpspreadsheet

### Community 146 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 148 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 149 - "test"
Cohesion: 0.67
Nodes (3): test, @php artisan config:clear --ansi @no_additional_args, @php artisan test

## Knowledge Gaps
- **108 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+103 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **27 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `ProjectPolicy`, `ImportTemplateBuilder`, `StoreUserRequest`, `Role.php`, `Task`, `Illuminate\Database\Eloquent\Model`, `CommentPolicy`, `Controller`, `Role`, `LoginRequest`, `UserManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Task.php`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Department`, `SubtaskPolicy`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Why does `Organization` connect `Organization` to `ImportValidator`, `.boardOrganizationIds`, `OrgMember`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Collection`, `Illuminate\Validation\Validator`, `ImportTemplateBuilder`, `User`, `Role.php`, `Task`, `Illuminate\Database\Eloquent\Model`, `Controller`, `TaskManagementController`, `Role`, `UserManagementController.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Task.php`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Department`, `CalendarController.php`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Why does `Task` connect `Task` to `ImportValidator`, `OrgMember`, `Organization`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Model`, `Controller`, `TaskManagementController`, `Role`, `Department.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Task.php`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Role.php`, `SubtaskPolicy`, `CalendarController.php`?**
  _High betweenness centrality (0.037) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `User` (e.g. with `.index()` and `.__invoke()`) actually correct?**
  _`User` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 16 inferred relationships involving `Organization` (e.g. with `.__invoke()` and `.index()`) actually correct?**
  _`Organization` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 7 inferred relationships involving `Task` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Task` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _108 weakly-connected nodes found - possible documentation gaps or missing edges._