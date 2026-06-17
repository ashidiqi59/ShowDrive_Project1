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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone');
            $table->foreignId('car_id')->constrained('cars')->onDelete('restrict');
            $table->date('date');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->string('payment_status')->default('Unpaid'); // Unpaid, Down Payment, Paid
            $table->string('payment_type')->default('None'); // None, Down Payment, Paid
            $table->bigInteger('paid_amount')->default(0);
            $table->string('payment_proof')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
