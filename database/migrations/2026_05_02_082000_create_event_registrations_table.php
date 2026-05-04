<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number', 50)->nullable()->unique();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'member_id']);
            $table->index('perguruan_id');
            $table->index('event_id');
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
