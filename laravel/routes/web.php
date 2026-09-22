<?php

use App\Http\Controllers\Web\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Web\AttachmentController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Employee\TicketController as EmployeeTicketController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SuperAdmin\DivisionController;
use App\Http\Controllers\Web\SuperAdmin\PermissionController;
use App\Http\Controllers\Web\SuperAdmin\RoleController;
use App\Http\Controllers\Web\SuperAdmin\TicketController as SuperAdminTicketController;
use App\Http\Controllers\Web\SuperAdmin\UserController;
use App\Http\Controllers\Web\User\TicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $roleName = strtolower($user->role->name ?? '');
        $divisionName = strtolower($user->division->name ?? '');

        return match (true) {
            $roleName === 'user' && $divisionName === 'general' => redirect()->route('user.dashboard'),
            $roleName === 'employee' && $divisionName !== 'general' => redirect()->route('employee.dashboard'),
            $roleName === 'admin' && $divisionName === 'general' => redirect()->route('admin.dashboard'),
            $roleName === 'super admin' && $divisionName === 'general' => redirect()->route('superadmin.dashboard'),
            default => redirect()->route('login'),
        };
    }

    return redirect()->route('login');
})->name('home');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->name('profile.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}/attachments/{path}', [AttachmentController::class, 'show'])
        ->where('path', '.*')
        ->name('tickets.attachments.show');
});

Route::prefix('user')->middleware(['auth', 'role.division', 'role:user'])->name('user.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/replies', [TicketController::class, 'addThread'])->name('tickets.reply');
    Route::put('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
});

Route::prefix('employee')->middleware(['auth', 'role.division', 'role:employee'])->name('employee.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/tickets', [EmployeeTicketController::class, 'index'])->name('tickets.assigned');
    Route::get('/tickets/{ticket}', [EmployeeTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/replies', [EmployeeTicketController::class, 'addThread'])->name('tickets.reply');
    Route::put('/tickets/{ticket}/status', [EmployeeTicketController::class, 'updateStatus'])->name('tickets.status');
});

Route::prefix('admin')->middleware(['auth', 'role.division', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/replies', [AdminTicketController::class, 'addThread'])->name('tickets.reply');
    Route::put('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status');
});

Route::prefix('super-admin')->middleware(['auth', 'role.division', 'role:super admin'])->name('superadmin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');

    Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
    Route::get('/divisions/create', [DivisionController::class, 'create'])->name('divisions.create');
    Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
    Route::get('/divisions/{division}', [DivisionController::class, 'show'])->name('divisions.show');
    Route::get('/divisions/{division}/edit', [DivisionController::class, 'edit'])->name('divisions.edit');
    Route::put('/divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
    Route::delete('/divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');

    Route::get('/tickets', [SuperAdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [SuperAdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/assign', [SuperAdminTicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/replies', [SuperAdminTicketController::class, 'addThread'])->name('tickets.reply');
    Route::put('/tickets/{ticket}/status', [SuperAdminTicketController::class, 'updateStatus'])->name('tickets.status');
});
