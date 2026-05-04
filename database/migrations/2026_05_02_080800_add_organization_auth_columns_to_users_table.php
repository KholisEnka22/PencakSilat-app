<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('perguruan_id')
                ->nullable()
                ->after('id')
                ->constrained('perguruans')
                ->nullOnDelete();

            $table->foreignId('rayon_id')
                ->nullable()
                ->after('perguruan_id')
                ->constrained('rayons')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['rayon_id']);
            $table->dropForeign(['perguruan_id']);

            $table->dropColumn([
                'rayon_id',
                'perguruan_id',
            ]);
        });
    }
};
