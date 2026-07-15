<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Alasan penolakan bukti bayar — wajib diisi kasir saat menolak
            // Disimpan sebagai audit trail dan dikirim ke pelanggan via WA prefilled
            $table->text('rejection_note')->nullable()->after('authentic_receipt')
                ->comment('Alasan penolakan bukti bayar oleh kasir');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('rejection_note');
        });
    }
};
