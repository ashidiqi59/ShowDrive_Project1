<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Seed data gudang/showroom lokasi.
     * Dijalankan setelah CompanySeeder karena membutuhkan company_id.
     */
    public function run(): void
    {
        $company = Company::where('name', 'ShowDrive Premium Corp')->firstOrFail();

        Warehouse::firstOrCreate(
            ['name' => 'Main Showroom Bandung'],
            [
                'company_id' => $company->id,
                'location'   => 'Jl. Phh. Mustofa No.68, Bandung',
            ]
        );

        Warehouse::firstOrCreate(
            ['name' => 'Showroom Sudirman Jakarta'],
            [
                'company_id' => $company->id,
                'location'   => 'Jl. Jend. Sudirman Kav. 25, Jakarta Selatan',
            ]
        );
    }
}
