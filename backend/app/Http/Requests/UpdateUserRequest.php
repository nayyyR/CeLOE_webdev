<?php

namespace App\Http\Requests;

use App\Models\Division;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['sometimes', 'exists:roles,id'],
            'division_id' => ['sometimes', 'exists:divisions,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $roleId = $this->input('role_id', $this->route('user')->role_id);
            $divisionId = $this->input('division_id', $this->route('user')->division_id);

            $role = Role::find($roleId);
            $division = Division::find($divisionId);

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
