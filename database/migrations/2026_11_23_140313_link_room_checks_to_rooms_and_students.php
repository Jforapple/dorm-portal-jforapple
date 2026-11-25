<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roomChecks = DB::table('room_checks')->get();

        if ($roomChecks->isNotEmpty()) {
            $dorm = DB::table('dorms')
                ->where('name', 'Unassigned Dorm')
                ->first();

            $dormId = $dorm?->id ?? DB::table('dorms')->insertGetId([
                'name' => 'Unassigned Dorm',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($roomChecks->pluck('room_id')->unique() as $roomId) {
                if (! DB::table('rooms')->where('id', $roomId)->exists()) {
                    DB::table('rooms')->insert([
                        'id' => $roomId,
                        'dorm_id' => $dormId,
                        'number' => (string) $roomId,
                        'capacity' => 4,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            foreach ($roomChecks->unique('student_id') as $roomCheck) {
                $existingUser = DB::table('users')
                    ->where('id', $roomCheck->student_id)
                    ->first();

                if ($existingUser) {
                    if ($existingUser->role !== 'student') {
                        throw new RuntimeException(
                            "User ID {$roomCheck->student_id} is not a student."
                        );
                    }

                    continue;
                }

                DB::table('users')->insert([
                    'id' => $roomCheck->student_id,
                    'name' => $roomCheck->student_name,
                    'email' => "legacy-student-{$roomCheck->student_id}@example.test",
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'student',
                    'room_id' => $roomCheck->room_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('room_checks', function (Blueprint $table) {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->restrictOnDelete();

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        Schema::table('room_checks', function (Blueprint $table) {
            $table->dropColumn('student_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_checks', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['student_id']);
        });

        Schema::table('room_checks', function (Blueprint $table) {
            $table->string('student_name')->default('');
        });

        $roomChecks = DB::table('room_checks')->get();

        foreach ($roomChecks as $roomCheck) {
            $studentName = DB::table('users')
                ->where('id', $roomCheck->student_id)
                ->value('name');

            DB::table('room_checks')
                ->where('id', $roomCheck->id)
                ->update(['student_name' => $studentName ?? '']);
        }
    }
};
