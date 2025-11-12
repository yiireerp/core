<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - using JWT authentication
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/switch-organization', [AuthController::class, 'switchOrganization']);
    
    // User profile management
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/avatar', [UserController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [UserController::class, 'deleteAvatar']);
    Route::put('/profile/password', [UserController::class, 'changePassword']);
    Route::put('/profile/preferences', [UserController::class, 'updatePreferences']);

    // Tenant management routes
    Route::prefix('tenants')->group(function () {
        Route::get('/', [TenantController::class, 'index']);
        Route::post('/', [TenantController::class, 'store']);
        Route::get('/current', [TenantController::class, 'current']);
        Route::get('/{id}', [TenantController::class, 'show']);
        Route::put('/{id}', [TenantController::class, 'update']);
        Route::delete('/{id}', [TenantController::class, 'destroy']);
        
        // Tenant user management
        Route::post('/{id}/add-user', [TenantController::class, 'addUser']);
        Route::post('/{id}/remove-user', [TenantController::class, 'removeUser']);
        Route::get('/{id}/context', [TenantController::class, 'userContext']);
    });

    // Tenant-scoped routes (require tenant context)
    Route::middleware(['tenant'])->group(function () {
        // User management routes (admin only in tenant)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{id}', [UserController::class, 'show']);
        });

        // Role management routes (admin only in tenant)
        Route::middleware(['role:admin'])->prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::put('/{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'destroy']);
            
            // Assign/remove roles to users
            Route::post('/{id}/assign-user', [RoleController::class, 'assignToUser']);
            Route::post('/{id}/remove-user', [RoleController::class, 'removeFromUser']);
            
            // Assign/remove permissions to roles
            Route::post('/{id}/assign-permission', [RoleController::class, 'assignPermission']);
            Route::post('/{id}/remove-permission', [RoleController::class, 'removePermission']);
        });

        // Permission management routes (admin only in tenant)
        Route::middleware(['role:admin'])->prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{id}', [PermissionController::class, 'show']);
            Route::put('/{id}', [PermissionController::class, 'update']);
            Route::delete('/{id}', [PermissionController::class, 'destroy']);
            
            // Assign/remove permissions directly to users
            Route::post('/{id}/assign-user', [PermissionController::class, 'assignToUser']);
            Route::post('/{id}/remove-user', [PermissionController::class, 'removeFromUser']);
        });
    });
});

