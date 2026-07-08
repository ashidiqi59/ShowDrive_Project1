<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed data perusahaan showroom.
     * Dijalankan PERTAMA karena company_id dibutuhkan oleh Warehouse & Cashier.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate agar aman dijalankan berulang kali tanpa duplikasi
        Company::firstOrCreate(
            ['name' => 'ShowDrive Premium Corp'],
            [
                'tax_id'  => 'TX-998-887',
                'address' => 'Jl. Phh. Mustofa No.68, Kota Bandung, Jawa Barat 40124',
                'phone'   => '022-7012345',
            ]
        );
    }
}
