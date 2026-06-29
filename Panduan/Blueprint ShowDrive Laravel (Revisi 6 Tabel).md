## Blueprint Pengembangan ShowDrive dengan Laravel & phpMyAdmin (Revisi RDBMS 6 Tabel) 

Dokumen ini berisi rancangan arsitektur tingkat lanjut untuk memigrasi prototipe ShowDrive menjadi aplikasi web dinamis berbasis Laravel dan MySQL. Arsitektur ini menggunakan standar Normalisasi Basis Data Relasional dengan 6 Entitas Utama demi menjaga integritas, skalabilitas, dan keamanan pencatatan finansial. 

## Arsitektur Database & Migrasi (MySQL/phpMyAdmin) 

Database showdrive_db akan menggunakan struktur 6 tabel yang saling berelasi. Desain ini memisahkan data entitas secara logis untuk mencegah redundansi (duplikasi data) dan mendukung Referential Integrity Constraints. 

erDiagram COMPANIES ||--o{ WAREHOUSES : "has" COMPANIES ||--o{ CASHIERS : "employs" WAREHOUSES ||--o{ ITEMS : "stores" CUSTOMERS ||--o{ INVOICES : "makes" ITEMS ||--o{ INVOICES : "billed_in" CASHIERS ||--o{ INVOICES : "verifies" COMPANIES { bigint id PK string name string tax_id } WAREHOUSES { bigint id PK bigint company_id FK string name string location } CASHIERS { bigint id PK bigint company_id FK string name string username string password string role } CUSTOMERS { expand bigint id PK string name tune string phone UK } chat_spark ITEMS { bigint id PK bigint warehouse_id FK string brand string model string vin UK "17-digit unique alphanumeric" integer year 

bigdecimal price enum status "Available, Invoiced, Sold" string engine string transmission string color string image_url timestamps created_at_updated_at } 

INVOICES { bigint id PK string invoice_code UK bigint customer_id FK "ON DELETE CASCADE" bigint item_id FK "ON DELETE RESTRICT" bigint cashier_id FK "Nullable" date date bigdecimal total_amount enum payment_status "Unpaid, Pending Validation, Paid" string authentic_receipt "file path" timestamps created_at_updated_at } 

## Penjelasan Relasi & Batasan (Constraints) 

1. Unique Constraint (UK): 

   - items.vin harus unik (tidak boleh ada nomor rangka ganda). 

customers.phone harus unik (digunakan sebagai otentikasi login mandiri pelanggan). 

2. Referential Integrity (Foreign Keys): 

   - item_id pada tabel INVOICES menggunakan batasan ON DELETE RESTRICT . Artinya, 

   - Admin tidak bisa menghapus data mobil dari tabel ITEMS jika mobil tersebut sudah memiliki invoice yang sedang berjalan. 

   - cashier_id pada tabel INVOICES bersifat Nullable (kosong pada awalnya), dan 

   - baru akan terisi dengan ID Kasir ketika pembayaran disahkan (Approved). 

## Komponen Aplikasi & Struktur File (Laravel 11) 

Aplikasi akan dikembangkan dengan arsitektur MVC (Model-View-Controller) yang solid. Berikut adalah daftar komponen utama berdasarkan arsitektur 6 tabel: 

## 1. Models & Migrations 

- app/Models/Company.php (HasMany: Warehouses, Cashiers) 

- app/Models/Warehouse.php (BelongsTo: Company | HasMany: Items) 

- app/Models/Cashier.php (Mewarisi class Authenticatable untuk Login Admin) app/Models/Customer.php (HasMany: Invoices) 

- app/Models/Item.php (BelongsTo: Warehouse | HasMany: Invoices) 

- app/Models/Invoice.php (BelongsTo: Customer, Item, Cashier) 

## 2. Controllers Utama 

PublicController.php : 

   - index() : Memuat tabel items dengan Eager Loading warehouse untuk katalog 

   - publik. 

   - generateInvoice() : Membuat record di tabel customers (jika belum ada) dan 

   - menyisipkan data baru ke tabel invoices . 

   - trackInvoice() : Mencari data invoices berdasarkan pencocokan nomor HP di tabel customers . 

- AdminController.php : 

   - dashboard() : Menampilkan metrik hasil JOIN dari keenam tabel. 

   - verifyInvoice($id) : Mengubah payment_status menjadi 'Paid', mengaitkan cashier_id dengan ID admin yang sedang login ( Auth::id() ), dan mengubah status items menjadi 'Sold' dalam satu blok Database Transaction. 

## Panduan Migrasi Bertahap (Execution Plan) 

Untuk menghindari bentrok struktur data, urutan pembuatan Migration di Laravel harus mengikuti hierarki dari entitas terkuat (tanpa Foreign Key) ke entitas terlemah (banyak Foreign Key): 

1. php artisan make:model Company -m 

2. php artisan make:model Customer -m 

3. php artisan make:model Warehouse -m 

4. php artisan make:model Cashier -m 

5. php artisan make:model Item -m 

6. php artisan make:model Invoice -m 

Catatan: Eksekusi command ini akan mempersiapkan pondasi RDBMS yang presisi untuk tahap koding selanjutnya. 

