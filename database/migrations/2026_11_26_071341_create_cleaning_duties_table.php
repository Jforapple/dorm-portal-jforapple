
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_duties', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->date('due_date');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_duties');
    }
};
