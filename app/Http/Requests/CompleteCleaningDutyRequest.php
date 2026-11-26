<?php

namespace App\Http\Requests;

use App\Models\CleaningDuty;
use Illuminate\Foundation\Http\FormRequest;

class CompleteCleaningDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cleaningDuty = $this->route('cleaning_duty');

        return $cleaningDuty instanceof CleaningDuty
            && ($this->user()?->can('complete', $cleaningDuty) ?? false);
    }

    public function rules(): array
    {
        return [];
    }
}
