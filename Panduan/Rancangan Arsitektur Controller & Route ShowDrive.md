## Arsitektur Route & Controller - ShowDrive (Proyek 1) 

Untuk menjaga keamanan dan kerapian kode (Clean Code), kita akan menerapkan konsep Separation of Concerns (Pemisahan Tugas). Aplikasi akan dibagi menjadi 3 Controller utama: 

## 1. AuthController (Pengawal Pintu Gerbang) 

Tugasnya khusus mengurus otentikasi (Login & Logout) bagi admin/kasir. 

Method yang disiapkan: 

- showLogin() : Menampilkan halaman form login admin. 

- authenticate() : Memeriksa username dan password di tabel cashiers . 

- logout() : Menghapus sesi (session) admin dan mengembalikan ke halaman login. 

## 2. PublicController (Area Pelanggan) 

Tugasnya melayani interaksi dari pengunjung publik dan pelanggan tanpa perlu login. Semua data yang ditampilkan bersifat aman (read-only untuk inventaris) dan insert-only (untuk pembuatan invoice). 

Method yang disiapkan: 

**==> picture [13 x 68] intentionally omitted <==**

**----- Start of picture text -----**<br>
expand<br>tune<br>chat_spark<br>**----- End of picture text -----**<br>


- index() : Mengambil data dari tabel items yang berstatus 'Available', di-join dengan 

- tabel warehouses untuk ditampilkan di katalog depan. 

- showDetail($id) : Menampilkan spesifikasi lengkap satu item . 

- generateInvoice(Request $request) : Menerima input form pelanggan. Mengecek 

- apakah customer sudah ada (berdasarkan no HP). Jika belum, buat baru. Setelah itu, buat data baru di tabel invoices dan ubah status mobil jadi 'Invoiced'. 

- trackInvoice(Request $request) : Mencari data invoices berdasarkan 

- pencocokan nomor HP customer . 

- uploadReceipt(Request $request, $invoice_id) : Menerima unggahan file bukti 

- bayar dari pelanggan dan menyimpannya ke folder storage server. 

- printInvoice($invoice_id) : Membuka tampilan kwitansi PDF yang siap dicetak. 

## 3. AdminController (Dapur Utama - Dilindungi Middleware) 

Tugasnya melayani semua aktivitas CRUD dan validasi finansial. Controller ini wajib dilindungi oleh middleware('auth') agar tidak bisa ditembus oleh orang yang belum login. 

Method yang disiapkan: 

- dashboard() : Menampilkan ringkasan analitik (menghitung total mobil, total uang 

- masuk) dan me-load tabel manajemen inventaris & keuangan. 

- storeItem(Request $request) : Menyisipkan data mobil baru ke tabel items . 

- Dilengkapi validasi Unique VIN. 

- updateItem(Request $request, $id) : Memperbarui data mobil (seperti ganti harga 

- atau warna). 

- deleteItem($id) : Menghapus mobil (akan dijaga otomatis oleh aturan ON DELETE 

- RESTRICT yang kita buat di migrasi). 

- verifyPayment($invoice_id) : Aksi krusial! Kasir menekan tombol "Sahkan Lunas". 

- Method ini akan mengubah payment_status di invoices jadi 'Paid', mengisi 

cashier_id dengan ID kasir yang login, dan mengubah status items jadi 'Sold'. 

## 4. Peta Jalan URL (Routes Map di routes/web.php ) 

Nantinya, file route kita akan terlihat sangat rapi seperti ini: 

// --- PUBLIC ROUTES (Pelanggan) --Route::get('/', [PublicController::class, 'index']); Route::get('/car/{id}', [PublicController::class, 'showDetail']); Route::post('/invoice/create', [PublicController::class, 'generateInvoice']); Route::get('/track', [PublicController::class, 'trackInvoice']); Route::post('/invoice/{id}/upload', [PublicController::class, 'uploadReceipt']) Route::get('/invoice/{id}/print', [PublicController::class, 'printInvoice']); // --- AUTH ROUTES (Login) --Route::get('/login', [AuthController::class, 'showLogin'])->name('login'); Route::post('/login', [AuthController::class, 'authenticate']); Route::post('/logout', [AuthController::class, 'logout']); // --- ADMIN ROUTES (Kasir/Admin - Wajib Login) --Route::middleware(['auth'])->prefix('admin')->group(function () { Route::get('/dashboard', [AdminController::class, 'dashboard']); 

// CRUD Items Route::post('/items', [AdminController::class, 'storeItem']); Route::put('/items/{id}', [AdminController::class, 'updateItem']); Route::delete('/items/{id}', [AdminController::class, 'deleteItem']); 

// Validasi Pembayaran Route::post('/invoice/{id}/verify', [AdminController::class, 'verifyPayment }); 

