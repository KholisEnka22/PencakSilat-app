<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight_actual', 5, 2)->nullable();
            $table->tinyInteger('seeding')->unsigned()->nullable();
            $table->enum('status', ['registered', 'verified', 'disqualified'])->default('registered');
            $table->timestamps();

            $table->unique(['competition_id', 'member_id']);
            $table->index('competition_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};
