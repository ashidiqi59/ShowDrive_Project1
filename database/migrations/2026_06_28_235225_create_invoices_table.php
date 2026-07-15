<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique();

            // Customer: cascade — if customer is deleted, remove their invoices
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // Item: restrict — block deleting a car with active invoice history
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');

            // Cashier: set null — invoice persists even if the cashier account is removed
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers')->onDelete('set null');

            $table->date('date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('payment_type', ['None', 'Down Payment', 'Paid'])->default('None');
            $table->enum('payment_status', ['Unpaid', 'Pending Validation', 'Down Payment', 'Paid'])->default('Unpaid');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('authentic_receipt')->nullable();
            $table->timestamps();

            // Composite indexes aligned with the three most common query patterns:
            // 1. Admin invoice list filtered by payment_status
            $table->index('payment_status');
            // 2. Customer tracking — look up invoices by customer
            $table->index('customer_id');
            // 3. Item-level invoice history
            $table->index('item_id');
            // 4. Laporan (financial report) filtered by created_at year/month
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
