<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->paginatedResponse(
            User::with(['role', 'division'])->paginate(10),
            'Users retrieved successfully.',
        );
    }

    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            data: $user->load(['role', 'division']),
            message: 'User retrieved successfully.',
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->successResponse(
            data: $user->load(['role', 'division']),
            message: 'User created successfully.',
            code: 201,
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->successResponse(
            data: $user->fresh(['role', 'division']),
            message: 'User updated successfully.',
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->successResponse(message: 'User deleted successfully.');
    }
}
