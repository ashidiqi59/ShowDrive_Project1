<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShowDrive - Sistem Manajemen Showroom Otomotif Premium')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js v3 -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts: Inter & Playfair Display -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;950&family=Playfair+Display:ital,wght@0,500;0,700;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        luxury: {
                            gold: '#D4AF37',
                            goldHover: '#AA8417',
                            goldLight: '#F3E5AB',
                            darkBg: '#09090b',
                            cardBg: '#121214',
                            border: '#222225'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #222225;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #D4AF37;
        }
        .text-glow {
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }

        /* ── Sticky Header Wrapper ── */
        #site-header-wrapper {
            position: sticky;
            top: 0;
            z-index: 50;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
        }
        #site-header-wrapper.scrolled {
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.7), 0 1px 0 rgba(212, 175, 55, 0.12);
        }
        #site-header-wrapper header {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        #site-header-wrapper.scrolled header {
            background-color: rgba(0, 0, 0, 0.98);
            border-color: rgba(212, 175, 55, 0.15);
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .print-card {
                background-color: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                box-shadow: none !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-luxury-darkBg text-zinc-100 min-h-screen flex flex-col justify-between selection:bg-luxury-gold selection:text-black">

    <!-- ═══════════════════════════════════════════════════════
         STICKY HEADER WRAPPER — Demo Bar + Main Navigation
         Dibungkus dalam satu unit sticky agar tidak ada
         elemen dekoratif yang tumpang tindih.
         ═══════════════════════════════════════════════════════ -->
    @if(!request()->routeIs('admin.*'))
    <div id="site-header-wrapper" class="no-print">

        <!-- PLATFORM DEMO SWITCHER BAR -->
        <div class="bg-zinc-950 border-b border-zinc-800/60 py-2 px-6 text-xs flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
                <span class="text-[10px] tracking-[0.2em] text-zinc-500 font-bold uppercase">ShowDrive Laravel (Aris &amp; Fathoni)</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}" class="text-zinc-400 hover:text-white px-3 py-1 bg-transparent hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-600 rounded-sm text-[10px] font-bold tracking-wider transition-all duration-200">
                    <i class="fa-solid fa-car text-luxury-gold mr-1.5"></i> KATALOG PUBLIK
                </a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-zinc-300 hover:text-black px-3 py-1 bg-luxury-gold/10 hover:bg-luxury-gold border border-luxury-gold/30 hover:border-luxury-gold rounded-sm text-[10px] font-bold tracking-wider transition-all duration-200">
                        <i class="fa-solid fa-user-gear mr-1.5"></i> PORTAL ADMIN
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-white px-3 py-1 bg-transparent hover:bg-red-900 border border-red-900/30 hover:border-red-600 rounded-sm text-[10px] font-bold tracking-wider transition-all duration-200">
                            <i class="fa-solid fa-right-from-bracket mr-1.5"></i> LOGOUT
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        <!-- MAIN HEADER & NAVIGATION -->
        <header class="bg-black/96 backdrop-blur-xl border-b border-zinc-900/80 py-4">
            <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
                <!-- Logo Brand -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-gradient-to-tr from-luxury-gold to-yellow-500 flex items-center justify-center font-black text-black text-sm tracking-tighter shrink-0">SD</div>
                    <span class="text-xl font-black tracking-[0.3em] text-white group-hover:text-luxury-gold transition-colors duration-300">
                        SHOW<span class="text-luxury-gold">DRIVE</span>
                    </span>
                </a>

                <!-- Main Navigation -->
                <nav class="hidden md:flex items-center space-x-10 text-xs font-bold tracking-[0.25em] text-zinc-400">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') || request()->routeIs('car.detail') ? 'text-luxury-gold' : '' }} hover:text-white transition-colors duration-200 relative group">
                        INVENTORY
                        <span class="absolute -bottom-1 left-0 h-px bg-luxury-gold transition-all duration-300 {{ request()->routeIs('home') || request()->routeIs('car.detail') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ route('booking.track') }}" class="{{ request()->routeIs('booking.track') ? 'text-luxury-gold' : '' }} hover:text-white transition-colors duration-200 relative group">
                        CEK STATUS &amp; KWITANSI
                        <span class="absolute -bottom-1 left-0 h-px bg-luxury-gold transition-all duration-300 {{ request()->routeIs('booking.track') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-luxury-gold text-black border-luxury-gold' : 'border-luxury-gold/50 text-luxury-gold hover:bg-luxury-gold hover:text-black' }} border transition-all duration-200 px-5 py-2">
                            CONTROL PANEL
                        </a>
                    @endauth
                </nav>

                <!-- Toggle Menu Mobile -->
                <button onclick="toggleMobileMenu()" class="md:hidden text-zinc-400 hover:text-white focus:outline-none transition-colors" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-zinc-950 border-t border-zinc-900 mt-4 px-6 py-4 space-y-3 text-xs font-semibold tracking-[0.15em]">
                <a href="{{ route('home') }}" class="block {{ request()->routeIs('home') ? 'text-luxury-gold' : 'text-zinc-400 hover:text-white' }} py-2 transition-colors">INVENTORY</a>
                <a href="{{ route('booking.track') }}" class="block {{ request()->routeIs('booking.track') ? 'text-luxury-gold' : 'text-zinc-400 hover:text-white' }} py-2 transition-colors">CEK STATUS &amp; KWITANSI</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block text-luxury-gold border border-luxury-gold/30 text-center py-2">CONTROL PANEL</a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-center text-red-400 border border-red-900/30 py-2 hover:bg-red-950 transition-colors">LOGOUT</button>
                    </form>
                @endauth
            </div>
        </header>

    </div><!-- end #site-header-wrapper -->
    @endif

    <!-- NOTIFICATIONS -->
    <div class="max-w-7xl mx-auto px-6 mt-6 no-print">
        @if(session('success'))
            <div class="bg-emerald-950/60 border border-emerald-900/50 text-emerald-400 p-4 mb-4 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-base"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-4 mb-4 text-xs font-semibold flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-4 mb-4 text-xs font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- CONTENT WRAPPER -->
    <main class="flex-grow {{ request()->routeIs('admin.*') ? '' : 'pb-24 md:pb-0' }}">
        @yield('content')
    </main>

    <!-- FOOTER (No-Print) -->
    @if(!request()->routeIs('admin.*'))
    <footer class="no-print bg-black border-t border-zinc-900 py-12 px-6 mt-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10 text-zinc-500 text-xs tracking-wider">
            <div class="space-y-4">
                <h4 class="text-white font-extrabold tracking-[0.25em] text-sm">SHOWDRIVE</h4>
                <p class="font-light leading-relaxed text-zinc-500">Platform sistem informasi terintegrasi manajemen aset otomotif premium yang efisien, aman, dan mutakhir.</p>
            </div>
            <div class="space-y-3">
                <h4 class="text-white font-extrabold tracking-[0.15em] text-xs">TI ULBI KELOMPOK 18</h4>
                <p class="font-light">
                    Muhammad Aris Ashidiqi (714250042)<br>
                    Fathoni Abdul Jabbar (714250039)
                </p>
                <p class="text-zinc-600 text-[10px]">D4 Teknik Informatika - Universitas Logistik & Bisnis Internasional</p>
            </div>
            <div class="space-y-3 md:text-right">
                <h4 class="text-white font-extrabold tracking-[0.15em] text-xs">DOSEN KOORDINATOR</h4>
                <p class="font-light text-zinc-400">M. Yusril Helmi Setyawan, S.Kom., M.Kom.</p>
                <p class="text-zinc-600 text-[10px]">&copy; 2026 ShowDrive System. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
    @endif

    <!-- BOTTOM NAVIGATION (Mobile-only: md:hidden) -->
    @if(!request()->routeIs('admin.*'))
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-black/95 border-t border-zinc-900 py-3.5 z-50 flex justify-around items-center text-center backdrop-blur-md no-print">
        <a href="#" onclick="navigateTo('home')" class="{{ request()->routeIs('home') || request()->routeIs('car.detail') ? 'text-luxury-gold' : 'text-zinc-400' }} hover:text-luxury-gold flex-1 flex flex-col items-center transition-all">
            <i class="fa-solid fa-car text-lg"></i>
            <span class="text-[10px] font-bold tracking-widest mt-1">KATALOG</span>
        </a>
        <div class="w-px h-6 bg-zinc-800"></div> <!-- Elegant separator -->
        <a href="#" onclick="navigateTo('booking-status')" class="{{ request()->routeIs('booking.track') ? 'text-luxury-gold' : 'text-zinc-400' }} hover:text-luxury-gold flex-1 flex flex-col items-center transition-all">
            <i class="fa-solid fa-receipt text-lg"></i>
            <span class="text-[10px] font-bold tracking-widest mt-1">CEK INVOICE</span>
        </a>
    </div>
    @endif

    <!-- Script for mobile menu + sticky header scroll effect + secret shortcut -->
    <script>
        // Navigation helper for mobile bottom navbar
        function navigateTo(viewId) {
            if (viewId === 'home') {
                window.location.href = "{{ route('home') }}";
            } else if (viewId === 'booking-status') {
                window.location.href = "{{ route('booking.track') }}";
            } else if (viewId === 'admin-login') {
                window.location.href = "{{ Auth::check() ? route('admin.dashboard') : route('login') }}";
            }
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Sticky header: tambah class 'scrolled' saat user scroll ke bawah
        (function () {
            const wrapper = document.getElementById('site-header-wrapper');
            if (!wrapper) return;

            function onScroll() {
                if (window.scrollY > 10) {
                    wrapper.classList.add('scrolled');
                } else {
                    wrapper.classList.remove('scrolled');
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll(); // run on load
        })();

        // ─────────────────────────────────────────────────────────────
        // SHORTCUT NAVIGASI KASIR — Ctrl + Shift + A
        // Tiga lapis perlindungan:
        //   1. Hanya aktif di halaman /track dan /invoice/*
        //   2. Auto-nonaktif setelah 5 detik sejak page load
        //   3. URL yang di-hardcode hanya relay '/pintu-masuk',
        //      bukan URL login sesungguhnya
        // ─────────────────────────────────────────────────────────────
        (function () {
            // Lapisan 1: cek halaman — hanya /track dan /invoice/*
            const path = window.location.pathname;
            const isAllowedPage = path === '/track'
                || path.startsWith('/track/')
                || path.startsWith('/invoice/');

            if (!isAllowedPage) return; // berhenti, tidak pasang listener

            // Lapisan 2: pasang listener tapi lepas otomatis setelah 5 detik
            function onKeyDown(e) {
                if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'a') {
                    e.preventDefault();
                    window.location.href = "{{ route('pintu.masuk') }}"; // Lapisan 3: Gated Relay URL
                }
            }

            window.addEventListener('keydown', onKeyDown);

            // Auto-remove listener setelah 5000ms (5 detik)
            setTimeout(function () {
                window.removeEventListener('keydown', onKeyDown);
            }, 5000);
        })();
    </script>
    @yield('scripts')
</body>
</html>
