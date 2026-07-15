<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();

            // Index for eager-loading item gallery
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};
