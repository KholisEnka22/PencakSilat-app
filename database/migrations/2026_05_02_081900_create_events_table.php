<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 30);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('registration_open_at')->nullable();
            $table->dateTime('registration_close_at')->nullable();
            $table->string('location_name', 200)->nullable();
            $table->text('street_address')->nullable();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->smallInteger('max_participants')->unsigned()->nullable();
            $table->decimal('registration_fee', 12, 2)->default(0);
            $table->string('poster')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'done', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('perguruan_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
