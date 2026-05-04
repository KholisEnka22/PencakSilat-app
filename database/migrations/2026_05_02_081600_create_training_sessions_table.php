<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('topic')->nullable();
            $table->foreignId('trainer_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('perguruan_id');
            $table->index('rayon_id');
            $table->index('session_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
