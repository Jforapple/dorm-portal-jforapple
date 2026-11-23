<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Support\ThailandHolidays;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class EventRequest extends FormRequest
{
    /**
     * Authorization is handled by EventPolicy in EventController.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Shared field-level rules for creating and updating events.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'dorm' => ['required', 'string', 'max:255', Rule::in(config('dorms'))],
            'date' => ['required', 'date_format:Y-m-d'],
            'session' => ['required', Rule::in(['morning', 'evening'])],
        ];
    }

    /**
     * Shared scheduling rules for creating and updating events.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            // Skip schedule checks when the basic date/session rules failed.
            if ($validator->errors()->has('date') || $validator->errors()->has('session')) {
                return;
            }

            // ISO weekday: 1 = Monday … 7 = Sunday.
            $day = CarbonImmutable::parse($this->input('date'))->dayOfWeekIso;

            if (ThailandHolidays::isHoliday($this->input('date'))) {
                $holiday = ThailandHolidays::DATES[$this->input('date')];
                $validator->errors()->add('date', "No worship on holidays: {$holiday}.");

                return;
            }

            // Weekly schedule: no weekends, Monday is evening, Tuesday–Friday is morning.
            if ($day >= 6) {
                $validator->errors()->add('date', 'There is no worship check-in on Saturday or Sunday.');
            } elseif (($day === 1 && $this->input('session') !== 'evening')
                || ($day >= 2 && $this->input('session') !== 'morning')) {
                $validator->errors()->add('session', 'Choose evening on Monday or morning on Tuesday through Friday.');
            }

            // One event per dorm per day, regardless of session or title.
            // Ignore the current event when updating it.
            $event = $this->route('event');

            $query = Event::query()
                ->where('dorm', $this->input('dorm'))
                ->whereDate('date', $this->input('date'));

            if ($event instanceof Event) {
                $query->whereKeyNot($event->getKey());
            }

            if ($validator->errors()->isEmpty() && $query->exists()) {
                $validator->errors()->add('date', 'This dorm already has a worship event on this date.');
            }
        }];
    }
}
