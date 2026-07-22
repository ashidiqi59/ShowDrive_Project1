<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->after('qris_image')
                ->comment('Path logo utama showroom di disk public');
            $table->string('favicon_url')->nullable()->after('logo_url')
                ->comment('Path favicon (.ico/.png) di disk public');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo_url', 'favicon_url']);
        });
    }
};
