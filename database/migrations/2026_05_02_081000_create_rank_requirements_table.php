<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rank_level_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('min_attendance_count')->unsigned()->default(0);
            $table->tinyInteger('min_active_months')->unsigned()->default(0);
            $table->tinyInteger('min_age')->unsigned()->nullable();
            $table->json('required_skills')->nullable();
            $table->text('additional_requirements')->nullable();
            $table->decimal('exam_fee', 12, 2)->default(0);
            $table->timestamps();

            $table->index('perguruan_id');
            $table->unique(['perguruan_id', 'rank_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rank_requirements');
    }
};
