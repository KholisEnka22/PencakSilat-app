<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perguruans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('abbreviation', 30)->nullable();
            $table->string('decree_number', 100)->nullable();
            $table->date('decree_date')->nullable();
            $table->string('logo')->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->text('street_address')->nullable();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->char('postal_code', 5)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perguruans');
    }
};
