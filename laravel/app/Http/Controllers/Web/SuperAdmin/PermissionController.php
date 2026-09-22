<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::withCount('roles')->latest()->paginate(10);

        return view('superadmin.permissions.index', compact('permissions'));
    }

    public function show(Permission $permission): View
    {
        $permission->load('roles');

        return view('superadmin.permissions.show', compact('permission'));
    }
}
