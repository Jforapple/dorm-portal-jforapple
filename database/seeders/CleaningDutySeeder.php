<?php

namespace Database\Seeders;

use App\Models\CleaningDuty;
use App\Models\User;
use Illuminate\Database\Seeder;

class CleaningDutySeeder extends Seeder
{
    public function run(): void
    {
        $student1 = User::where('email', 'student1@example.com')->firstOrFail();
        $student2 = User::where('email', 'student2@example.com')->firstOrFail();
        $student3 = User::where('email', 'student3@example.com')->firstOrFail();

        CleaningDuty::create([
            'area' => 'Common room',
            'user_id' => $student1->id,
            'due_date' => '2026-12-05',
            'completed_at' => null,
        ]);

        CleaningDuty::create([
            'area' => 'Study area',
            'user_id' => $student2->id,
            'due_date' => '2026-11-25',
            'completed_at' => '2026-11-26 10:00:00',
        ]);

        CleaningDuty::create([
            'area' => 'Shared bathroom',
            'user_id' => $student3->id,
            'due_date' => '2026-11-25',
            'completed_at' => null,
        ]);
    }
}
