<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse {
        return response()->json(
            User::with([
                'role',
                'division',
            ])->paginate(10)
        );
    }

    public function show(User $user): JsonResponse {
        return response()->json(
            $user->load([
                'role',
                'division',
            ])
        );
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'division_id' => ['required', 'exists:divisions,id'],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user->load(['role', 'division']),
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],

            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'username')
                    ->ignore($user->id),
            ],

            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'password' => [
                'sometimes',
                'string',
                'min:8',
            ],

            'role_id' => [
                'sometimes',
                'exists:roles,id',
            ],

            'division_id' => [
                'sometimes',
                'exists:divisions,id',
            ],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make(
                $data['password']
            );
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh([
                'role',
                'division',
            ]),
        ]);
    }

    public function destroy(User $user): JsonResponse {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }
}