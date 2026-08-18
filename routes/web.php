<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentManagementController;
use App\Http\Controllers\OrganizationManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings', SettingsController::class)->name('settings.index');

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
    Route::delete('/organizations/{organization}', [OrganizationManagementController::class, 'destroy'])->name('organizations.destroy');

    Route::get('/departments', [DepartmentManagementController::class, 'index'])->name('departments.index');
    Route::get('/departments/create', [DepartmentManagementController::class, 'create'])->name('departments.create');
    Route::post('/departments', [DepartmentManagementController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentManagementController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentManagementController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentManagementController::class, 'destroy'])->name('departments.destroy');

    Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
});
