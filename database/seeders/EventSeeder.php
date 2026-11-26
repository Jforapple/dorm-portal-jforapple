<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Dorm;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $eveHall = Dorm::where('name', 'Eve Hall')->firstOrFail();

        $event = Event::create([
            'title' => 'Eve Hall Worship Service',
            'dorm_id' => $eveHall->id,
            'date' => '2026-12-01',
            'session' => 'morning',
        ]);

        $students = User::where('role', 'student')
            ->whereHas('room', function ($query) use ($eveHall) {
                $query->where('dorm_id', $eveHall->id);
            })
            ->orderBy('name')
            ->get();

        foreach ($students as $index => $student) {
            $status = match ($index) {
                3 => AttendanceStatus::Late,
                7 => AttendanceStatus::Absent,
                11 => AttendanceStatus::Excused,
                default => AttendanceStatus::Present,
            };

            Attendance::create([
                'event_id' => $event->id,
                'user_id' => $student->id,
                'status' => $status,
            ]);
        }
    }
}
