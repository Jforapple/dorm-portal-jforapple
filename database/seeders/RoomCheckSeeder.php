<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class RoomCheckSeeder extends Seeder
{
    public function run(): void
    {
        $remainingInspections = 100;

        $inspector = User::factory()->create([
            'role' => Role::Staff,
            'room_id' => null,
        ]);

        $rooms = Room::withCount('users')->get();

        foreach ($rooms as $room) {
            if ($remainingInspections === 0) {
                break;
            }

            $availableSlots = max(
                0,
                $room->capacity - $room->users_count
            );

            $studentsToCreate = min(
                $availableSlots,
                $remainingInspections
            );

            for ($i = 0; $i < $studentsToCreate; $i++) {
                $student = User::factory()->create([
                    'role' => Role::Student,
                    'room_id' => $room->id,
                ]);

                RoomCheck::factory()->create([
                    'room_id' => $room->id,
                    'student_id' => $student->id,
                    'inspector_id' => $inspector->id,
                ]);
            }

            $remainingInspections -= $studentsToCreate;
        }

        if ($remainingInspections > 0) {
            throw new RuntimeException(
                'Not enough room capacity to create 100 room inspections.'
            );
        }
    }
}
