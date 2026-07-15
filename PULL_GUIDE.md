# 🚀 PANDUAN SETUP SETELAH `git pull` — SHOWDRIVE

Panduan ini berisi langkah-langkah wajib yang harus dilakukan oleh developer/tim setelah melakukan `git pull` untuk memastikan perubahan database, konfigurasi baru, dan pengamanan sistem berjalan dengan semestinya.

---

### Langkah 1: Tarik Perubahan Terbaru (Git Pull)
Tarik kode terbaru dari branch utama repositori:
```bash
git pull origin <nama-branch>
```

### Langkah 2: Tambahkan Variabel Baru di File `.env`
Karena file `.env` di-ignore oleh Git, setiap developer wajib menambahkan variabel konfigurasi keamanan baru untuk token gateway akses admin secara manual.

Buka file **`.env`** Anda, lalu tambahkan baris berikut di bawah `APP_KEY`:
```env
SD_GATEWAY_TOKEN=SD_STEALTH_AUTH_2026
```
*(Catatan: `SD_STEALTH_AUTH_2026` adalah nilai default. Anda dapat mengubahnya sesuai kebutuhan keamanan).*

### Langkah 3: Jalankan Migrasi Database
Kami menambahkan skema database baru (kolom `handed_over_at` dan indeks komposit pencarian). Jalankan perintah berikut untuk memperbarui struktur database lokal Anda:
```bash
php artisan migrate
```

### Langkah 4: Bersihkan Cache Konfigurasi
Untuk memastikan Laravel membaca kunci konfigurasi baru (`gateway_token` di dalam `config/app.php`), bersihkan cache konfigurasi Laravel:
```bash
php artisan config:clear
```
*(Jika di server staging/produksi, Anda dapat melakukan re-cache dengan `php artisan config:cache`).*

### Langkah 5: Jalankan Server Lokal
Nyalakan kembali server development lokal Anda:
```bash
php artisan serve
```

---

## 🔍 Detail Perubahan yang Diterapkan:
1. **Keamanan Rute Admin**: Token login admin kini dibaca dinamis dari `.env` (`SD_GATEWAY_TOKEN`) dan diverifikasi melalui `config('app.gateway_token')`.
2. **Proteksi Handover**: Transaksi yang sudah diserahterimakan (`handed_over_at` tidak null) atau sudah lunas (`Paid`) otomatis dikunci rapat-rapat baik dari sisi UI maupun validasi server-side API.
3. **Indeks Komposit**: Struktur database dioptimalkan dengan indeks komposit pada kolom `payment_status` dan `handed_over_at` demi performa query pencarian yang lebih cepat.
