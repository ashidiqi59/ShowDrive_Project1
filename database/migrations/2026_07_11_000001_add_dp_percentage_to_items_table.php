<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // DP percentage per unit — default 20%, range 1–100
            // Stored as TINYINT UNSIGNED to keep it lightweight (1 byte)
            $table->unsignedTinyInteger('dp_percentage')
                ->default(20)
                ->after('price')
                ->comment('Persentase uang muka (DP) dalam persen, contoh: 20 = 20%');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('dp_percentage');
        });
    }
};
