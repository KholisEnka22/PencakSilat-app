<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_rayon_id')->constrained('rayons')->cascadeOnDelete();
            $table->foreignId('to_rayon_id')->constrained('rayons')->cascadeOnDelete();
            $table->date('transferred_at');
            $table->text('reason')->nullable();
            $table->foreignId('transferred_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('member_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_transfers');
    }
};
