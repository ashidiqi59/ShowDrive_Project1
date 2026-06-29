## Kode Model Laravel (Eloquent ORM) - ShowDrive 

Semua file ini secara default berada di dalam folder app/Models/ . Jika filenya belum ada, kamu bisa membuatnya menggunakan perintah php artisan make:model NamaModel . 

Perhatikan penggunaan properti $fillable (untuk mengizinkan input data secara massal/Mass Assignment) dan fungsi relasinya. 

## 1. Model Company.php 

<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; 

class Company extends Model { use HasFactory; 

protected $fillable = ['name', 'tax_id']; 

// Relasi One-to-Many: Satu perusahaan punya banyak gudang public function warehouses() { return $this->hasMany(Warehouse::class); } 

expand tune chat_spark 

// Relasi One-to-Many: Satu perusahaan punya banyak kasir/admin public function cashiers() { return $this->hasMany(Cashier::class); } } 

## 2. Model Warehouse.php 

<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; 

class Warehouse extends Model { use HasFactory; protected $fillable = ['company_id', 'name', 'location']; 

// Relasi Belongs-To: Gudang ini milik satu perusahaan public function company() { 

return $this->belongsTo(Company::class); 

} 

// Relasi One-to-Many: Gudang ini menyimpan banyak mobil (items) public function items() { 

return $this->hasMany(Item::class); } } 

## 3. Model Cashier.php (Khusus: Pakai Authenticatable untuk Login) 

Catatan Penting: Karena Cashier adalah entitas yang bisa Login ke sistem Admin, model ini wajib mewarisi (extend) class Authenticatable , bukan Model biasa. 

<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory; 

use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk fitur use Illuminate\Notifications\Notifiable; 

class Cashier extends Authenticatable { use HasFactory, Notifiable; protected $fillable = [ 'company_id', 'name', 'username', 'password', 'role' ]; protected $hidden = [ 'password', // Sembunyikan password saat data dipanggil ]; public function company() { return $this->belongsTo(Company::class); } // Relasi One-to-Many: Satu kasir bisa mengesahkan banyak invoice public function invoices() { return $this->hasMany(Invoice::class); } } 

## 4. Model Customer.php 

<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; 

class Customer extends Model { use HasFactory; protected $fillable = ['name', 'phone']; 

// Relasi One-to-Many: Satu pelanggan bisa punya banyak riwayat invoice public function invoices() { return $this->hasMany(Invoice::class); } } 

## 5. Model Item.php 

<?php namespace App\Models; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; class Item extends Model { use HasFactory; protected $fillable = [ 'warehouse_id', 'brand', 'model', 'vin', 'year', 'price', 'status', 'engine', 'transmission', 'color', 'image_url' ]; public function warehouse() { return $this->belongsTo(Warehouse::class); } 

// Relasi One-to-Many: Satu mobil bisa memiliki riwayat invoice (walau bias public function invoices() { return $this->hasMany(Invoice::class); } 

} 

6. Model Invoice.php  (Pusat Transaksi) 

<?php namespace App\Models; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; class Invoice extends Model { use HasFactory; protected $fillable = [ 'invoice_code', 'customer_id', 'item_id', 'cashier_id', 'date', 'total_amount', 'payment_status', 'authentic_receipt' ]; // Relasi Belongs-To ke 3 Entitas Sekaligus public function customer() { return $this->belongsTo(Customer::class); } public function item() { return $this->belongsTo(Item::class); } public function cashier() { return $this->belongsTo(Cashier::class); } } 

