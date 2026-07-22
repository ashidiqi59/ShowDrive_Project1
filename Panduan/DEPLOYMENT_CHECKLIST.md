# Deployment Checklist — ShowDrive

> Ikuti urutan ini dari atas ke bawah. Setiap item punya checkbox `[ ]` — centang setelah selesai.

---

## Fase 1 — Persiapan Lokal (Sebelum Upload ke Server)

### 1.1 Build Asset Frontend

```bash
npm run build
```

Ini menghasilkan folder `public/build/` yang berisi CSS dan JS yang sudah di-minify.
Pastikan tidak ada error sebelum upload.

- [ ] `npm run build` selesai tanpa error
- [ ] Folder `public/build/` ada dan berisi file

---

### 1.2 Hapus File Development yang Tidak Perlu

Jangan upload folder ini ke server (sudah ada di `.gitignore`, tapi pastikan):

- [ ] Folder `node_modules/` **tidak** diupload
- [ ] Folder `vendor/` **tidak** diupload (akan diinstall ulang di server via composer)
- [ ] File `.env` **tidak** diupload via Git (buat manual di server)

---

## Fase 2 — Setup di Server

### 2.1 Upload File

Cara yang disarankan: gunakan **Git** (paling bersih) atau FTP/cPanel File Manager.

**Via Git:**
```bash
git add .
git commit -m "deploy: production build"
git push origin main
```
Lalu di server:
```bash
git pull origin main
```

**Via FTP/cPanel:** Upload semua file kecuali `node_modules/` dan `vendor/`.

- [ ] Semua file sudah ada di server

---

### 2.2 Install Dependencies PHP

Di terminal server (SSH), masuk ke root project lalu jalankan:

```bash
composer install --optimize-autoloader --no-dev
```

Flag `--no-dev` penting — tidak menginstall package development (faker, phpunit, dll) di production.

- [ ] `composer install --no-dev` selesai tanpa error
- [ ] Folder `vendor/` terbentuk di server

---

### 2.3 Buat File `.env` di Server

**Jangan copy `.env` lokal**. Buat baru di server. Template lengkap di bawah — sesuaikan nilai yang diberi tanda `← GANTI`:

```env
APP_NAME=ShowDrive
APP_ENV=production
APP_KEY=                        ← dikosongkan dulu, akan di-generate di langkah 2.4
APP_DEBUG=false                 ← WAJIB false di production
APP_URL=https://namadomain.com  ← GANTI dengan domain asli (pakai https://)
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

# --- LOGGING ---
LOG_CHANNEL=daily
LOG_LEVEL=error                 ← Hanya log error (bukan debug) di production

# --- DATABASE --- ← GANTI semua nilai DB dengan credentials server
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=showdrive_db        ← nama DB di server
DB_USERNAME=user_db             ← username DB di server
DB_PASSWORD=password_db_kuat    ← password DB di server

# --- SESSION ---
SESSION_DRIVER=database         ← WAJIB database di production
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true      ← WAJIB true jika pakai HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# --- CACHE ---
CACHE_STORE=database            ← Ganti ke redis jika server support Redis

# --- QUEUE ---
QUEUE_CONNECTION=database

# --- MAIL --- ← GANTI jika ingin email aktif
MAIL_MAILER=smtp
MAIL_HOST=smtp.namahost.com
MAIL_PORT=587
MAIL_USERNAME=email@domain.com
MAIL_PASSWORD=password_email
MAIL_FROM_ADDRESS="noreply@namadomain.com"
MAIL_FROM_NAME="ShowDrive"

FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log
VITE_APP_NAME="${APP_NAME}"

# --- TOKEN GATEWAY ADMIN (salin dari .env lokal) ---
SD_GATEWAY_TOKEN=SD_STEALTH_AUTH_2026
```

> **Catatan penting:** `SD_GATEWAY_TOKEN` adalah token untuk akses halaman login tersembunyi.
> Nilai ini ada di `.env` lokal kamu (`SD_STEALTH_AUTH_2026`). Salin sama persis ke server.

- [ ] File `.env` sudah dibuat di root project di server
- [ ] `APP_ENV=production` ✓
- [ ] `APP_DEBUG=false` ✓
- [ ] `APP_URL` sudah pakai `https://` dan domain yang benar ✓
- [ ] Credentials DB sudah diisi dengan nilai server ✓
- [ ] `SESSION_DRIVER=database` ✓
- [ ] `SESSION_SECURE_COOKIE=true` ✓
- [ ] `SD_GATEWAY_TOKEN` sudah disalin ✓

---

### 2.4 Generate APP_KEY

```bash
php artisan key:generate
```

Ini mengisi `APP_KEY` di `.env` secara otomatis. **Wajib dijalankan sekali** — tanpa ini semua session dan enkripsi rusak.

- [ ] `APP_KEY` sudah terisi di `.env` (format: `base64:xxx...`)

---

### 2.5 Jalankan Migrasi Database

```bash
php artisan migrate --force
```

Flag `--force` dibutuhkan karena di production Laravel akan meminta konfirmasi interaktif.

Ini akan membuat semua tabel yang dibutuhkan, termasuk tabel `sessions` (dibutuhkan karena `SESSION_DRIVER=database`).

- [ ] Migrasi selesai tanpa error
- [ ] Tabel `sessions` terbentuk di database

---

### 2.6 Buat Storage Symbolic Link

```bash
php artisan storage:link
```

Tanpa ini, foto unit kendaraan dan bukti transfer pelanggan tidak akan bisa diakses lewat browser.

> Di shared hosting (cPanel), kalau SSH tidak tersedia, bisa jalankan lewat **Terminal** di cPanel atau lewat file `.php` sementara.

- [ ] `php artisan storage:link` berhasil
- [ ] Link `public/storage` → `storage/app/public` terbentuk
- [ ] Coba akses salah satu URL foto unit — pastikan gambar muncul

---

### 2.7 Optimize untuk Production

```bash
php artisan optimize
php artisan view:cache
```

`optimize` meng-cache config, route, dan event. `view:cache` meng-compile semua Blade template.
Hasilnya: halaman lebih cepat load karena tidak perlu parse ulang setiap request.

- [ ] `php artisan optimize` selesai
- [ ] `php artisan view:cache` selesai

---

## Fase 3 — Cron Job (Scheduled Tasks)

Dua command sudah terjadwal di `routes/console.php`:
- `bookings:cancel-expired` — setiap jam, auto-cancel booking Unpaid > 24 jam
- `backup:storage` — setiap Minggu pukul 02:00, backup file upload

Untuk mengaktifkan, daftarkan **satu** cron entry di server yang menjalankan Laravel Scheduler:

### Di cPanel (Shared Hosting)

1. Login cPanel → **Cron Jobs**
2. Set interval: `* * * * *` (setiap menit)
3. Command (sesuaikan path ke root project):
   ```
   cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```
   Atau jika project ada di subfolder:
   ```
   cd /home/username/public_html/showdrive && php artisan schedule:run >> /dev/null 2>&1
   ```

### Di VPS/Dedicated (SSH)

```bash
crontab -e
```
Tambahkan baris:
```
* * * * * cd /var/www/showdrive && php artisan schedule:run >> /dev/null 2>&1
```

- [ ] Cron job sudah didaftarkan di server
- [ ] Test manual: `php artisan schedule:run` tidak error

---

## Fase 4 — Queue Worker (Untuk DeleteOldImageJob)

`DeleteOldImageJob` (hapus foto lama saat re-upload bukti transfer) berjalan via Laravel Queue.
Tanpa queue worker aktif, job akan ngantre tapi tidak pernah dieksekusi.

### Di Shared Hosting (tanpa supervisor)

Jalankan sekali via SSH:
```bash
php artisan queue:work --tries=3 --sleep=3 --timeout=60 &
```

> Masalah: queue worker akan mati jika koneksi SSH terputus atau server restart.
> Solusi: gunakan cron sebagai workaround — tambahkan satu baris lagi di cron:
> ```
> * * * * * cd /path/project && php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
> ```
> `--stop-when-empty` memastikan proses berhenti setelah queue habis, lalu cron memulainya lagi setiap menit.

### Di VPS (dengan Supervisor — disarankan)

```bash
sudo apt install supervisor
```

Buat file config `/etc/supervisor/conf.d/showdrive-queue.conf`:
```ini
[program:showdrive-queue]
command=php /var/www/showdrive/artisan queue:work --tries=3 --sleep=3 --timeout=60
directory=/var/www/showdrive
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/showdrive/storage/logs/queue.log
```

Lalu:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start showdrive-queue
```

- [ ] Queue worker sudah berjalan (cek: `php artisan queue:monitor`)
- [ ] Test: upload ulang bukti transfer → foto lama terhapus dalam beberapa detik

---

## Fase 5 — Verifikasi Akhir

Lakukan pengecekan ini setelah semua langkah di atas selesai:

### Fungsional
- [ ] Halaman beranda (`/`) terbuka, katalog kendaraan muncul dengan foto
- [ ] Halaman detail unit muncul, foto unit tampil
- [ ] Form booking bisa disubmit, redirect ke halaman sukses
- [ ] Tracking booking bisa diakses, OTP flow berjalan
- [ ] Upload bukti transfer berhasil
- [ ] Halaman login admin (`/pintu-akses-masuk-showdrive?gateway_token=SD_STEALTH_AUTH_2026`) bisa diakses
- [ ] Login admin berhasil, dashboard muncul
- [ ] Admin bisa approve/reject invoice
- [ ] WhatsApp share button di halaman detail berfungsi
- [ ] OG preview: coba share link ke WhatsApp/Telegram, pastikan muncul gambar & deskripsi

### Keamanan
- [ ] Buka DevTools browser → Network → cek tidak ada stack trace PHP di response error
- [ ] Coba akses `/admin/dashboard` tanpa login — harus dapat 404 (bukan redirect ke login)
- [ ] `APP_DEBUG=false` terkonfirmasi (bisa cek dengan sengaja trigger error 404)
- [ ] HTTPS aktif, semua URL menggunakan `https://`

### Performance
- [ ] `php artisan optimize` sudah dijalankan
- [ ] Cek `storage/framework/cache/` — ada file cache config dan route

---

## Fase 6 — Setelah Go Live

- [ ] Hapus/nonaktifkan seeder data dummy jika ada
- [ ] Buat akun admin production yang password-nya kuat (bukan default)
- [ ] Simpan credential DB, APP_KEY, dan SD_GATEWAY_TOKEN di tempat aman (password manager)
- [ ] Catat tanggal deploy pertama

---

## Referensi Cepat — Artisan Commands di Server

| Tujuan | Command |
|---|---|
| Cek semua route terdaftar | `php artisan route:list` |
| Clear semua cache | `php artisan optimize:clear` |
| Cek status queue | `php artisan queue:monitor` |
| Jalankan backup manual | `php artisan backup:storage` |
| Cancel booking expired manual | `php artisan bookings:cancel-expired` |
| Masuk maintenance mode | `php artisan down` |
| Keluar maintenance mode | `php artisan up` |
| Cek versi Laravel | `php artisan --version` |

---

*Dokumen ini dibuat khusus untuk ShowDrive Project — disesuaikan dengan konfigurasi aktual project.*
