<?php

namespace Database\Seeders;

use App\Models\Dorm;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomNumbers = ['101', '102', '103', '104', '105'];

        $dorms = Dorm::all();

        foreach ($dorms as $dorm) {
            foreach ($roomNumbers as $roomNumber) {
                $dorm->rooms()->firstOrCreate(
                    ['number' => $roomNumber],
                    ['capacity' => 4]
                );
            }
        }
    }
}
