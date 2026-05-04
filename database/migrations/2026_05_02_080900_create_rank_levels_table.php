<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rank_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->tinyInteger('order_level')->unsigned();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('perguruan_id');
            $table->unique(['perguruan_id', 'order_level']);
            $table->unique(['perguruan_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rank_levels');
    }
};
