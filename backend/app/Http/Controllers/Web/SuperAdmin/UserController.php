<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::with(['role', 'division']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = request('role_id')) {
            $query->where('role_id', $roleId);
        }

        if ($divisionId = request('division_id')) {
            $query->where('division_id', $divisionId);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'roles', 'divisions'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('superadmin.users.create', compact('roles', 'divisions'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('superadmin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load(['role', 'division']);

        return view('superadmin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load(['role', 'division']);
        $roles = Role::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        return view('superadmin.users.edit', compact('user', 'roles', 'divisions'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('superadmin.users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'User deleted successfully.');
    }
}
