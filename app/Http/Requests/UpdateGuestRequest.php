<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuestRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:30'],
            'email'       => ['nullable', 'email', 'max:255'],
            'id_number'   => ['required', 'string', 'max:50'],
            'nationality' => ['required', 'string', 'max:100'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }
}
