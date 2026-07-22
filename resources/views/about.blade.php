@extends('layouts.app')

@section('title', 'Tentang ShowDrive — Platform Manajemen Showroom Otomotif Premium')

@section('og_description', 'Pelajari lebih lanjut tentang ShowDrive — sistem informasi manajemen showroom otomotif premium berbasis web yang dibangun oleh mahasiswa D4 Teknik Informatika ULBI.')

@section('og_image', isset($company) && $company?->logo_url ? asset('storage/'.$company->logo_url) : asset('images/og-default.jpg'))

@section('content')

{{-- ══════════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-black border-b border-zinc-900">
    {{-- Background subtle pattern --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 39px, #D4AF37 39px, #D4AF37 40px), repeating-linear-gradient(90deg, transparent, transparent 39px, #D4AF37 39px, #D4AF37 40px);"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black via-black/80 to-luxury-darkBg"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-20 md:py-28">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
            <span class="text-[10px] font-bold tracking-[0.35em] text-zinc-500 uppercase">Tentang Platform</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-none mb-4">
            SHOW<span class="text-luxury-gold">DRIVE</span>
        </h1>
        <p class="text-zinc-400 text-sm md:text-base max-w-2xl leading-relaxed font-light">
            Sistem informasi manajemen showroom otomotif premium berbasis web — dirancang untuk mengintegrasikan katalog kendaraan, alur reservasi, verifikasi pembayaran, dan pelaporan keuangan dalam satu platform yang efisien.
        </p>
        <div class="w-16 h-[2px] bg-luxury-gold mt-8"></div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     TENTANG PLATFORM
══════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        <div>
            <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-2">Latar Belakang</span>
            <h2 class="text-2xl md:text-3xl font-black tracking-wide mb-6 leading-tight">Apa itu ShowDrive?</h2>
            <div class="space-y-4 text-zinc-400 text-sm leading-relaxed">
                <p>
                    ShowDrive adalah sebuah <strong class="text-zinc-200">Sistem Informasi Manajemen Showroom Otomotif Premium</strong> berbasis web yang dibangun menggunakan framework Laravel. Platform ini menjadi solusi digital bagi showroom kendaraan mewah untuk mengelola inventaris, menerima reservasi dari pelanggan, memvalidasi pembayaran, dan menghasilkan laporan keuangan secara real-time.
                </p>
                <p>
                    Proyek ini dikembangkan sebagai <strong class="text-zinc-200">Tugas Akhir (Proyek 1)</strong> pada program studi D4 Teknik Informatika di Universitas Logistik & Bisnis Internasional (ULBI), dengan fokus pada penerapan prinsip rekayasa perangkat lunak yang baik — mulai dari arsitektur MVC, keamanan berlapis, hingga user experience yang intuitif.
                </p>
                <p>
                    Sistem ini menangani seluruh alur bisnis showroom: dari pelanggan menemukan unit kendaraan di katalog publik, melakukan reservasi jadwal temu, mengunggah bukti pembayaran, hingga admin memverifikasi dan mencetak invoice resmi — semua dalam satu platform terpadu.
                </p>
            </div>
        </div>

        {{-- Fitur Unggulan Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
                $features = [
                    ['icon' => 'fa-car-side', 'color' => 'text-luxury-gold', 'bg' => 'bg-yellow-950/40 border-yellow-900/30',
                     'title' => 'Katalog Kendaraan', 'desc' => 'Grid katalog publik dengan filter pencarian brand, model, dan VIN. Setiap unit dilengkapi galeri foto dan spesifikasi lengkap.'],
                    ['icon' => 'fa-calendar-check', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-950/40 border-emerald-900/30',
                     'title' => 'Sistem Reservasi', 'desc' => 'Formulir booking online dengan validasi real-time. Dilindungi DB transaction + lockForUpdate() untuk mencegah race condition.'],
                    ['icon' => 'fa-shield-halved', 'color' => 'text-blue-400', 'bg' => 'bg-blue-950/40 border-blue-900/30',
                     'title' => 'Autentikasi OTP', 'desc' => 'Pelanggan mengakses riwayat booking via verifikasi OTP berbasis nomor WhatsApp — tanpa perlu membuat akun.'],
                    ['icon' => 'fa-file-invoice-dollar', 'color' => 'text-violet-400', 'bg' => 'bg-violet-950/40 border-violet-900/30',
                     'title' => 'Invoice & Laporan', 'desc' => 'Cetak invoice PDF resmi, laporan keuangan dengan filter periode, dan ekspor data CSV untuk analisis lebih lanjut.'],
                    ['icon' => 'fa-check-double', 'color' => 'text-amber-400', 'bg' => 'bg-amber-950/40 border-amber-900/30',
                     'title' => 'Validasi Pembayaran', 'desc' => 'Admin memverifikasi bukti transfer, mengelola status booking, dan mengirim notifikasi WhatsApp prefilled ke pelanggan.'],
                    ['icon' => 'fa-share-nodes', 'color' => 'text-green-400', 'bg' => 'bg-green-950/40 border-green-900/30',
                     'title' => 'Social Preview', 'desc' => 'Meta tags Open Graph lengkap di setiap halaman agar link yang dibagikan ke WhatsApp/Telegram tampil dengan preview yang rapi.'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="bg-zinc-950 border {{ $f['bg'] }} p-4 hover:border-zinc-700 transition-all group">
                    <div class="flex items-center gap-2.5 mb-2">
                        <i class="fa-solid {{ $f['icon'] }} {{ $f['color'] }} text-sm"></i>
                        <h4 class="text-xs font-bold text-white tracking-wide">{{ $f['title'] }}</h4>
                    </div>
                    <p class="text-zinc-500 text-[11px] leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     TECH STACK
══════════════════════════════════════════════ --}}
<section class="border-t border-zinc-900 bg-zinc-950/50">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-2">Dibangun Dengan</span>
            <h2 class="text-2xl md:text-3xl font-black tracking-wide">Technology Stack</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $stacks = [
                    ['name' => 'Laravel 12', 'role' => 'Backend Framework', 'icon' => 'fa-brands fa-laravel', 'color' => 'text-red-400', 'desc' => 'MVC, Eloquent ORM, Queue, Artisan Commands, DB Transactions'],
                    ['name' => 'MySQL', 'role' => 'Relational Database', 'icon' => 'fa-solid fa-database', 'color' => 'text-blue-400', 'desc' => 'Foreign key constraints, indexing, lockForUpdate() untuk concurrency control'],
                    ['name' => 'Tailwind CSS', 'role' => 'UI Framework', 'icon' => 'fa-solid fa-palette', 'color' => 'text-cyan-400', 'desc' => 'Utility-first CSS dengan tema dark premium dan sistem warna luxury-gold'],
                    ['name' => 'Alpine.js', 'role' => 'Frontend Reaktif', 'icon' => 'fa-solid fa-bolt', 'color' => 'text-green-400', 'desc' => 'Interaktivitas modal, validasi form real-time, dan state management ringan'],
                    ['name' => 'PHP 8.2+', 'role' => 'Runtime Language', 'icon' => 'fa-brands fa-php', 'color' => 'text-violet-400', 'desc' => 'Typed properties, match expressions, fibers, dan readonly classes'],
                    ['name' => 'Blade Engine', 'role' => 'Template Engine', 'icon' => 'fa-solid fa-code', 'color' => 'text-orange-400', 'desc' => 'Component-based views dengan @section, @yield, dan @stack inheritance'],
                    ['name' => 'WhatsApp API', 'role' => 'Notifikasi', 'icon' => 'fa-brands fa-whatsapp', 'color' => 'text-emerald-400', 'desc' => 'Deep link wa.me dengan pesan pre-filled untuk notifikasi admin ke pelanggan'],
                    ['name' => 'Laravel Queue', 'role' => 'Background Jobs', 'icon' => 'fa-solid fa-gears', 'color' => 'text-yellow-400', 'desc' => 'DeleteOldImageJob untuk hapus file lama secara async tanpa blocking request'],
                ];
            @endphp
            @foreach($stacks as $s)
                <div class="bg-zinc-950 border border-zinc-900 p-5 hover:border-zinc-700 transition-all group">
                    <i class="fa-solid {{ $s['icon'] }} {{ $s['color'] }} text-2xl mb-3 block"></i>
                    <h4 class="text-white font-black text-sm tracking-wide mb-0.5">{{ $s['name'] }}</h4>
                    <p class="text-luxury-gold text-[10px] font-bold uppercase tracking-wider mb-2">{{ $s['role'] }}</p>
                    <p class="text-zinc-600 text-[10px] leading-relaxed">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     ARSITEKTUR SISTEM
══════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-6 py-16 border-t border-zinc-900">
    <div class="text-center mb-12">
        <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-2">Alur Sistem</span>
        <h2 class="text-2xl md:text-3xl font-black tracking-wide">Bagaimana Cara Kerjanya?</h2>
    </div>

    <div class="relative">
        {{-- Connector line (desktop) --}}
        <div class="hidden md:block absolute top-10 left-[10%] right-[10%] h-px bg-gradient-to-r from-transparent via-luxury-gold/30 to-transparent"></div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $steps = [
                    ['num' => '01', 'icon' => 'fa-magnifying-glass', 'color' => 'text-luxury-gold border-luxury-gold/40 bg-yellow-950/30',
                     'title' => 'Temukan Unit', 'desc' => 'Pelanggan menjelajahi katalog kendaraan, melihat foto & spesifikasi lengkap, membandingkan harga, dan berbagi unit via WhatsApp.'],
                    ['num' => '02', 'icon' => 'fa-calendar-plus', 'color' => 'text-blue-400 border-blue-500/40 bg-blue-950/30',
                     'title' => 'Buat Reservasi', 'desc' => 'Isi form booking online. Sistem mencatat data, mengunci unit, dan mengirimkan nomor invoice ke pelanggan secara instan.'],
                    ['num' => '03', 'icon' => 'fa-money-bill-transfer', 'color' => 'text-amber-400 border-amber-500/40 bg-amber-950/30',
                     'title' => 'Transfer & Upload', 'desc' => 'Pelanggan transfer ke rekening showroom, lalu unggah bukti bayar via halaman tracking yang diamankan dengan OTP.'],
                    ['num' => '04', 'icon' => 'fa-circle-check', 'color' => 'text-emerald-400 border-emerald-500/40 bg-emerald-950/30',
                     'title' => 'Verifikasi & Serah Terima', 'desc' => 'Admin memvalidasi pembayaran, mencetak invoice resmi, dan mengkonfirmasi serah terima fisik unit kendaraan.'],
                ];
            @endphp
            @foreach($steps as $step)
                <div class="relative flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full border-2 {{ $step['color'] }} flex items-center justify-center mb-4 relative z-10">
                        <i class="fa-solid {{ $step['icon'] }} text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black tracking-[0.3em] text-zinc-600 mb-1">STEP {{ $step['num'] }}</span>
                    <h4 class="text-white font-black text-sm mb-2 tracking-wide">{{ $step['title'] }}</h4>
                    <p class="text-zinc-500 text-[11px] leading-relaxed max-w-xs">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     FITUR KEAMANAN
══════════════════════════════════════════════ --}}
<section class="border-t border-zinc-900 bg-zinc-950/50">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-2">Keamanan Sistem</span>
                <h2 class="text-2xl md:text-3xl font-black tracking-wide mb-6">Dibangun dengan Security-First</h2>
                <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                    Setiap fitur dirancang dengan lapisan keamanan yang mempertimbangkan ancaman nyata — mulai dari input publik hingga aksi admin yang sensitif.
                </p>
                <div class="space-y-3">
                    @php
                        $securities = [
                            ['icon' => 'fa-lock', 'color' => 'text-luxury-gold', 'title' => 'DB Transaction + lockForUpdate()',
                             'desc' => 'Mencegah double-booking saat dua pengguna menekan tombol reservasi bersamaan (race condition).'],
                            ['icon' => 'fa-shield-halved', 'color' => 'text-blue-400', 'title' => 'OTP Session Authentication',
                             'desc' => 'Akses dashboard pelanggan diamankan dengan OTP 4-digit + bcrypt hash, expire 5 menit, maks 3x percobaan.'],
                            ['icon' => 'fa-gauge-high', 'color' => 'text-amber-400', 'title' => 'Rate Limiting',
                             'desc' => 'Endpoint OTP, verifikasi, booking, dan login masing-masing dibatasi dengan rate limiter berbeda untuk mencegah brute force.'],
                            ['icon' => 'fa-user-secret', 'color' => 'text-emerald-400', 'title' => 'Stealth Admin Login',
                             'desc' => 'URL login admin tidak standar dan memerlukan gateway token — akses langsung ke /login menghasilkan 404.'],
                            ['icon' => 'fa-file-shield', 'color' => 'text-violet-400', 'title' => 'Upload Validation',
                             'desc' => 'Upload bukti transfer dibatasi MIME type JPEG/PNG/WebP saja. SVG diblokir eksplisit untuk mencegah stored XSS.'],
                            ['icon' => 'fa-triangle-exclamation', 'color' => 'text-red-400', 'title' => 'Strict Eloquent Mode',
                             'desc' => 'preventLazyLoading(), preventSilentlyDiscardingAttributes() aktif di non-production untuk deteksi bug sedini mungkin.'],
                        ];
                    @endphp
                    @foreach($securities as $sec)
                        <div class="flex items-start gap-3 p-3 bg-zinc-900/40 border border-zinc-900 hover:border-zinc-700 transition-all">
                            <div class="w-8 h-8 flex items-center justify-center bg-zinc-950 border border-zinc-800 shrink-0 mt-0.5">
                                <i class="fa-solid {{ $sec['icon'] }} {{ $sec['color'] }} text-xs"></i>
                            </div>
                            <div>
                                <h5 class="text-white text-xs font-bold mb-0.5">{{ $sec['title'] }}</h5>
                                <p class="text-zinc-500 text-[11px] leading-relaxed">{{ $sec['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Database Schema Summary --}}
            <div class="bg-zinc-950 border border-zinc-900 p-6">
                <h4 class="text-xs font-bold tracking-[0.25em] text-zinc-400 uppercase mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-luxury-gold"></i> Struktur Database Utama
                </h4>
                <div class="space-y-2 font-mono text-[11px]">
                    @php
                        $tables = [
                            ['name' => 'users', 'color' => 'text-violet-400', 'fields' => 'id, name, email, password, role'],
                            ['name' => 'items', 'color' => 'text-luxury-gold', 'fields' => 'id, warehouse_id, brand, model, vin, year, price, dp_percentage, status, color, engine, transmission, image_url'],
                            ['name' => 'item_images', 'color' => 'text-yellow-600', 'fields' => 'id, item_id→items, image_path'],
                            ['name' => 'customers', 'color' => 'text-blue-400', 'fields' => 'id, name, phone [unique], nik [unique]'],
                            ['name' => 'invoices', 'color' => 'text-emerald-400', 'fields' => 'id, invoice_code, customer_id→customers, item_id→items, cashier_id→users, date, subtotal, tax_rate, tax_amount, total_amount, paid_amount, payment_type, payment_status, status, authentic_receipt, rejection_note, cancellation_note, handed_over_at'],
                            ['name' => 'companies', 'color' => 'text-pink-400', 'fields' => 'id, name, tax_id, address, phone, bank_name, bank_account, bank_account_holder, qris_image, logo_url, favicon_url'],
                            ['name' => 'warehouses', 'color' => 'text-orange-400', 'fields' => 'id, company_id→companies, name, location'],
                        ];
                    @endphp
                    @foreach($tables as $t)
                        <div class="border border-zinc-800 bg-zinc-900/30 p-2.5 hover:border-zinc-700 transition-all">
                            <span class="{{ $t['color'] }} font-bold">{{ $t['name'] }}</span>
                            <p class="text-zinc-600 text-[10px] mt-0.5 leading-relaxed">{{ $t['fields'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     TIM PENGEMBANG
══════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-6 py-16 border-t border-zinc-900">
    <div class="text-center mb-12">
        <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-2">Kelompok 18</span>
        <h2 class="text-2xl md:text-3xl font-black tracking-wide">Tim Pengembang</h2>
        <p class="text-zinc-500 text-xs mt-2">D4 Teknik Informatika — Universitas Logistik & Bisnis Internasional (ULBI)</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
        @php
            $team = [
                ['nim' => '714250042', 'name' => 'Muhammad Aris Ashidiqi',
                 'role' => 'Full-Stack Developer',
                 'focus' => 'Backend architecture, database design, security layer, admin panel, public interface',
                 'initials' => 'AA'],
                ['nim' => '714250039', 'name' => 'Fathoni Abdul Jabbar',
                 'role' => 'Frontend Developer & UI/UX',
                 'focus' => 'Interface design, Tailwind implementation, mobile responsiveness, user experience',
                 'initials' => 'FA'],
            ];
        @endphp
        @foreach($team as $member)
            <div class="bg-zinc-950 border border-zinc-900 hover:border-luxury-gold/40 transition-all p-6 group">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-zinc-900 border-2 border-luxury-gold/30 group-hover:border-luxury-gold/60 flex items-center justify-center text-luxury-gold font-black text-lg transition-all shrink-0">
                        {{ $member['initials'] }}
                    </div>
                    <div>
                        <h4 class="text-white font-black text-sm tracking-wide leading-tight">{{ $member['name'] }}</h4>
                        <p class="text-luxury-gold text-[10px] font-bold uppercase tracking-wider mt-0.5">{{ $member['role'] }}</p>
                        <p class="text-zinc-600 font-mono text-[10px] mt-0.5">NIM: {{ $member['nim'] }}</p>
                    </div>
                </div>
                <p class="text-zinc-500 text-[11px] leading-relaxed border-t border-zinc-900 pt-3">
                    <i class="fa-solid fa-code text-zinc-700 mr-1.5"></i>{{ $member['focus'] }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Dosen Pembimbing --}}
    <div class="max-w-2xl mx-auto mt-6">
        <div class="bg-zinc-950 border border-zinc-800 p-5 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-full bg-zinc-900 border border-zinc-700 flex items-center justify-center text-luxury-gold font-black text-sm shrink-0">
                <i class="fa-solid fa-chalkboard-user text-base"></i>
            </div>
            <div>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider mb-0.5">Dosen Koordinator Mata Kuliah</p>
                <p class="text-white font-bold text-sm">M. Yusril Helmi Setyawan, S.Kom., M.Kom.</p>
                <p class="text-zinc-600 text-[10px] mt-0.5">Proyek 1 — D4 Teknik Informatika, ULBI &middot; 2026</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     CTA BOTTOM
══════════════════════════════════════════════ --}}
<section class="border-t border-zinc-900 bg-black">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-white font-black text-lg tracking-wide">Siap Menjelajahi Katalog?</h3>
            <p class="text-zinc-500 text-xs mt-1">Temukan kendaraan premium impian Anda di ShowDrive.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('home') }}"
               class="bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold px-6 py-3 text-xs tracking-[0.2em] uppercase transition-all flex items-center gap-2">
                <i class="fa-solid fa-car"></i> Lihat Katalog
            </a>
            <a href="{{ route('booking.track') }}"
               class="border border-zinc-700 hover:border-zinc-500 text-zinc-400 hover:text-white font-bold px-6 py-3 text-xs tracking-[0.2em] uppercase transition-all flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> Cek Reservasi
            </a>
        </div>
    </div>
</section>

@endsection
