<?php

namespace Database\Seeders;

use App\Models\RoomCheck;
use Illuminate\Database\Seeder;

/**
 * Inserts 100 random room inspections.
 */
class RoomCheckSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        RoomCheck::factory()->count(100)->create();
    }
}
