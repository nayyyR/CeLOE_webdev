<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('permissions')->latest()->get();

        return view('superadmin.roles.index', compact('roles'));
    }

    public function show(Role $role): View
    {
        $role->load('permissions');

        return view('superadmin.roles.show', compact('role'));
    }
}
