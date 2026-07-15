<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::where('name', 'Main Showroom Bandung')->firstOrFail();

        $car1 = Item::firstOrCreate(
            ['vin' => 'WP0ZZZ99ZLS123456'],
            [
                'warehouse_id'  => $warehouse->id,
                'brand'         => 'Porsche',
                'model'         => '911 GT3 RS',
                'year'          => 2024,
                'price'         => 4_500_000_000,
                'dp_percentage' => 20,
                'status'        => 'Available',
                'engine'        => '4.0L Flat-6 Naturally Aspirated',
                'transmission'  => '7-Speed PDK Automatic',
                'color'         => 'Shark Blue',
                'image_url'     => 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=800&q=80',
            ]
        );

        $car2 = Item::firstOrCreate(
            ['vin' => '1G1Y22D41P5109876'],
            [
                'warehouse_id'  => $warehouse->id,
                'brand'         => 'Chevrolet',
                'model'         => 'Corvette C8',
                'year'          => 2023,
                'price'         => 2_800_000_000,
                'dp_percentage' => 20,
                'status'        => 'Available',
                'engine'        => '6.2L V8 DI Premium Engine',
                'transmission'  => '8-Speed Dual-Clutch Transmission',
                'color'         => 'Torch Red',
                'image_url'     => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80',
            ]
        );

        $car3 = Item::firstOrCreate(
            ['vin' => 'ZFF72AHA8K0234567'],
            [
                'warehouse_id'  => $warehouse->id,
                'brand'         => 'Ferrari',
                'model'         => '488 Pista Coupe',
                'year'          => 2021,
                'price'         => 8_900_000_000,
                'dp_percentage' => 25,
                'status'        => 'Invoiced',
                'engine'        => '3.9L Twin-Turbocharged V8',
                'transmission'  => '7-Speed Dual-Clutch F1 Style',
                'color'         => 'Rosso Corsa Red',
                'image_url'     => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80',
            ]
        );

        $customer = Customer::firstOrCreate(
            ['phone' => '08112233445'],
            ['name'  => 'Mohammad Naufal']
        );

        // Hitung pajak konsisten dengan logika storeBooking()
        $subtotal  = 8_900_000_000;
        $taxRate   = 11.00;
        $taxAmount = (int) round($subtotal * ($taxRate / 100));
        $total     = $subtotal + $taxAmount;

        // Format invoice_code konsisten dengan Invoice::generateCode()
        Invoice::firstOrCreate(
            ['invoice_code' => 'SD/2026/0001'],
            [
                'customer_id'       => $customer->id,
                'item_id'           => $car3->id,
                'cashier_id'        => null,
                'date'              => '2026-07-25',
                'subtotal'          => $subtotal,
                'tax_rate'          => $taxRate,
                'tax_amount'        => $taxAmount,
                'total_amount'      => $total,
                'paid_amount'       => 0,
                'payment_status'    => 'Unpaid',
                'payment_type'      => 'None',
                'status'            => 'Pending',
                'authentic_receipt' => null,
            ]
        );

        unset($car1, $car2);
    }
}
