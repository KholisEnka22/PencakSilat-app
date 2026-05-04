<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->tinyInteger('day_of_week')->unsigned();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('perguruan_id');
            $table->index('rayon_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
