<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(
            data: Role::with('permissions')->get(),
            message: 'Roles retrieved successfully.',
        );
    }

    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(
            data: $role->load('permissions'),
            message: 'Role retrieved successfully.',
        );
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $role = Role::create($data);

        return $this->successResponse(
            data: $role,
            message: 'Role created successfully.',
            code: 201,
        );
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $data = $request->validated();

        $role->update($data);

        return $this->successResponse(
            data: $role,
            message: 'Role updated successfully.',
        );
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return $this->successResponse(message: 'Role deleted successfully.');
    }
}
