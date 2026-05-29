<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('confirmed_at')
                ->nullable()
                ->after('status');

            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
          $table->dropColumn([
            'status',
            'confirmed_at',
            'cancelled_at',
        ]);
        });
    }
};
