<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_number'  => ['required', 'string', 'max:20', 'unique:rooms,room_number'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'status'       => ['required', 'in:available,occupied,reserved,maintenance,dirty'],
            'floor'        => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
