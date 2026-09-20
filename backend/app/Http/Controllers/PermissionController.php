<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\SyncRolePermissionsRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(
            data: Permission::with('roles')->get(),
            message: 'Permissions retrieved successfully.',
        );
    }

    public function show(Permission $permission): JsonResponse
    {
        return $this->successResponse(
            data: $permission->load('roles'),
            message: 'Permission retrieved successfully.',
        );
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $permission = Permission::create($data);

        return $this->successResponse(
            data: $permission,
            message: 'Permission created successfully.',
            code: 201,
        );
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $data = $request->validated();

        $permission->update($data);

        return $this->successResponse(
            data: $permission,
            message: 'Permission updated successfully.',
        );
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return $this->successResponse(message: 'Permission deleted successfully.');
    }

    public function syncRolePermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $data = $request->validated();

        $role->permissions()->sync($data['permission_ids']);

        return $this->successResponse(
            data: $role->load('permissions'),
            message: 'Role permissions updated successfully.',
        );
    }
}
