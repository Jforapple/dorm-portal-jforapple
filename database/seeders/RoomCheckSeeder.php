<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Dorm;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomCheckSeeder extends Seeder
{
    public function run(): void
    {
        $staff1 = User::create([
            'name' => 'Staff 1',
            'email' => 'staff1@example.com',
            'password' => 'password',
            'role' => Role::Staff,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Staff 2',
            'email' => 'staff2@example.com',
            'password' => 'password',
            'role' => Role::Staff,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@example.com',
            'password' => 'password',
            'role' => Role::Admin,
            'email_verified_at' => now(),
        ]);

        $studentNumber = 1;

        $dorms = [
            'Eve Hall' => [
                '101' => 4,
                '102' => 4,
                '103' => 4,
            ],
            'Esther Hall' => ['101' => 2],
            'Ruth Hall' => ['101' => 2],
            'Elijah Hall' => ['101' => 2],
            'Solomon Hall' => ['101' => 2],
            'Sarah Hall' => ['101' => 2],
        ];

        foreach ($dorms as $dormName => $roomAssignments) {
            $dorm = Dorm::where('name', $dormName)->firstOrFail();

            foreach ($roomAssignments as $roomNumber => $studentCount) {
                $room = $dorm->rooms()
                    ->where('number', $roomNumber)
                    ->firstOrFail();

                for ($i = 0; $i < $studentCount; $i++) {
                    $student = User::create([
                        'name' => "Student {$studentNumber}",
                        'email' => "student{$studentNumber}@example.com",
                        'password' => 'password',
                        'role' => Role::Student,
                        'room_id' => $room->id,
                        'email_verified_at' => now(),
                    ]);

                    RoomCheck::create([
                        'room_id' => $room->id,
                        'student_id' => $student->id,
                        'inspector_id' => $staff1->id,
                        'inspection_date' => '2026-11-20',
                        'checklist' => [
                            'bed' => 'pass',
                            'floor' => 'pass',
                            'desk' => 'pass',
                            'trash' => 'pass',
                            'bathroom' => 'pass',
                        ],
                        'score' => 100,
                        'notes' => null,
                    ]);

                    $studentNumber++;
                }
            }
        }
    }
}
