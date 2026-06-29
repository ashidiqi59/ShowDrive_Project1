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
        Schema::create('invoices', function (Blueprint $table) {
           $table->id();
            $table->string('invoice_code')->unique();
            
            // Relasi ke Customer
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
            // Relasi ke Item dengan RESTRICT (Integritas Data agar mobil tidak bisa dihapus asal)
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');
            
            // Relasi ke Cashier (Nullable karena di awal transaksi kasir belum mengesahkan)
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers')->onDelete('set null');
            
            $table->date('date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('payment_type', ['None', 'Down Payment', 'Paid'])->default('None');
            $table->enum('payment_status', ['Unpaid', 'Pending Validation', 'Down Payment', 'Paid'])->default('Unpaid');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('authentic_receipt')->nullable(); // Path untuk file gambar
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
