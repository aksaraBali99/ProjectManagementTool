<?php

use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentManagementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\LinkPreviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\OrganizationManagementController;
use App\Http\Controllers\PermissionManagementController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\RichTextAudioController;
use App\Http\Controllers\RichTextDocumentController;
use App\Http\Controllers\RichTextImageController;
use App\Http\Controllers\RichTextVideoController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskColorController;
use App\Http\Controllers\TaskDocumentController;
use App\Http\Controllers\TaskManagementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard/{organization?}', DashboardController::class)->name('dashboard');
    Route::get('/kanban/{organization?}', KanbanController::class)->name('kanban');
    Route::get('/calendar/{organization?}', CalendarController::class)->name('calendar');
    Route::get('/settings', SettingsController::class)->name('settings.index');
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');
    Route::get('/analytics', AnalyticsController::class)->name('analytics.index');
    Route::get('/task-colors', [TaskColorController::class, 'edit'])->name('task-colors.edit');
    Route::put('/task-colors/status', [TaskColorController::class, 'updateStatus'])->name('task-colors.update-status');
    Route::put('/task-colors/priority', [TaskColorController::class, 'updatePriority'])->name('task-colors.update-priority');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::get('/notification-settings', [NotificationSettingsController::class, 'index'])->name('notification-settings.index');
    Route::put('/notification-settings/mine', [NotificationSettingsController::class, 'updateMine'])->name('notification-settings.update-mine');
    Route::post('/notification-settings/rules', [NotificationSettingsController::class, 'storeRule'])->name('notification-settings.rules.store');
    Route::patch('/notification-settings/rules/{notificationSetting}/toggle', [NotificationSettingsController::class, 'toggleRule'])->name('notification-settings.rules.toggle');
    Route::delete('/notification-settings/rules/{notificationSetting}', [NotificationSettingsController::class, 'destroyRule'])->name('notification-settings.rules.destroy');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [UserManagementController::class, 'updatePassword'])->name('users.password.update');
    Route::patch('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('/organizations', [OrganizationManagementController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizationManagementController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationManagementController::class, 'store'])->name('organizations.store');
    Route::get('/organizations/{organization}/edit', [OrganizationManagementController::class, 'edit'])->name('organizations.edit');
    Route::put('/organizations/{organization}', [OrganizationManagementController::class, 'update'])->name('organizations.update');
    Route::patch('/organizations/{organization}/toggle-active', [OrganizationManagementController::class, 'toggleActive'])->name('organizations.toggle-active');

    Route::get('/departments/create', [DepartmentManagementController::class, 'create'])->name('departments.create');
    Route::post('/departments', [DepartmentManagementController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentManagementController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentManagementController::class, 'update'])->name('departments.update');
    Route::patch('/departments/{department}/toggle-active', [DepartmentManagementController::class, 'toggleActive'])->name('departments.toggle-active');
    Route::get('/departments/{organization?}', [DepartmentManagementController::class, 'index'])->name('departments.index');

    Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
    // Must stay registered before /roles/{role} below — both are
    // single-role-param-shaped, and Laravel matches in registration order,
    // so /roles/permissions would otherwise be swallowed as an attempt to
    // route-model-bind a Role with route key "permissions".
    Route::get('/roles/permissions', [PermissionManagementController::class, 'edit'])->name('roles.permissions.edit');
    Route::put('/roles/permissions', [PermissionManagementController::class, 'update'])->name('roles.permissions.update');
    Route::get('/roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');

    Route::get('/access-control/{organization?}', [AccessControlController::class, 'index'])->name('access-control.index');
    Route::post('/access-control/toggle', [AccessControlController::class, 'toggle'])->name('access-control.toggle');

    Route::get('/projects/create/{organization?}', [ProjectManagementController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectManagementController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectManagementController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectManagementController::class, 'update'])->name('projects.update');
    Route::get('/projects/{project}/template', [ProjectManagementController::class, 'template'])->name('projects.template');
    Route::get('/projects/{organization?}', [ProjectManagementController::class, 'index'])->name('projects.index');

    Route::get('/tasks/create/{project?}', [TaskManagementController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskManagementController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskManagementController::class, 'edit'])->name('tasks.edit')->withTrashed();
    Route::put('/tasks/{task}', [TaskManagementController::class, 'update'])->name('tasks.update')->withTrashed();
    Route::patch('/tasks/{task}/toggle-active', [TaskManagementController::class, 'toggleActive'])->name('tasks.toggle-active')->withTrashed();
    Route::patch('/tasks/{task}/status', [TaskManagementController::class, 'updateStatus'])->name('tasks.update-status');
    Route::patch('/tasks/{task}/assignee', [TaskManagementController::class, 'updateAssignee'])->name('tasks.update-assignee');

    // ->withTrashed(): matches tasks.edit/update above — a deactivated
    // task's Description is still reachable/editable there, so its image
    // button must keep working too.
    Route::post('/tasks/{task}/images', [RichTextImageController::class, 'store'])->name('tasks.images.store')->withTrashed();
    // Not nested under /tasks/{task} — the Add Task page has no task yet;
    // see RichTextImageController::storePending().
    Route::post('/pending-task-images', [RichTextImageController::class, 'storePending'])->name('tasks.images.store-pending');

    // task #4 phase 4 — mirrors the two image routes above exactly.
    Route::post('/tasks/{task}/audio', [RichTextAudioController::class, 'store'])->name('tasks.audio.store')->withTrashed();
    Route::post('/pending-task-audio', [RichTextAudioController::class, 'storePending'])->name('tasks.audio.store-pending');

    // task #4 phase 5 — mirrors the two audio routes above exactly.
    Route::post('/tasks/{task}/video', [RichTextVideoController::class, 'store'])->name('tasks.video.store')->withTrashed();
    Route::post('/pending-task-video', [RichTextVideoController::class, 'storePending'])->name('tasks.video.store-pending');

    // Document upload + embedding in the editor (task #4). Not
    // /tasks/{task}/documents — that path is already
    // task-documents.attach below (attaching an EXISTING document by id
    // via the picker); "document-uploads" disambiguates "upload a NEW
    // file from the editor" from that.
    Route::post('/tasks/{task}/document-uploads', [RichTextDocumentController::class, 'store'])->name('tasks.document-uploads.store')->withTrashed();
    Route::post('/pending-task-document-uploads', [RichTextDocumentController::class, 'storePending'])->name('tasks.document-uploads.store-pending');

    // Smart Links (task #4) — resolves a pasted URL to a title/image/
    // domain; see LinkPreviewController and LinkPreviewService for why
    // there's no separate reconciliation-on-save step the way documents
    // need one.
    Route::post('/tasks/{task}/link-previews', [LinkPreviewController::class, 'resolve'])->name('tasks.link-previews.resolve')->withTrashed();
    Route::post('/pending-task-link-previews', [LinkPreviewController::class, 'resolvePending'])->name('tasks.link-previews.resolve-pending');

    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::patch('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])->name('subtasks.toggle');
    Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');

    Route::get('/tasks/{task}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reactions', [CommentReactionController::class, 'store'])->name('comments.reactions.store');

    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/create/{organization?}', [DocumentController::class, 'create'])->name('documents.create');
    // A standalone top-level path, not /documents/download — the latter
    // would hit the same "swallowed by the optional-segment route below"
    // problem /documents/create already has to dodge with ordering, and
    // this one's real identifier is a `url` query value, not a path
    // segment, so it never needed to live under /documents/ at all.
    Route::get('/file-downloads', [DocumentController::class, 'download'])->name('file-downloads.show');
    // Must stay registered after /documents/create/{organization?} above —
    // both are single-optional-segment GET routes, and Laravel matches in
    // registration order, so /documents/create would otherwise be
    // swallowed as an attempt to bind an Organization with route key
    // "create" (same ordering Tasks/Projects/Departments rely on).
    Route::get('/documents/{organization?}', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/tasks/{task}/documents', [TaskDocumentController::class, 'attach'])->name('task-documents.attach');
    Route::delete('/tasks/{task}/documents/{document}', [TaskDocumentController::class, 'detach'])->name('task-documents.detach');

    // Must stay registered after /tasks/create/{project?} above — both are
    // single-optional-segment GET routes, and Laravel matches in
    // registration order, so /tasks/create would otherwise be swallowed by
    // {organization?} here (same ordering Departments/Projects rely on).
    Route::get('/tasks/{organization?}', [TaskManagementController::class, 'index'])->name('tasks.index');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import/upload', [ImportController::class, 'upload'])->name('import.upload');
    Route::get('/import/{batch}/review', [ImportController::class, 'review'])->name('import.review');
    Route::post('/import/{batch}/commit', [ImportController::class, 'commit'])->name('import.commit');
});
