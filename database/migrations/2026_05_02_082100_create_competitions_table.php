<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('category_type', 30);
            $table->string('age_category', 30)->nullable();
            $table->string('gender', 10)->nullable();
            $table->decimal('min_weight', 5, 2)->nullable();
            $table->decimal('max_weight', 5, 2)->nullable();
            $table->enum('bracket_type', ['single_elimination', 'double_elimination', 'round_robin'])
                ->default('single_elimination');
            $table->enum('status', ['draft', 'open', 'ongoing', 'done'])->default('draft');
            $table->timestamps();

            $table->index('event_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
