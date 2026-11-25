<?php

namespace Database\Seeders;

use App\Models\Dorm;
use Illuminate\Database\Seeder;

class DormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dorms = [
            'Eve Hall',
            'Esther Hall',
            'Ruth Hall',
            'Elijah Hall',
            'Solomon Hall',
            'Sarah Hall',
        ];

        foreach ($dorms as $dorm) {
            Dorm::firstOrCreate([
                'name' => $dorm,
            ]);
        }
    }
}
