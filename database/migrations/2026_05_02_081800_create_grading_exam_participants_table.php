<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_exam_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grading_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('current_rank_level_id')->constrained('rank_levels')->cascadeOnDelete();
            $table->decimal('score_theory', 5, 2)->nullable();
            $table->decimal('score_practice', 5, 2)->nullable();
            $table->decimal('score_final', 5, 2)->nullable();
            $table->enum('result', ['pass', 'fail', 'absent'])->nullable();
            $table->string('certificate_number', 100)->nullable()->unique();
            $table->date('certificate_issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['grading_exam_id', 'member_id']);
            $table->index('perguruan_id');
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_exam_participants');
    }
};
