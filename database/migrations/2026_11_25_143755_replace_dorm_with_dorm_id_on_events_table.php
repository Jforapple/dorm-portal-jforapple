<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $dorms = DB::table('dorms')->pluck('id', 'name');

        $events = DB::table('events')->get();

        foreach ($events as $event) {
            if (! $dorms->has($event->dorm)) {
                throw new RuntimeException(
                    "Dorm '{$event->dorm}' does not exist in the dorms table."
                );
            }
        }

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('dorm_id')
                ->nullable()
                ->constrained('dorms')
                ->restrictOnDelete();
        });

        foreach ($events as $event) {
            DB::table('events')
                ->where('id', $event->id)
                ->update([
                    'dorm_id' => $dorms[$event->dorm],
                ]);
        }

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('dorm_id')->nullable(false)->change();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('dorm');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('dorm')->nullable();
        });

        $events = DB::table('events')->get();

        foreach ($events as $event) {
            $dormName = DB::table('dorms')
                ->where('id', $event->dorm_id)
                ->value('name');

            DB::table('events')
                ->where('id', $event->id)
                ->update([
                    'dorm' => $dormName,
                ]);
        }

        Schema::table('events', function (Blueprint $table) {
            $table->string('dorm')->nullable(false)->change();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dorm_id');
        });
    }
};
