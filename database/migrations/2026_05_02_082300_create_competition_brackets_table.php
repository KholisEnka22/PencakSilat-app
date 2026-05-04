<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('round')->unsigned();
            $table->smallInteger('match_number')->unsigned();
            $table->foreignId('participant_a_id')->nullable()->constrained('competition_participants')->nullOnDelete();
            $table->foreignId('participant_b_id')->nullable()->constrained('competition_participants')->nullOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('competition_participants')->nullOnDelete();
            $table->string('score_a', 20)->nullable();
            $table->string('score_b', 20)->nullable();
            $table->dateTime('match_datetime')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('competition_id');
            $table->unique(['competition_id', 'round', 'match_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_brackets');
    }
};
