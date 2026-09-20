<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
            'status' => [
                'required',
                Rule::in(['open', 'in_progress', 'resolved', 'closed']),
            ],
            'created_by' => ['required', 'exists:users,id'],
            'target_division_id' => ['required', 'exists:divisions,id'],
            'assigned_employee_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
