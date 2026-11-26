<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\CleaningDuty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCleaningDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CleaningDuty::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'area' => ['required', 'string', 'max:255'],
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', Role::Student->value),
            ],
            'due_date' => ['required', 'date'],
        ];
    }
}
