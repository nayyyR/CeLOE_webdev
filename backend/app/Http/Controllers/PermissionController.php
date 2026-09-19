<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(): JsonResponse {
        return response()->json(
            Permission::with('roles')->get()
        );
    }

    public function show(Permission $permission): JsonResponse {
        return response()->json(
            $permission->load('roles')
        );
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $permission = Permission::create($data);

        return response()->json([
            'message' => 'Permission created successfully.',
            'permission' => $permission,
        ], 201);
    }

    public function update(Request $request, Permission $permission): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')
                    ->ignore($permission->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $permission->update($data);

        return response()->json([
            'message' => 'Permission updated successfully.',
            'permission' => $permission,
        ]);
    }

    public function destroy(Permission $permission): JsonResponse {
        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully.',
        ]);
    }

    public function syncRolePermissions(Request $request, Role $role): JsonResponse {
        $data = $request->validate([
            'permission_ids' => [
                'required',
                'array',
            ],

            'permission_ids.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $role->permissions()->sync(
            $data['permission_ids']
        );

        return response()->json([
            'message' => 'Role permissions updated successfully.',
            'role' => $role->load('permissions'),
        ]);
    }
}