<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            // VIN is exactly 17 chars per ISO 3779 — enforce at DB level too
            $table->string('vin', 17)->unique();
            $table->smallInteger('year')->unsigned();
            $table->decimal('price', 15, 2);
            $table->enum('status', ['Available', 'Invoiced', 'Sold'])->default('Available');
            $table->string('engine');
            $table->string('transmission');
            $table->string('color');
            $table->string('image_url')->nullable();
            $table->timestamps();

            // Composite index: catalog page filters (brand + model search + status filter)
            $table->index(['brand', 'model']);
            $table->index('status');
            $table->index('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
