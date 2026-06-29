## Kode Migrasi Laravel - ShowDrive (RDBMS 6 Tabel) 

Berikut adalah isi dari keenam file migrasi. Pastikan kamu membuatnya secara berurutan 

menggunakan perintah php artisan make:migration create_nama_tabel_table agar urutan timestamp pada nama filenya benar. 

## 1. Tabel Companies (Induk Tertinggi) 

Perintah: php artisan make:migration create_companies_table 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; 

return new class extends Migration { public function up(): void { Schema::create('companies', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('tax_id')->nullable(); expand $table->timestamps(); }); tune } public function down(): void chat_spark { Schema::dropIfExists('companies'); } }; 

## 2. Tabel Warehouses (Anak dari Companies) 

Perintah: php artisan make:migration create_warehouses_table 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('warehouses', function (Blueprint $table) { $table->id(); $table->foreignId('company_id')->constrained('companies')->onDelete $table->string('name'); $table->string('location'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('warehouses'); 

} }; 

## 3. Tabel Cashiers (Admin Login, Anak dari Companies) 

Perintah: php artisan make:migration create_cashiers_table 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('cashiers', function (Blueprint $table) { $table->id(); $table->foreignId('company_id')->constrained('companies')->onDelete $table->string('name'); $table->string('username')->unique(); $table->string('password'); $table->string('role')->default('Head Cashier'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('cashiers'); } }; 

## 4. Tabel Customers (Induk Mandiri) 

Perintah: php artisan make:migration create_customers_table 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('customers', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('phone')->unique(); // Unique agar bisa jadi kredens $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('customers'); 

} }; 

## 5. Tabel Items (Katalog Mobil, Anak dari Warehouses) 

Perintah: php artisan make:migration create_items_table 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; 

return new class extends Migration { public function up(): void { Schema::create('items', function (Blueprint $table) { $table->id(); $table->foreignId('warehouse_id')->constrained('warehouses')->onDel $table->string('brand'); $table->string('model'); $table->string('vin', 17)->unique(); // Batasan Unique 17 Karakter $table->integer('year'); $table->decimal('price', 15, 2); $table->enum('status', ['Available', 'Invoiced', 'Sold'])->default( $table->string('engine'); $table->string('transmission'); $table->string('color'); $table->string('image_url'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('items'); } }; 

## 6. Tabel Invoices (Pusat Transaksi) 

Perintah: php artisan make:migration create_invoices_table Catatan: Perhatikan penggunaan onDelete('restrict') pada item_id . 

use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('invoices', function (Blueprint $table) { $table->id(); $table->string('invoice_code')->unique(); 

// Relasi ke Customer 

$table->foreignId('customer_id')->constrained('customers')->onDelet 

// Relasi ke Item dengan RESTRICT (Integritas Data agar mobil tidak $table->foreignId('item_id')->constrained('items')->onDelete('restr 

// Relasi ke Cashier (Nullable karena di awal transaksi kasir belum $table->foreignId('cashier_id')->nullable()->constrained('cashiers' $table->date('date'); $table->decimal('total_amount', 15, 2); $table->enum('payment_status', ['Unpaid', 'Pending Validation', 'Pa $table->string('authentic_receipt')->nullable(); // Path untuk file $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('invoices'); } }; 

