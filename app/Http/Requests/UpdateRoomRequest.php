<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_number'  => ['required', 'string', 'max:20', Rule::unique('rooms', 'room_number')->ignore($this->room)],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'status'       => ['required', 'in:available,occupied,reserved,maintenance,dirty'],
            'floor'        => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
