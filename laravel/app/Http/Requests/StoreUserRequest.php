<?php

namespace App\Http\Requests;

use App\Models\Division;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'division_id' => ['required', 'exists:divisions,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $role = Role::find($this->input('role_id'));
            $division = Division::find($this->input('division_id'));

            if (! $role || ! $division) {
                return;
            }

            $roleName = strtolower($role->name);
            $divisionName = strtolower($division->name);

            $valid = match (true) {
                $roleName === 'user' && $divisionName === 'general' => true,
                $roleName === 'employee' && $divisionName !== 'general' => true,
                $roleName === 'admin' && $divisionName === 'general' => true,
                $roleName === 'super admin' && $divisionName === 'general' => true,
                default => false,
            };

            if (! $valid) {
                $validator->errors()->add(
                    'role_id',
                    'The selected role and division combination is not authorized.'
                );
            }
        });
    }
}
