<?php

namespace App\Http\Requests;

use App\Models\RoomCheck;
use Illuminate\Foundation\Http\FormRequest;

class RoomCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roomCheckId = $this->route('roomcheck');

        if ($roomCheckId !== null) {
            RoomCheck::findOrFail($roomCheckId);
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer'],
            'student_id' => ['required', 'integer'],
            'student_name' => ['required', 'string', 'max:255'],
        ];
    }
}
