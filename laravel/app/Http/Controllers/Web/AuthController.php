<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt(['email' => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors([
                'login' => 'The provided credentials do not match our records.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $this->isValidRoleDivisionPair($user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Your role and division combination is not authorized. Please contact an administrator.');
        }

        return redirect()->intended($this->resolveDashboard());
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $username = Str::before($data['email'], '@');
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.$counter;
            $counter++;
        }

        $defaultRole = Role::where('name', 'User')->first();
        $defaultDivision = Division::where('name', 'general')->first();

        $user = User::create([
            'name' => $data['name'],
            'username' => $username,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $defaultRole->id,
            'division_id' => $defaultDivision->id,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended($this->resolveDashboard());
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }

    private function isValidRoleDivisionPair(User $user): bool
    {
        $roleName = strtolower($user->role->name ?? '');
        $divisionName = strtolower($user->division->name ?? '');

        return match (true) {
            $roleName === 'user' && $divisionName === 'general' => true,
            $roleName === 'employee' && $divisionName !== 'general' => true,
            $roleName === 'admin' && $divisionName === 'general' => true,
            $roleName === 'super admin' && $divisionName === 'general' => true,
            default => false,
        };
    }

    private function resolveDashboard(): string
    {
        $user = Auth::user();

        if (! $user || ! $user->role || ! $user->division) {
            return '/';
        }

        $roleName = strtolower($user->role->name);
        $divisionName = strtolower($user->division->name);

        return match (true) {
            $roleName === 'user' && $divisionName === 'general' => route('user.dashboard'),
            $roleName === 'employee' && $divisionName !== 'general' => route('employee.dashboard'),
            $roleName === 'admin' && $divisionName === 'general' => route('admin.dashboard'),
            $roleName === 'super admin' && $divisionName === 'general' => route('superadmin.dashboard'),
            default => '/login',
        };
    }
}
