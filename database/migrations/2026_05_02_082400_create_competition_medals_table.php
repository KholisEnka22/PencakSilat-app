<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_medals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('medal_type', ['gold', 'silver', 'bronze']);
            $table->timestamps();

            $table->index('competition_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_medals');
    }
};
