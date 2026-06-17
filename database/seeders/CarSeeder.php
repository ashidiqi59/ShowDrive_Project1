<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Car;
use App\Models\Booking;
use App\Models\User;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default Admin User
        User::updateOrCreate(
            ['email' => 'admin@showdrive.com'],
            [
                'name' => 'Admin ShowDrive',
                'password' => bcrypt('admin'),
            ]
        );

        // Seed Cars
        $car1 = Car::create([
            'brand' => 'Porsche',
            'model' => '911 GT3 RS',
            'vin' => 'WP0ZZZ99ZLS123456',
            'year' => 2024,
            'price' => 4500000000,
            'status' => 'Available',
            'engine' => '4.0L Flat-6 Naturally Aspirated',
            'transmission' => '7-Speed PDK Automatic',
            'color' => 'Shark Blue',
            'image' => 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=800&q=80',
        ]);

        $car2 = Car::create([
            'brand' => 'Chevrolet',
            'model' => 'Corvette C8',
            'vin' => '1G1Y22D41P5109876',
            'year' => 2023,
            'price' => 2800000000,
            'status' => 'Available',
            'engine' => '6.2L V8 DI Premium Engine',
            'transmission' => '8-Speed Dual-Clutch Transmission',
            'color' => 'Torch Red',
            'image' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80',
        ]);

        $car3 = Car::create([
            'brand' => 'Ferrari',
            'model' => '488 Pista Coupe',
            'vin' => 'ZFF72AHA8K0234567',
            'year' => 2021,
            'price' => 8900000000,
            'status' => 'Booked',
            'engine' => '3.9L Twin-Turbocharged V8',
            'transmission' => '7-Speed Dual-Clutch F1 Style',
            'color' => 'Rosso Corsa Red',
            'image' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80',
        ]);

        // Seed initial Booking linked to Ferrari (car3)
        Booking::create([
            'customer_name' => 'Mohammad Naufal',
            'phone' => '08112233445',
            'car_id' => $car3->id,
            'date' => '2026-05-25',
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'payment_type' => 'None',
            'paid_amount' => 0,
            'payment_proof' => null,
        ]);
    }
}
