expand tune chat_spark 

## Memahami onDelete('restrict')  pada Database Relasional 

Fungsi onDelete adalah sebuah aturan (constraint) pada Foreign Key yang memberi tahu database: "Apa yang harus sistem lakukan pada tabel Anak, jika data di tabel Induk dihapus?" 

Pada tabel invoices yang kita buat, kita secara spesifik menggunakan $table>foreignId('item_id')->constrained('items')->onDelete('restrict'); . 

Mengapa kita menggunakan restrict dan bukan yang lain? Mari kita bedah jenis-jenisnya: 

## 1. onDelete('restrict')  - Efek Sabuk Pengaman (Blokir) 

- Artinya: Mencegah (memblokir) penghapusan data induk jika masih ada data anak yang berelasi dengannya. 

- Skenario di ShowDrive: Ada Customer bernama Naufal sudah membayar DP untuk mobil Porsche 911 (Transaksi ini tersimpan di tabel invoices ). Suatu hari, ada Admin ceroboh yang iseng atau tidak sengaja menekan tombol "Hapus Mobil Porsche" di tabel items . 

- Apa yang terjadi? Sistem database (MySQL) akan langsung menolak perintah Admin tersebut dan memunculkan pesan Error Constraint. 

- Tujuan Bisnis: Memastikan integritas keuangan. Mobil tidak boleh lenyap dari database jika masih ada tagihan invoice/pembayaran pelanggan yang menggantung. Admin harus membatalkan/menghapus invoice-nya terlebih dahulu, baru mobilnya bisa dihapus. 

## 2. Bandingkan dengan onDelete('cascade') - Efek Domino (Berbahaya!) 

- Artinya: Jika data induk dihapus, maka semua data anak yang berelasi dengannya akan ikut terhapus secara otomatis. 

- Skenario di ShowDrive (Misal kita pakai ini di item_id): Jika Admin menghapus mobil Porsche 911 dari katalog, maka seketika itu juga semua riwayat invoice dan data pembayaran pelanggan yang membeli Porsche tersebut akan ikut terhapus otomatis dari database tanpa sisa! 

- Dampak: Laporan keuangan showroom akan hancur dan data pelanggan hilang. (Catatan: Cascade cocok dipakai pada tabel warehouses ke items . Jika sebuah Gudang ditutup/dihapus, wajar jika data mobil di dalamnya ikut terhapus/dipindahkan). 

## 3. Bandingkan dengan onDelete('set null') - Efek Pengosongan 

- Artinya: Jika data induk dihapus, biarkan data anak tetap ada, tapi kosongkan saja (jadikan NULL) nilai Foreign Key-nya. 

- Skenario di ShowDrive (Kita pakai ini pada cashier_id di invoice): Misal kasir bernama Aris resign dari ShowDrive dan akunnya dihapus. Kita tidak ingin menghapus riwayat invoice yang pernah Aris sahkan, kan? Maka, invoice tetap ada di database, hanya saja kolom 

- cashier_id -nya berubah menjadi kosong (NULL), menandakan kasir aslinya sudah tidak ada 

- di sistem. 

## 💡 Senjata Rahasia untuk Sidang 

Jika dosen penguji (seperti Pak Yusril) bertanya: "Kenapa relasi tabel item ke invoice pakai Restrict?" 

Kamu jawab dengan tegas: 

"Untuk menjaga Referential Integrity dan mencegah Orphaned Data (Data Yatim), Pak. Karena Invoice adalah dokumen finansial, sistem tidak boleh mengizinkan penghapusan aset (Item/Mobil) jika aset tersebut masih memiliki riwayat transaksi yang berjalan atau mengikat. Jika dipaksa dihapus, laporan akuntansi showroom akan cacat." 

