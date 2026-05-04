<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->foreignId('target_rank_level_id')->constrained('rank_levels')->cascadeOnDelete();
            $table->date('exam_date');
            $table->string('location')->nullable();
            $table->string('examiner_name', 150)->nullable();
            $table->smallInteger('max_participants')->unsigned()->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'done'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('perguruan_id');
            $table->index('exam_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_exams');
    }
};
