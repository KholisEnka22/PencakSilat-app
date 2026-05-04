<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('perguruan_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method', 30);
            $table->decimal('amount_paid', 12, 2);
            $table->dateTime('paid_at');
            $table->string('reference_number', 100)->nullable();
            $table->string('proof_file')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('perguruan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
