<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'notes'  => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'in:pending,confirmed,cancelled'],
        ];
    }
}
