<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'priority' => [
                'sometimes',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
            'status' => [
                'sometimes',
                Rule::in(['open', 'in_progress', 'resolved', 'closed']),
            ],
            'target_division_id' => ['sometimes', 'exists:divisions,id'],
            'assigned_employee_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
