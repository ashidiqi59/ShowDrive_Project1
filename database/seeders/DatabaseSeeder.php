<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Urutan pemanggilan penting karena ada dependency antar tabel:
     *   Company → Warehouse → Cashier → Car (Item)
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,   // 1. Harus pertama — company_id dibutuhkan oleh Warehouse & Cashier
            WarehouseSeeder::class, // 2. Butuh company_id
            CashierSeeder::class,   // 3. Butuh company_id
            CarSeeder::class,       // 4. Butuh warehouse_id
        ]);
    }
}
