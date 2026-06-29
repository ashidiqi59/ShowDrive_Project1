<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\Cashier;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Invoice;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Company
        $company = Company::create([
            'name' => 'ShowDrive Premium Corp',
            'tax_id' => 'TX-998-887',
        ]);

        // 2. Seed Warehouse
        $warehouse = Warehouse::create([
            'company_id' => $company->id,
            'name' => 'Main Showroom Bandung',
            'location' => 'Jl. Phh. Mustofa No.68, Bandung',
        ]);

        // 3. Seed Cashier (Admin)
        Cashier::create([
            'company_id' => $company->id,
            'name' => 'Admin ShowDrive',
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'role' => 'Head Cashier',
        ]);

        // 4. Seed Items (Cars)
        $car1 = Item::create([
            'warehouse_id' => $warehouse->id,
            'brand' => 'Porsche',
            'model' => '911 GT3 RS',
            'vin' => 'WP0ZZZ99ZLS123456',
            'year' => 2024,
            'price' => 4500000000,
            'status' => 'Available',
            'engine' => '4.0L Flat-6 Naturally Aspirated',
            'transmission' => '7-Speed PDK Automatic',
            'color' => 'Shark Blue',
            'image_url' => 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=800&q=80',
        ]);

        $car2 = Item::create([
            'warehouse_id' => $warehouse->id,
            'brand' => 'Chevrolet',
            'model' => 'Corvette C8',
            'vin' => '1G1Y22D41P5109876',
            'year' => 2023,
            'price' => 2800000000,
            'status' => 'Available',
            'engine' => '6.2L V8 DI Premium Engine',
            'transmission' => '8-Speed Dual-Clutch Transmission',
            'color' => 'Torch Red',
            'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80',
        ]);

        $car3 = Item::create([
            'warehouse_id' => $warehouse->id,
            'brand' => 'Ferrari',
            'model' => '488 Pista Coupe',
            'vin' => 'ZFF72AHA8K0234567',
            'year' => 2021,
            'price' => 8900000000,
            'status' => 'Invoiced',
            'engine' => '3.9L Twin-Turbocharged V8',
            'transmission' => '7-Speed Dual-Clutch F1 Style',
            'color' => 'Rosso Corsa Red',
            'image_url' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80',
        ]);

        // 5. Seed Customer
        $customer = Customer::create([
            'name' => 'Mohammad Naufal',
            'phone' => '08112233445',
        ]);

        // 6. Seed initial Invoice linked to Ferrari (car3)
        Invoice::create([
            'invoice_code' => 'SD-INV-001',
            'customer_id' => $customer->id,
            'item_id' => $car3->id,
            'cashier_id' => null,
            'date' => '2026-05-25',
            'total_amount' => 8900000000,
            'paid_amount' => 0,
            'payment_status' => 'Unpaid',
            'payment_type' => 'None',
            'status' => 'Pending',
            'authentic_receipt' => null,
        ]);
    }
}
