<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_checks', function (Blueprint $table) {
            $table->date('inspection_date')->nullable();
            $table->foreignId('inspector_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('checklist')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_checks', function (Blueprint $table) {
            $table->dropForeign(['inspector_id']);
            $table->dropColumn([
                'inspection_date',
                'inspector_id',
                'checklist',
                'score',
                'notes',
            ]);
        });
    }
};
