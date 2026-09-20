<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => [
                'required',
                'string',
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

    public function messages(): array
    {
        return [
            'body.required' => 'Body wajib diisi.',
            'body.string' => 'Body harus berupa teks.',

            'attachments.array' => 'Attachments harus berupa array.',
            'attachments.max' => 'Maksimal 5 attachment.',

            'attachments.*.file' => 'Setiap attachment harus berupa file.',
            'attachments.*.max' => 'Ukuran setiap attachment maksimal 2 MB.',
            'attachments.*.mimes' => 'Format file yang diperbolehkan: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
        ];
    }
}
