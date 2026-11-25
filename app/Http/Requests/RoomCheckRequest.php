<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => [
                'required',
                'integer',
                'exists:rooms,id',
            ],

            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'student')
                    ->where('room_id', $this->input('room_id')),
            ],
        ];
    }
}
