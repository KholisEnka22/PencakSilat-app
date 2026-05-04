<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_rank_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rank_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grading_exam_id')->nullable()->constrained()->nullOnDelete();
            $table->date('promoted_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_rank_histories');
    }
};
