<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomTypeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100', Rule::unique('room_types', 'name')->ignore($this->room_type)],
            'base_rate'   => ['required', 'numeric', 'min:0'],
            'capacity'    => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
