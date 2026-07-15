<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // NIK = Nomor Induk Kependudukan (KTP Indonesia) — 16 digit
            // Nullable: pelanggan lama tidak wajib punya NIK tercatat
            // Unique: satu NIK hanya boleh terdaftar pada satu customer record
            // Digunakan sebagai identifier sekunder untuk pengurusan BPKB/STNK
            $table->string('nik', 16)->nullable()->unique()->after('phone')
                ->comment('Nomor Induk Kependudukan KTP 16 digit — opsional');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
