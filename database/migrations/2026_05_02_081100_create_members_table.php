<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rank_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('member_number', 50)->nullable()->unique();
            $table->string('full_name', 150);
            $table->string('national_id', 16)->nullable()->unique();
            $table->string('gender', 10);
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->string('religion', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('photo')->nullable();
            $table->text('street_address')->nullable();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->char('postal_code', 5)->nullable();
            $table->date('join_date');
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('perguruan_id');
            $table->index('rayon_id');
            $table->index('rank_level_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
