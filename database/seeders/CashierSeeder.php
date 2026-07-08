<?php

namespace Database\Seeders;

use App\Models\Cashier;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
    /**
     * Seed akun kasir/admin default.
     * Dijalankan setelah CompanySeeder karena membutuhkan company_id.
     *
     * Kredensial default untuk fresh install:
     *   Username : admin
     *   Password : showdrive2026
     */
    public function run(): void
    {
        $company = Company::where('name', 'ShowDrive Premium Corp')->firstOrFail();

        Cashier::firstOrCreate(
            ['username' => 'admin'],
            [
                'company_id' => $company->id,
                'name'       => 'Super Admin ShowDrive',
                'password'   => Hash::make('showdrive2026'),
                'role'       => 'Super Admin',
            ]
        );
    }
}
