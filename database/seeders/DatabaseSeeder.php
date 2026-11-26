<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DormSeeder::class,
            RoomSeeder::class,
            RoomCheckSeeder::class,
            EventSeeder::class,
            CleaningDutySeeder::class,
        ]);
    }
}
