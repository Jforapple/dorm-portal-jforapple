<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use App\Enums\Role;
use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarkAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Attendance::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'attendances' => ['required', 'array', 'min:1'],

            'attendances.*.user_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id')
                    ->where('role', Role::Student->value),
            ],

            'attendances.*.status' => [
                'required',
                Rule::enum(AttendanceStatus::class),
            ],
        ];
    }
}
