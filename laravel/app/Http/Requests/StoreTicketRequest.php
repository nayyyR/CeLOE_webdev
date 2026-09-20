<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'target_division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')
                    ->where(function ($query) {
                        $query->where('name', '!=', 'general');
                    }),
            ],

            'attachments' => [
                'nullable',
                'array',
                'max:5',
            ],

            'attachments.*' => [
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            ],
        ];
    }
}
