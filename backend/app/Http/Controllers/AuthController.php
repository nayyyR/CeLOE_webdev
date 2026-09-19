<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse {
        $userRole = Role::where('name', 'User')->firstOrFail();

        $generalDivision = Division::where('name', 'general')->firstOrFail();

        $baseUsername = Str::before($request->email, '@');

        $username = $baseUsername;

        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }

        $user = User::create([
            'name' => $request->name,

            'username' => $username,

            'email' => $request->email,

            'password' => $request->password,

            'role_id' => $userRole->id,

            'division_id' => $generalDivision->id,
        ]);

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user->load([
                'role',
                'division',
            ]),
        ], 201);
    }

    public function login(Request $request): JsonResponse {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with([
            'role',
            'division',
        ])
            ->where('email', $credentials['login'])
            ->orWhere('username', $credentials['login'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $dashboard = $this->resolveDashboard($user);

        if ($dashboard === null) {
            return response()->json([
                'message' => 'Your role and division combination is not authorized.',
            ], 403);
        }

        $token = $user->createToken('ticketing-api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'dashboard' => $dashboard,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    private function resolveDashboard(User $user): ?string {
        $role = strtolower($user->role->name);
        $division = strtolower($user->division->name);

        return match (true) {
            $role === 'user' && $division === 'general' => 'user',
            $role === 'employee' && $division !== 'general' => 'employee',
            $role === 'admin' && $division === 'general' => 'admin',
            $role === 'super admin' && $division === 'general' => 'superadmin',
            default => null,
        };
    }
}