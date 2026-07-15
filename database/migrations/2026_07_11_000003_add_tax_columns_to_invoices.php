<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Subtotal = harga unit sebelum pajak
            $table->decimal('subtotal', 15, 2)->default(0)->after('total_amount')
                ->comment('Harga unit sebelum pajak (PPN)');

            // Tax rate dalam persen, contoh: 11.00 = PPN 11%
            $table->decimal('tax_rate', 5, 2)->default(11.00)->after('subtotal')
                ->comment('Tarif PPN dalam persen');

            // Nominal pajak (subtotal * tax_rate / 100)
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate')
                ->comment('Nominal PPN yang dikenakan');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_rate', 'tax_amount']);
        });
    }
};
