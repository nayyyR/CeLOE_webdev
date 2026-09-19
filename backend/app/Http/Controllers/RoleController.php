<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(): JsonResponse {
        return response()->json(
            Role::with('permissions')->get()
        );
    }

    public function show(Role $role): JsonResponse {
        return response()->json(
            $role->load('permissions')
        );
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
        ]);

        $role = Role::create($data);

        return response()->json([
            'message' => 'Role created successfully.',
            'role' => $role,
        ], 201);
    }

    public function update(Request $request, Role $role): JsonResponse {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
        ]);

        $role->update($data);

        return response()->json([
            'message' => 'Role updated successfully.',
            'role' => $role,
        ]);
    }

    public function destroy(Role $role): JsonResponse {
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully.',
        ]);
    }
}