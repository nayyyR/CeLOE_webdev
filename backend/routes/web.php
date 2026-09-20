<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Session-based (auth:web) routes for the Blade + Tailwind UI.
| API endpoints (auth:sanctum) remain untouched in routes/api.php.
|
*/

// Public: Login / Register
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');

// Root → redirect to login or dashboard
Route::get('/', fn () => redirect()->route('login'))->name('home');

// Authenticated: Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Role-Based Dashboard Routes
|--------------------------------------------------------------------------
|
| Each role group uses: auth + role.division middleware.
| The role.division middleware enforces the strict RBAC matrix (1a-1e).
|
*/

// User Dashboard
Route::prefix('user')->middleware(['auth', 'role.division'])->name('user.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Employee Dashboard
Route::prefix('employee')->middleware(['auth', 'role.division'])->name('employee.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Admin Dashboard
Route::prefix('admin')->middleware(['auth', 'role.division'])->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Super Admin Dashboard + Management
Route::prefix('super-admin')->middleware(['auth', 'role.division'])->name('superadmin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/users', fn () => view('superadmin.placeholder', ['section' => 'Users']))->name('users.index');
    Route::get('/roles', fn () => view('superadmin.placeholder', ['section' => 'Roles']))->name('roles.index');
    Route::get('/divisions', fn () => view('superadmin.placeholder', ['section' => 'Divisions']))->name('divisions.index');
    Route::get('/permissions', fn () => view('superadmin.placeholder', ['section' => 'Permissions']))->name('permissions.index');
    Route::get('/tickets', fn () => view('superadmin.placeholder', ['section' => 'Tickets']))->name('tickets.index');
});
