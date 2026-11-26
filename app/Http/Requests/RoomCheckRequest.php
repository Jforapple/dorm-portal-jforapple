<?php

namespace App\Http\Requests;

use App\Models\RoomCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $roomCheck = $this->route('roomcheck');

        if ($roomCheck instanceof RoomCheck) {
            return $user->can('update', $roomCheck);
        }

        return $user->can('create', RoomCheck::class);
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

            'inspection_date' => [
                'required',
                'date',
            ],

            'checklist' => [
                'required',
                'array:bed,floor,desk,trash,bathroom',
            ],

            'checklist.bed' => [
                'required',
                Rule::in(['pass', 'fail']),
            ],

            'checklist.floor' => [
                'required',
                Rule::in(['pass', 'fail']),
            ],

            'checklist.desk' => [
                'required',
                Rule::in(['pass', 'fail']),
            ],

            'checklist.trash' => [
                'required',
                Rule::in(['pass', 'fail']),
            ],

            'checklist.bathroom' => [
                'required',
                Rule::in(['pass', 'fail']),
            ],

            'score' => [
                'required',
                'integer',
                'between:0,100',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
