<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMasterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register',]);
    Route::post('/login', [AuthController::class, 'login',]);
});

/*
|--------------------------------------------------------------------------
| Authenticated API
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role.division',])->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout',]);

    // USER
    Route::prefix('user')->middleware(['permission:create_tickets',])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index',]);
        Route::post('/tickets', [TicketController::class, 'store',]);
        Route::get('/tickets/{ticket}', [TicketController::class, 'show',])->missing(function () {
            return response()->json([
                'message' => 'Ticket not found.',
            ], 404);
        });
        Route::post('/tickets/{ticket}/threads', [TicketController::class, 'addThread',]);
        Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus',])->middleware(['permission:confirm_resolution',]);
    });

    // ADMIN
    Route::prefix('admin')->middleware(['permission:view_all_tickets',])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index',]);
        Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign',])->middleware(['permission:assign_tickets',]);
    });

    // EMPLOYEE
    Route::prefix('employee')->middleware(['permission:view_assigned_tickets',])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index',]);
        Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus',])->middleware(['permission:resolve_tickets',]);
        Route::post('/tickets/{ticket}/threads', [TicketController::class, 'addThread',])->middleware(['permission:reply_tickets',]);
    });

    // SUPER ADMIN
    Route::prefix('superadmin')->middleware(['permission:manage_users',])->group(function () {
        // Users
        Route::apiResource('users', UserController::class);

        // Roles
        Route::apiResource('roles', RoleController::class)->middleware(['permission:manage_roles',]);

        // Divisions
        Route::apiResource('divisions', DivisionController::class);

        // Permissions
        Route::apiResource('permissions', PermissionController::class)->middleware(['permission:manage_permissions',]);

        // Assign permissions to role
        Route::put('/roles/{role}/permissions', [PermissionController::class, 'syncRolePermissions',])->middleware(['permission:manage_permissions',]);

        // All tickets
        Route::apiResource('tickets', TicketMasterController::class);
    });
});