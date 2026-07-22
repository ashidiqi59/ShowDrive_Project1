<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom `date` dari DATE menjadi DATETIME agar dapat menyimpan
     * waktu inspeksi sekaligus tanggal.
     * Nilai yang sudah ada dipertahankan — MySQL otomatis mengisi waktu 00:00:00.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Rollback ke DATE — perhatian: komponen waktu akan hilang
            $table->date('date')->change();
        });
    }
};
