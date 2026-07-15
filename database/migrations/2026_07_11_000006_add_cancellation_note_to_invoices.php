<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Alasan pembatalan dari sisi PELANGGAN
            // Berbeda dari rejection_note (alasan penolakan oleh ADMIN)
            // Keduanya membentuk audit trail dua arah yang transparan
            $table->text('cancellation_note')->nullable()->after('rejection_note')
                ->comment('Alasan pembatalan reservasi dari pelanggan');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('cancellation_note');
        });
    }
};
