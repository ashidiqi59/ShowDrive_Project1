## **LAPORAN PROYEK RANCANG BANGUN SISTEM INFORMASI INVENTARIS DAN TRANSAKSI PENJUALAN BERBASIS WEB** 

Disusun oleh: Kelompok 18 

Fathoni Abdul Jabbar 714250039 Muhammad Aris Ashidiqi 714250042 

**PROGRAM STUDI D4 TEKNIK INFORMATIKA UNIVERSITAS LOGISTIK DAN BISNIS INTERNASIONAL** 

**BANDUNG** 

**2026** 

## **LEMBAR PERNYATAAN PERSETUJUAN DAN PERMOHONAN SIDANG PROYEK 1** 

Saya sebagai Pembimbing Kelompok .....dengan Anggota: 

|Nama<br>Mahasiswa 1|Muhammad Aris Ashidiqi|
|---|---|
|NPM|714250042|
|Nama<br>Mahasiswa 2|Fathoni Abdul Jabbar|
|NPM|714250039|
|Judul Proyek 1|Rancang Bangun Sistem Informasi Inventaris dan Transaksi<br>Penjualan Berbasis Web|



Menyatakan bahwa mahasiswa tersebut telah menyelesaikan semua luaran dengan kemajuan: …………%. Bagian yang belum diselesaikan (Jika ada) : 

………………………..…………………………………………………………… 

……………………………………………………………………………………… 

Adapun penulisan laporan Akhir Proyek …. telah diselesaikan seluruhnya (100%) Dengan demikian saya mengajukan mahasiswa tersebut untuk mengikuti sidang Proyek 1.  Apabila ternyata pernyataan saya tersebut tidak benar, maka saya menyetujui penundaan sidang termasuk pembatalan sidang Proyek 1 untuk mahasiswa bimbingan saya tersebut sesuai aturan yang berlaku. 

Bandung, …………………2026 

i 

**Mahasiswa 1 Dosen Pembimbing** …………………………… …………………………… NPM : NIK : **Mahasiswa 2** 

…………………………… NPM : 

ii 

## **LEMBAR PENGESAHAN RANCANG BANGUN SISTEM INFORMASI INVENTARIS DAN TRANSAKSI PENJUALAN BERBASIS WEB** 

Fathoni Abdul Jabbar 714250039 Muhammad Aris Ashidiqi 714250042 

Dokumen Proyek 1 ini telah diperiksa, disetujui dan disidangkan Di Bandung, 

Oleh : 

**Penguji Pendamping, Penguji Utama,** ___________________ ______________________ NIK: NIK: **Pembimbing, Koordinator Proyek 1** _____________________ ____________________ NIK: NIK: Menyetujui, 

**Ketua Program Studi D-IV Teknik Informatika,** 

_____________________ 

NIK: 

iii 

## **KATA PENGANTAR** 

Puji syukur kehadirat Tuhan Yang Maha Esa atas rahmat dan karunia-Nya, sehingga laporan teknis ini dapat terselesaikan dengan baik. Laporan ini disusun untuk mendokumentasikan proses penelitian, pengembangan, dan evaluasi yang telah dilakukan, dengan merujuk pada data teknis yang tersimpan dalam repositori `ShowDrive_Project1`. 

Laporan ini bertujuan memberikan gambaran mengenai tantangan, metodologi, dan hasil yang dicapai selama proyek berlangsung. Kami menyadari keberhasilan ini tidak lepas dari dukungan dan kerja keras seluruh pihak yang terlibat. 

Kami mengucapkan terima kasih kepada semua pihak yang telah memberikan kontribusi, baik moril maupun teknis. Kami menyadari laporan ini masih jauh dari sempurna, sehingga kritik dan saran yang membangun sangat kami harapkan untuk penyempurnaan di masa mendatang. 

Semoga laporan ini bermanfaat dan menjadi referensi bagi pihak-pihak yang membutuhkan. 

Bandung, 1 Juli 2026 

Tim Peneliti 

iv 

## **DAFTAR ISI** 

LEMBAR PERNYATAAN PERSETUJUAN DAN PERMOHONAN SIDANG PROYEK 1 ........................................................................................................................ i LEMBAR PENGESAHAN RANCANG BANGUN SISTEM INFORMASI INVENTARIS DAN TRANSAKSI PENJUALAN BERBASIS WEB .......................... iii KATA PENGANTAR ..................................................................................................... iv DAFTAR ISI .................................................................................................................... v BAB I PENDAHULUAN ................................................................................................ 1 1.1. Latar Belakang ....................................................................................................... 1 1.2. Nama Aplikasi dan Dasar Ide ................................................................................ 2 1.3. Tujuan Pengembangan .......................................................................................... 3 1.4. Ruang Lingkup ...................................................................................................... 4 BAB II  DESKRIPSI SISTEM ......................................................................................... 5 2.1. Gambaran Umum Aplikasi .................................................................................... 5 2.2. Stakeholder dan User ............................................................................................. 5 2.3. Kebutuhan Fungsional ........................................................................................... 6 2.4. Kebutuhan Non-Fungsional ................................................................................... 7 BAB III PERANCANGAN SISTEM .............................................................................. 8 3.1. Arsitektur Sistem ................................................................................................... 8 3.2 Workflow Sistem .................................................................................................... 9 3.3. Class Diagram ..................................................................................................... 10 3.4. Entity Relationship Diagram (ERD) ................................................................... 11 BAB IV DESAIN ANTARMUKA ................................................................................ 14 4.1. Konsep Desain ..................................................................................................... 14 4.2. Mockup / Wireframe ........................................................................................... 14 4.3. Deskripsi Tampilan ............................................................................................. 15 BAB V IMPLEMENTASI DASAR ............................................................................... 16 5.1. Tools dan Teknologi ............................................................................................ 16 5.2. Struktur Folder ..................................................................................................... 16 5.3. Petunjuk Menjalankan Aplikasi........................................................................... 17 BAB VI  PENUTUP ....................................................................................................... 19 6.1. Kesimpulan .......................................................................................................... 19 6.2. Saran Pengembangan ........................................................................................... 20 

v 

DAFTAR PUSTAKA ..................................................................................................... 22 

vi 

## **BAB I** 

## **PENDAHULUAN** 

## **1.1. Latar Belakang** 

Perkembangan teknologi informasi dan komunikasi yang pesat pada era digital ini telah mendorong berbagai sektor industri untuk melakukan transformasi digital dalam proses bisnis mereka. Salah satu sektor yang turut merasakan dampak signifikan dari transformasi tersebut adalah industri otomotif, khususnya pada segmen penjualan dan distribusi kendaraan bermotor. Showroom atau dealer kendaraan bermotor yang selama ini mengandalkan pencatatan manual dan pengelolaan inventaris konvensional kini dituntut untuk mengadopsi sistem informasi berbasis web yang lebih efisien, akurat, dan dapat diakses secara real-time. 

Permasalahan yang sering ditemui pada pengelolaan showroom konvensional antara lain adalah ketidakakuratan data inventaris kendaraan, sulitnya pelacakan status transaksi secara langsung oleh pelanggan, lambatnya proses verifikasi pembayaran oleh kasir, serta rentan terjadinya duplikasi data atau kehilangan riwayat transaksi yang dapat berimbas pada integritas laporan keuangan. Kondisi ini semakin diperburuk dengan meningkatnya volume transaksi dan kebutuhan pelanggan akan layanan yang cepat dan transparan. 

Penelitian ini merancang dan mengimplementasikan sistem informasi manajemen showroom kendaraan bermotor berbasis web dengan nama **ShowDrive** . Sistem ini dibangun menggunakan framework Laravel 11 sebagai backend dengan dukungan basis data relasional MySQL yang dinormalisasi ke dalam 6 entitas utama. Sistem ShowDrive menawarkan dua antarmuka utama, yaitu antarmuka publik bagi pelanggan untuk menelusuri katalog kendaraan, melakukan pemesanan, mengunggah bukti pembayaran, dan melacak status transaksi; serta antarmuka administrasi bagi kasir atau admin showroom untuk mengelola inventaris kendaraan, memverifikasi pembayaran, dan memantau metrik keuangan secara real-time. 

1 

Pemilihan framework Laravel didasarkan pada kemampuannya dalam menyediakan arsitektur yang terstruktur mengikuti pola _Model-View-Controller_ (MVC), kemudahan pengelolaan basis data melalui Eloquent ORM, serta ekosistem yang matang untuk pengembangan aplikasi web skala menengah. Dengan demikian, penelitian ini diharapkan dapat menghasilkan sistem yang mampu menjawab kebutuhan pengelolaan showroom secara menyeluruh, efisien, dan aman. 

## **1.2. Nama Aplikasi dan Dasar Ide** 

Aplikasi yang dikembangkan dalam penelitian ini diberi nama **ShowDrive** . Nama ini merupakan penggabungan dari dua kata dalam bahasa Inggris, yaitu _Show_ (menampilkan/memamerkan) dan _Drive_ (berkendara), yang secara konseptual mencerminkan fungsi inti sistem, yaitu menampilkan dan memfasilitasi proses transaksi unit kendaraan bermotor kepada pelanggan secara digital. 

Dasar ide pengembangan ShowDrive berakar dari pengamatan terhadap proses bisnis showroom kendaraan konvensional yang masih berjalan secara manual. Proses pencatatan inventaris menggunakan lembar kerja spreadsheet, komunikasi pemesanan melalui telepon atau pesan langsung, serta verifikasi pembayaran yang menuntut kehadiran fisik pelanggan merupakan hambatan-hambatan nyata yang menurunkan efisiensi operasional dan kualitas pengalaman pelanggan. 

Ide utama ShowDrive adalah memindahkan seluruh alur transaksi mulai dari penemuan katalog kendaraan, pengajuan pemesanan, pengunggahan bukti pembayaran, hingga verifikasi dan penerbitan kwitansi digital ke dalam satu platform web yang terintegrasi dan dapat diakses kapan saja dan dari mana saja. Inovasi khusus yang membedakan ShowDrive dari sistem serupa adalah: 

a) Identifikasi pelanggan tanpa akun: Pelanggan dapat melacak seluruh riwayat transaksi cukup dengan nomor telepon, tanpa perlu mendaftar atau mengingat kata sandi. 

2 

- b) Perlindungan integritas finansial berbasis database: Sistem secara aktif mencegah penghapusan aset ke.3ndaraan yang masih terikat transaksi aktif menggunakan constraint _ON DELETE RESTRICT_ pada tingkat basis data. 

- c) Operasi kasir berbasis AJAX: Kasir dapat memverifikasi puluhan transaksi secara efisien tanpa gangguan muat ulang halaman penuh. 

## **1.3. Tujuan Pengembangan** 

Pengembangan sistem ShowDrive bertujuan untuk menghasilkan sebuah aplikasi web yang mampu: 

- a) Mendigitalisasi Proses Pengelolaan Inventaris Kendaraan: Menyediakan antarmuka yang memudahkan admin/kasir dalam menambahkan, memperbarui, dan menghapus data unit kendaraan beserta dokumentasi foto galeri secara terpusat dan terstruktur. 

- b) Menyederhanakan Alur Pemesanan bagi Pelanggan: Membangun pengalaman pemesanan kendaraan yang intuitif dan mandiri bagi pelanggan, mulai dari penelusuran katalog, pengisian formulir pemesanan dengan validasi otomatis, hingga pengunggahan bukti transfer secara digital tanpa memerlukan registrasi akun. 

- c) Mempercepat Proses Validasi Transaksi oleh Kasir: Mengimplementasikan fitur verifikasi pembayaran dan persetujuan jadwal inspeksi berbasis AJAX pada dashboard administrasi, sehingga kasir dapat memproses beberapa transaksi dalam waktu singkat secara real-time. 

- d) Menjamin Keandalan dan Keamanan Data Transaksi: Menerapkan mekanisme transaksi basis data atomik untuk memastikan konsistensi data pada operasi yang melibatkan beberapa tabel sekaligus, serta mengimplementasikan lapisan keamanan autentikasi yang mencegah akses tidak sah ke panel administrasi. 

- e) 

- Menyediakan Laporan Keuangan Ringkas Secara _Real-Time_ : Menampilkan metrik performa _showroom_ secara dinamis pada 

3 

dashboard admin, mencakup total unit inventaris, jumlah unit terjual, jumlah transaksi menunggu verifikasi, dan total pendapatan. 

## **1.4. Ruang Lingkup** 

Untuk memastikan pengembangan sistem berjalan terarah dan sesuai dengan sumber daya yang tersedia, ruang lingkup proyek ShowDrive dibatasi sebagai berikut. 

Dari sisi fungsional, sistem ShowDrive menyediakan fitur katalog kendaraan dengan pencarian, formulir pemesanan kendaraan, pelacakan status transaksi via nomor HP, unggah bukti transfer pembayaran, serta cetak kwitansi/invoice digital bagi pelanggan publik. Sementara itu, admin/kasir dapat menggunakan login melalui jalur aman, dashboard metrik keuangan realtime, _CRUD_ kendaraan dengan galeri multi-gambar, verifikasi pembayaran via _AJAX_ , dan persetujuan/penolakan jadwal inspeksi. Beberapa fitur secara sengaja tidak dimasukkan ke dalam ruang lingkup, yaitu integrasi payment gateway otomatis, notifikasi SMS/WhatsApp otomatis, manajemen pengiriman fisik kendaraan, dan laporan keuangan ekspor PDF/Excel. 

Dari sisi teknologi, sistem dibangun menggunakan Laravel 11 dengan PHP 8.2 di sisi _backend_ , serta _Blade Template Engine_ , CSS, dan JavaScript _(AJAX/Fetch API)_ di sisi _frontend_ . Basis data menggunakan MySQL 8.0 dengan skema enam tabel relasional yang telah dinormalisasi, dan pengembangan dilakukan pada lingkungan server lokal Laragon 6.0 berbasis sistem operasi Windows. 

Dari sisi pengguna, sistem ini dirancang untuk digunakan oleh dua kelompok utama: pelanggan publik, yaitu siapa pun yang mengakses website showroom melalui browser tanpa memerlukan akun khusus, dan admin/kasir, yaitu staf internal showroom yang telah terdaftar dalam sistem dan memiliki kredensial untuk mengakses panel administrasi. 

4 

## **BAB II** 

## **DESKRIPSI SISTEM** 

## **2.1. Gambaran Umum Aplikasi** 

ShowDrive merupakan aplikasi manajemen showroom berbasis website yang dibangun untuk mendukung proses bisnis penjualan kendaraan, mulai dari tahap pencarian kendaraan oleh pelanggan hingga proses verifikasi pembayaran oleh pihak showroom. Aplikasi ini dikembangkan menggunakan _framework_ Laravel 11 dengan basis data MySQL 8.0. 

Secara umum, alur penggunaan aplikasi dibagi menjadi dua sisi. Pada sisi pelanggan, pengguna dapat mengakses website tanpa perlu membuat akun terlebih dahulu. Pelanggan dapat menelusuri katalog kendaraan yang tersedia, melihat detail spesifikasi dan galeri gambar, kemudian mengisi formulir pemesanan. Setelah pemesanan dibuat, pelanggan mengunggah bukti transfer pembayaran dan dapat memantau status transaksinya secara mandiri menggunakan nomor HP yang didaftarkan pada saat pemesanan. Setelah transaksi disetujui, pelanggan dapat mencetak kwitansi atau _invoice_ digital sebagai bukti transaksi yang sah. 

Pada sisi internal, admin/kasir login ke dalam sistem melalui jalur yang aman untuk mengelola data kendaraan, memverifikasi bukti pembayaran yang diunggah pelanggan, serta menyetujui atau menolak jadwal inspeksi kendaraan. Admin/kasir juga dapat memantau kondisi keuangan _showroom_ melalui _dashboard_ yang menampilkan metrik secara real-time, sehingga pengambilan keputusan operasional dapat dilakukan dengan lebih cepat dan berbasis data. 

## **2.2. Stakeholder dan User** 

Terdapat dua kelompok pengguna utama yang berinteraksi langsung dengan sistem ShowDrive. 

5 

- a) Kelompok pertama adalah pelanggan publik, yaitu calon pembeli yang mengakses website showroom melalui browser tanpa memerlukan akun khusus. Pelanggan publik memiliki hak akses untuk melihat katalog kendaraan, melakukan pencarian, mengisi formulir pemesanan, mengunggah bukti pembayaran, melacak status transaksi menggunakan nomor HP, serta mencetak kwitansi/invoice. 

- b) Kelompok kedua adalah admin/kasir, yaitu staf internal showroom yang telah terdaftar dalam sistem dan memiliki kredensial resmi. Admin/kasir memiliki hak akses untuk login melalui jalur aman, mengelola data kendaraan beserta galeri gambar, memverifikasi pembayaran, menyetujui atau menolak jadwal inspeksi, serta memantau dashboard keuangan secara real-time. Pelanggan publik berperan sebagai pengguna eksternal yang tidak memerlukan proses autentikasi, sedangkan admin/kasir berperan sebagai pengguna internal dengan tingkat akses lebih tinggi karena berhubungan langsung dengan data transaksi dan keuangan showroom. 

## **2.3. Kebutuhan Fungsional** 

Berdasarkan proses bisnis yang telah dijelaskan sebelumnya, sistem ShowDrive perlu mampu menampilkan katalog kendaraan beserta fitur pencarian, menyediakan formulir pemesanan, serta mencatat dan menampilkan status transaksi yang dapat dilacak pelanggan menggunakan nomor HP. Sistem juga harus dapat menerima unggahan bukti transfer pembayaran dan menghasilkan kwitansi/invoice digital yang dapat dicetak oleh pelanggan. 

Di sisi admin/kasir, sistem perlu menyediakan proses login yang aman, dashboard metrik keuangan secara real-time, serta fitur pengelolaan data kendaraan (tambah, lihat, ubah, hapus) beserta galeri multi-gambar. Selain itu, sistem harus dapat memverifikasi status pembayaran secara asinkron menggunakan AJAX, dan memproses persetujuan atau penolakan jadwal inspeksi kendaraan. 

6 

## **2.4. Kebutuhan Non-Fungsional** 

Selain kebutuhan fungsional, sistem ShowDrive juga harus memenuhi beberapa kebutuhan non-fungsional agar dapat digunakan secara optimal. 

- a) Dari sisi kinerja, sistem harus mampu menampilkan halaman katalog dan memproses verifikasi pembayaran dengan waktu respons yang cepat, termasuk pada proses AJAX yang berjalan tanpa memuat ulang halaman secara penuh. 

- b) Dari sisi kemudahan penggunaan, antarmuka sistem dirancang agar mudah dipahami, baik oleh pelanggan publik yang belum familiar dengan sistem, maupun oleh admin/kasir dalam menjalankan operasional sehari-hari. 

- c) Dari sisi keamanan, proses login admin/kasir dilindungi menggunakan jalur autentikasi yang aman, dan data transaksi maupun bukti pembayaran hanya dapat diakses oleh pihak yang memiliki hak akses sesuai. 

- d) Dari sisi keandalan, sistem harus dapat menyimpan data transaksi secara konsisten pada basis data MySQL yang telah dinormalisasi, sehingga meminimalkan risiko duplikasi maupun kehilangan data. Sistem juga dirancang agar kompatibel dengan browser web umum tanpa memerlukan instalasi tambahan, serta memiliki struktur basis data yang dinormalisasi ke dalam enam tabel relasional untuk memudahkan pengembangan fitur lanjutan di masa mendatang. 

7 

## **BAB III** 

## **PERANCANGAN SISTEM** 

## **3.1. Arsitektur Sistem** 

Sistem ShowDrive dibangun menggunakan arsitektur tiga lapis (threetier architecture) yang memisahkan tanggung jawab antara tampilan, logika bisnis, dan penyimpanan data. Pemisahan ini bertujuan agar sistem lebih mudah dikembangkan, diuji, dan dipelihara. 

Lapisan frontend berperan sebagai antarmuka yang berinteraksi langsung dengan pengguna, dibangun menggunakan Blade Template Engine yang dipadukan dengan CSS untuk tampilan dan JavaScript (AJAX/Fetch API) untuk komunikasi asinkron, seperti proses verifikasi pembayaran tanpa memuat ulang halaman. Lapisan backend menggunakan framework Laravel 11 yang menangani seluruh logika bisnis melalui Controller, memproses data melalui Model berbasis Eloquent ORM, dan mengatur alur permintaan (routing) dari pengguna. Lapisan database menggunakan MySQL 8.0 sebagai tempat penyimpanan data utama, dengan skema enam tabel relasional yang telah dinormalisasi untuk menjaga konsistensi dan menghindari duplikasi data. 

8 

## **3.2 Workflow Sistem** 

Alur penggunaan aplikasi ShowDrive dimulai dari titik Mulai, yang mengarahkan pengguna ke _Landing Page_ . Halaman ini menjadi titik masuk bersama sebelum sistem membagi alur berdasarkan dua peran pengguna utama, yaitu pelanggan ( _customer_ ) dan kasir ( _cashier_ ). 

9 

## 1. **Alur Pelanggan (Customer):** 

- Pelanggan mengakses katalog unit kendaraan ( _Items_ ) yang tersedia. 

- Sistem menampilkan daftar item dengan status Available. 

- Pelanggan memilih unit, lalu mengisi formulir registrasi data diri dan pemesanan. 

- Sistem memproses transaksi secara otomatis: menyisipkan data pelanggan ke tabel customers (jika belum ada), membuat rekaman transaksi baru di tabel invoices dengan status Unpaid, serta mengubah status unit di tabel items menjadi Invoiced. 

- Pelanggan mengunggah bukti transfer fisik asli ( _authentic receipt_ ) melalui fitur pelacakan invoice berbasis nomor telepon. Status transaksi berubah menjadi Pending Validation. 

## 2. **Alur Kasir (Cashier):** 

- Kasir masuk melalui halaman login aman ( _secure custom route_ ). 

- Kasir diarahkan ke _Dashboard Control Panel_ untuk memantau metrik keuangan dan tabel transaksi secara _real-time_ . 

- Kasir melihat daftar invoice masuk yang memiliki berkas bukti pembayaran. 

- Kasir melakukan validasi bukti pembayaran fisik. Jika valid, kasir menekan tombol "Sahkan Lunas". 

- Sistem mengubah status pembayaran invoice menjadi Paid, mencatat ID Kasir pengesah di kolom cashier_id, dan memperbarui status unit mobil di tabel items menjadi Sold. 

## **3.3. Class Diagram** 

Class Diagram pada sistem ShowDrive merepresentasikan pemetaan model-model Eloquent yang saling berinteraksi untuk menjalankan proses bisnis showroom. 

- **Class Company:** Menyimpan informasi profil perusahaan utama. Memiliki relasi satu-ke-banyak (1:N) dengan Warehouse dan Cashier. 

10 

- **Class Warehouse:** Menyimpan data lokasi penyimpanan fisik unit. Berelasi banyak-ke-satu (N:1) dengan Company, dan satu-ke-banyak (1:N) dengan Item. 

- **Class Cashier:** Merepresentasikan akun pengelola atau kasir yang memiliki hak akses sistem. Berelasi banyak-ke-satu (N:1) dengan Company dan satuke-banyak (1:N) dengan Invoice untuk mencatat otorisasi validasi keuangan. 

- **Class Customer:** Menyimpan informasi kontak pelanggan (Nama dan No. HP). Berelasi satu-ke-banyak (1:N) dengan Invoice. 

- **Class Item:** Merepresentasikan katalog unit kendaraan beserta spesifikasi teknisnya. Berelasi banyak-ke-satu (N:1) dengan Warehouse, dan satu-kebanyak (1:N) dengan Invoice. 

- **Class Invoice:** Berfungsi sebagai pusat pencatatan transaksi. Class ini mempertemukan relasi dari Customer, Item, dan Cashier (sebagai penanggung jawab verifikasi). 

## **3.4. Entity Relationship Diagram (ERD)** 

Skema relasi database pada aplikasi ShowDrive terdiri atas 6 entitas yang dirancang untuk memenuhi standar normalisasi database, guna menjaga integritas referensial data ( _referential integrity_ ). 

11 

12 

## **Penjelasan Relasi & Atribut Kunci:** 

1. **Companies ke Warehouses & Cashiers (** 1:N **):** Satu perusahaan (Companies) dapat membawahi beberapa gudang fisik penyimpanan (Warehouses) dan mempekerjakan banyak kasir (Cashiers). Relasi ini dihubungkan oleh Foreign Key company_id pada masing-masing tabel anak. 

2. **Warehouses ke Items (** 1:N **):** Satu gudang penyimpanan menampung banyak unit kendaraan (Items). Relasi dihubungkan oleh Foreign Key warehouse_id pada tabel items. 

3. **Customers ke Invoices (** 1:N **):** Satu pelanggan (Customers) dapat memiliki beberapa riwayat transaksi pembelian (Invoices). Relasi menggunakan Foreign Key customer_id pada tabel invoices dengan batasan ON DELETE CASCADE. 

4. **Items ke Invoices (** 1:N **):** Satu unit barang dihubungkan ke data pencatatan faktur keuangan. Relasi menggunakan Foreign Key item_id pada tabel invoices dengan batasan ketat ON DELETE RESTRICT. Aturan ini menjamin bahwa unit mobil yang memiliki riwayat transaksi aktif di dalam tabel faktur tidak dapat dihapus secara tidak sengaja oleh kasir. 

5. **Cashiers ke Invoices (** 1:N **):** Satu kasir dapat mengesahkan banyak transaksi pembayaran faktur. Kolom cashier_id pada tabel invoices bersifat nullable pada awal transaksi dan baru akan terisi dengan ID kasir ketika pembayaran telah diverifikasi. Relasi diatur dengan batasan ON DELETE SET NULL. 

13 

## **BAB IV** 

## **DESAIN ANTARMUKA** 

## **4.1. Konsep Desain** 

Desain antarmuka ShowDrive dirancang dengan prinsip sederhana dan mudah digunakan (simple and user-friendly), mengingat pengguna utama sistem ini terdiri atas dua kelompok dengan kebutuhan berbeda, yaitu pelanggan publik yang mengakses website tanpa panduan khusus, dan admin/kasir yang menggunakan sistem secara rutin dalam operasional harian. 

Tampilan untuk pelanggan publik difokuskan pada kemudahan navigasi, terutama pada proses pencarian kendaraan, pengisian formulir pemesanan, dan pelacakan status transaksi, sehingga alur transaksi dapat diselesaikan tanpa memerlukan bantuan pihak lain. Sementara itu, tampilan untuk admin/kasir dirancang agar informasi penting seperti metrik keuangan, status verifikasi pembayaran, dan jadwal inspeksi dapat terlihat secara ringkas melalui dashboard, sehingga mempercepat pengambilan keputusan operasional. 

## **4.2. Mockup / Wireframe** 

Penyusunan _mockup_ dan _wireframe_ pada sistem ShowDrive mengacu pada visualisasi kebutuhan fungsional dari masing-masing antarmuka. Gambar rancangan antarmuka disajikan secara terstruktur untuk memberikan panduan visual bagi pengembang dalam mengimplementasikan komponen desain _frontend_ . 

Rancangan halaman publik difokuskan pada penyajian katalog kendaraan yang intuitif dengan struktur grid simetris, diikuti dengan halaman detail unit yang menampilkan area galeri gambar utama dan sub-gambar pendukung secara hierarkis seperti pada visualisasi sistem nyata. Di sisi admin, rancangan difokuskan pada fleksibilitas _layouting_ tabel data dinamis dan penempatan komponen tombol aksi berbasis asinkron agar kontrol operasional dapat dilakukan dalam satu ruang kerja visual yang ringkas. 

14 

## **4.3. Deskripsi Tampilan** 

Halaman Beranda/Katalog berfungsi sebagai titik masuk utama bagi pelanggan publik, menampilkan daftar kendaraan yang tersedia lengkap dengan fitur pencarian agar pelanggan dapat menemukan kendaraan sesuai kebutuhan dengan cepat. Dari halaman ini, pelanggan dapat menuju Halaman Detail Kendaraan untuk melihat spesifikasi lengkap beserta galeri gambar sebelum melanjutkan ke Halaman Formulir Pemesanan, tempat pelanggan mengisi data diri dan konfirmasi kendaraan yang dipesan. 

Setelah pemesanan dibuat, pelanggan diarahkan ke Halaman Unggah Bukti Pembayaran untuk melampirkan bukti transfer, dan dapat memantau perkembangan transaksinya kapan saja melalui Halaman Lacak Status menggunakan nomor HP yang telah didaftarkan. 

Pada sisi internal, Halaman Login Admin menjadi gerbang akses bagi staf showroom menuju Halaman Dashboard, yang menyajikan ringkasan metrik keuangan secara real-time sebagai gambaran umum kondisi bisnis. Admin/kasir dapat mengelola data kendaraan melalui Halaman Kelola Kendaraan, memproses transaksi masuk melalui Halaman Verifikasi Pembayaran yang bekerja secara asinkron tanpa memuat ulang halaman, serta menentukan kelayakan jadwal melalui Halaman Jadwal Inspeksi. 

15 

## **BAB V** 

## **IMPLEMENTASI DASAR** 

## **5.1. Tools dan Teknologi** 

Pengembangan sistem ShowDrive memanfaatkan sejumlah tools dan teknologi berikut: bahasa pemrograman PHP 8.2 dengan _framework backend_ Laravel 11; sisi _frontend_ menggunakan Blade Template Engine, HTML, CSS, dan JavaScript ( _AJAX/Fetch API_ ); basis data MySQL 8.0; lingkungan pengembangan lokal Laragon 6.0 berbasis Windows; version control Git dan GitHub dengan repositori ShowDrive_Project1; _code editor_ Visual Studio Code; serta manajemen dependensi menggunakan Composer untuk PHP dan NPM untuk JavaScript/CSS. 

## **5.2. Struktur Folder** 

Aplikasi ShowDrive dikembangkan menggunakan pola arsitektur standar framework Laravel 11. Struktur direktori utama diatur sebagai berikut: 

- **app/Http/Controllers/** : Berisi pengendali logika bisnis utama aplikasi. 

   - AuthController.php: Menangani otentikasi login dan logout akun Kasir/Admin. 

   - AdminController.php: Menangani fungsi-fungsi dashboard kontrol panel, CRUD unit kendaraan, dan eksekusi transaksi verifikasi pembayaran. 

   - PublicController.php: Melayani pencarian katalog publik, detail unit, serta pembuatan transaksi booking dari sisi pelanggan. 

- **app/Models/** : Berisi representasi objek tabel database menggunakan Eloquent ORM. 

   - Company.php: Model data profil perusahaan. 

   - Warehouse.php: Model data lokasi gudang penyimpanan. 

   - Cashier.php: Model data kasir (menggunakan pewarisan _Authenticatable_ untuk sistem keamanan login). 

   - Customer.php: Model data identitas pelanggan. 

16 

   - Item.php: Model data spesifikasi unit kendaraan. 

   - Invoice.php: Model data transaksi keuangan faktur. 

- **database/migrations/** : Berisi berkas skema pembuatan 6 tabel relasional yang dieksekusi ke server basis data MySQL. 

- **resources/views/** : Berisi halaman-halaman antarmuka pengguna berbasis Blade HTML. 

   - layouts/app.blade.php: Kerangka induk ( _layouting_ ) desain web. 

   - admin/dashboard.blade.php: Halaman antarmuka manajemen data internal kasir. 

   - public/: Direktori yang berisi halaman beranda katalog dan halaman detail unit kendaraan untuk pelanggan. 

- **routes/web.php** : Berisi pengaturan pemetaan URL ( _routing_ ) aplikasi, termasuk penerapan pengalihan URL rahasia untuk keamanan portal login admin. 

## **5.3. Petunjuk Menjalankan Aplikasi** 

Berikut langkah-langkah untuk menjalankan aplikasi ShowDrive pada lingkungan pengembangan lokal: 

- a) Clone repositori proyek dari GitHub menggunakan perintah git clone https://github.com/username/ShowDrive_Project1.git. 

- b) Masuk ke direktori proyek lalu instal dependensi PHP menggunakan perintah composer install. 

- c) Salin file .env.example menjadi .env, kemudian sesuaikan konfigurasi basis data (nama database, username, password) sesuai pengaturan MySQL pada Laragon. 

- d) Jalankan perintah php artisan key:generate untuk membuat application key. 

- e) Jalankan migrasi basis data menggunakan php artisan migrate untuk membuat seluruh tabel, dan bila diperlukan jalankan php artisan db:seed untuk mengisi data contoh. 

17 

- f) Jalankan server lokal menggunakan php artisan serve, lalu buka browser dan akses aplikasi melalui alamat http://localhost:8000. 

18 

## **BAB VI** 

## **PENUTUP** 

## **6.1. Kesimpulan** 

Berdasarkan proses perancangan, desain antarmuka, dan implementasi dasar yang telah dijabarkan pada bab-bab sebelumnya, dapat disimpulkan bahwa aplikasi ShowDrive berhasil dikembangkan sebagai sistem manajemen showroom kendaraan berbasis website yang menghubungkan dua kelompok pengguna utama, yaitu pelanggan publik dan admin/kasir, dalam satu platform terpadu. 

Dari sisi pelanggan, aplikasi telah memenuhi tujuan utamanya dalam memberikan kemudahan bagi pengguna untuk menelusuri katalog kendaraan, melakukan pemesanan, mengunggah bukti pembayaran, serta memantau status transaksi secara mandiri tanpa memerlukan akun khusus. Dari sisi internal, aplikasi juga telah memenuhi kebutuhan admin/kasir dalam mengelola data kendaraan, memverifikasi pembayaran, menyetujui atau menolak jadwal inspeksi, serta memantau kondisi keuangan showroom melalui dashboard secara real-time. 

Secara arsitektur, penerapan pola three-tier menggunakan Laravel 11 sebagai backend, Blade Template Engine dengan JavaScript ( _AJAX/Fetch API_ ) sebagai frontend, serta MySQL 8.0 sebagai basis data yang telah dinormalisasi, terbukti mampu mendukung seluruh proses bisnis yang telah dirancang pada tahap perencanaan. Dengan demikian, secara keseluruhan tujuan pengembangan aplikasi ShowDrive sebagaimana dirumuskan pada ruang lingkup proyek telah tercapai, meskipun aplikasi ini masih berada pada tahap implementasi dasar dan belum mencakup fitur-fitur lanjutan yang sebelumnya telah ditetapkan berada di luar ruang lingkup, seperti integrasi payment gateway otomatis dan notifikasi otomatis via SMS/WhatsApp. 

19 

## **6.2. Saran Pengembangan** 

Sebagai tindak lanjut dari proyek ini, terdapat beberapa aspek yang dapat dikembangkan lebih lanjut untuk meningkatkan fungsionalitas dan nilai guna aplikasi ShowDrive, di antaranya: 

- a) Integrasi Payment Gateway Otomatis: menambahkan integrasi dengan penyedia payment gateway (seperti _Midtrans_ atau _Xendit_ ) agar proses pembayaran dapat diverifikasi secara otomatis, tanpa memerlukan pengecekan manual oleh admin/kasir terhadap bukti transfer yang diunggah. 

- b) Notifikasi Otomatis via SMS/WhatsApp: menambahkan sistem notifikasi otomatis kepada pelanggan pada setiap perubahan status transaksi, seperti saat pembayaran terverifikasi atau jadwal inspeksi disetujui, sehingga pelanggan tidak perlu memeriksa status secara manual. 

- c) Manajemen Pengiriman Fisik Kendaraan: menambahkan modul untuk melacak proses pengiriman kendaraan setelah transaksi selesai, termasuk status logistik dan estimasi waktu tiba. 

- d) Laporan Keuangan dengan Ekspor PDF/Excel: mengembangkan fitur pelaporan yang memungkinkan admin/kasir mengunduh laporan transaksi dan keuangan dalam format PDF atau Excel untuk kebutuhan arsip maupun audit. 

- e) Peningkatan Keamanan Sistem: menerapkan mekanisme keamanan tambahan seperti autentikasi dua faktor (2FA) untuk akun admin/kasir, serta enkripsi data pada informasi sensitif seperti bukti pembayaran pelanggan. 

- f) Optimasi Tampilan Responsif: melakukan pengujian dan penyempurnaan tampilan antarmuka agar dapat diakses secara optimal melalui perangkat mobile, mengingat sebagian besar pelanggan publik kemungkinan akan mengakses aplikasi melalui smartphone. 

- g) Pengujian Sistem secara Menyeluruh: melakukan pengujian lebih lanjut, baik dari sisi fungsional (functional testing) maupun keamanan (security testing), untuk memastikan aplikasi siap digunakan pada lingkungan produksi (deployment). 

20 

Dengan dilakukannya pengembangan lanjutan pada aspek-aspek tersebut, diharapkan aplikasi ShowDrive dapat berkembang menjadi sistem manajemen showroom yang lebih lengkap, aman, dan siap digunakan secara nyata oleh pelaku usaha di bidang penjualan kendaraan. 

21 

## **DAFTAR PUSTAKA** 

Connolly, T., & Begg, C. (2015). _Database Systems: A Practical Approach to Design, Implementation, and Management_ (6th ed.). Pearson Education. 

Date, C. J. (2019). _Database Design and Relational Theory: Normal Forms and All That Jazz_ (2nd ed.). Apress. 

Fowler, M. (2004). _UML Distilled: A Brief Guide to the Standard Object Modeling Language_ (3rd ed.). Addison-Wesley. 

Laravel. (2024). _Laravel 11.x Documentation_ . Diakses dari https://laravel.com/docs/11.x 

MDN Web Docs. (2024). _JavaScript Reference_ . Mozilla. Diakses dari - https://developer.mozilla.org/en US/docs/Web/JavaScript 

MySQL. (2024). _MySQL 8.0 Reference Manual_ . Oracle Corporation. Diakses dari https://dev.mysql.com/doc/refman/8.0/en/ 

PHP Group. (2024). _PHP Manual_ . Diakses dari https://www.php.net/manual/en/ Pressman, R. S., & Maxim, B. R. (2020). _Software Engineering: A Practitioner's Approach_ (9th ed.). McGraw-Hill Education. 

Silberschatz, A., Korth, H. F., & Sudarshan, S. (2020). _Database System Concepts_ (7th ed.). McGraw-Hill Education. 

Sommerville, I. (2016). _Software Engineering_ (10th ed.). Pearson Education. 

W3Schools. (2024). _AJAX Tutorial_ . Diakses dari 

https://www.w3schools.com/xml/ajax_intro.asp 

22 

