<?php

namespace Database\Factories;

use App\Models\Dorm;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'dorm_id' => Dorm::factory(),
            'date' => '2026-10-21',
            'session' => 'morning',
        ];
    }
}
