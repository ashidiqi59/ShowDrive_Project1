<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL tidak bisa ALTER ENUM langsung via Blueprint, harus raw SQL
        DB::statement("
            ALTER TABLE invoices
            MODIFY COLUMN status
            ENUM('Pending','Approved','Rejected','Cancelled') NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            ALTER TABLE invoices
            MODIFY COLUMN payment_status
            ENUM('Unpaid','Pending Validation','Down Payment','Paid','Cancelled') NOT NULL DEFAULT 'Unpaid'
        ");
    }

    public function down(): void
    {
        // Rollback: hapus nilai Cancelled, kembalikan ke enum awal
        // Data dengan status Cancelled diset ke Rejected/Unpaid terlebih dahulu
        DB::statement("UPDATE invoices SET status = 'Rejected' WHERE status = 'Cancelled'");
        DB::statement("UPDATE invoices SET payment_status = 'Unpaid' WHERE payment_status = 'Cancelled'");

        DB::statement("
            ALTER TABLE invoices
            MODIFY COLUMN status
            ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            ALTER TABLE invoices
            MODIFY COLUMN payment_status
            ENUM('Unpaid','Pending Validation','Down Payment','Paid') NOT NULL DEFAULT 'Unpaid'
        ");
    }
};
