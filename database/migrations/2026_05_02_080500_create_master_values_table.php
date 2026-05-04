<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_data_id')->constrained('master_data')->cascadeOnDelete();
            $table->string('label', 100);
            $table->string('value', 100);
            $table->text('description')->nullable();
            $table->smallInteger('sort_order')->unsigned()->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('master_data_id');
            $table->unique(['master_data_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_values');
    }
};
