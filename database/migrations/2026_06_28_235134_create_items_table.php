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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->string('vin', 17)->unique(); // Batasan Unique 17 Karakter
            $table->integer('year');
            $table->decimal('price', 15, 2);
            $table->enum('status', ['Available', 'Invoiced', 'Sold'])->default('Available');
            $table->string('engine');
            $table->string('transmission');
            $table->string('color');
            $table->string('image_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
