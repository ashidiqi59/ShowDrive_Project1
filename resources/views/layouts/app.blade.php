<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShowDrive - Sistem Manajemen Showroom Otomotif Premium')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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

    <!-- PLATFORM DEMO SWITCHER BAR (No-Print) -->
    <div class="no-print bg-zinc-900 border-b border-luxury-gold/20 py-2.5 px-6 text-xs flex flex-col sm:flex-row justify-between items-center z-[100] sticky top-0 gap-3">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-luxury-gold animate-ping"></span>
            <span class="text-[10px] tracking-[0.2em] text-zinc-400 font-extrabold uppercase">ShowDrive Laravel (Aris & Fathoni)</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('home') }}" class="text-zinc-300 hover:text-white px-3 py-1.5 bg-black/60 hover:bg-black border border-zinc-800 rounded text-[10px] font-bold tracking-wider transition-all">
                <i class="fa-solid fa-car text-luxury-gold mr-1.5"></i> KATALOG PUBLIK
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="text-zinc-300 hover:text-white px-3 py-1.5 bg-luxury-gold/10 hover:bg-luxury-gold hover:text-black border border-luxury-gold/30 rounded text-[10px] font-bold tracking-wider transition-all">
                    <i class="fa-solid fa-user-gear text-luxury-gold mr-1.5"></i> PORTAL ADMIN
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-white px-3 py-1.5 bg-red-950/20 hover:bg-red-900 border border-red-900/30 rounded text-[10px] font-bold tracking-wider transition-all">
                        <i class="fa-solid fa-right-from-bracket mr-1.5"></i> LOGOUT
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white px-3 py-1.5 bg-luxury-gold/10 hover:bg-luxury-gold hover:text-black border border-luxury-gold/30 rounded text-[10px] font-bold tracking-wider transition-all">
                    <i class="fa-solid fa-user-gear text-luxury-gold mr-1.5"></i> PORTAL ADMIN & KEUANGAN
                </a>
            @endauth
        </div>
    </div>

    <!-- MAIN HEADER & NAVIGATION (No-Print) -->
    <header class="no-print bg-black/95 backdrop-blur-md border-b border-zinc-900 py-4.5 z-40 relative">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <!-- Logo Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 bg-gradient-to-tr from-luxury-gold to-yellow-500 rounded-none flex items-center justify-center font-black text-black text-sm tracking-tighter">SD</div>
                <span class="text-xl font-black tracking-[0.3em] text-white group-hover:text-luxury-gold transition-colors duration-300">
                    SHOW<span class="text-luxury-gold">DRIVE</span>
                </span>
            </a>

            <!-- Main Navigation -->
            <nav class="hidden md:flex items-center space-x-10 text-xs font-bold tracking-[0.25em] text-zinc-400">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') || request()->routeIs('car.detail') ? 'text-luxury-gold' : '' }} hover:text-white transition-all">INVENTORY</a>
                <a href="{{ route('booking.track') }}" class="{{ request()->routeIs('booking.track') ? 'text-luxury-gold' : '' }} hover:text-white transition-all">CEK STATUS & KWITANSI</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'border-luxury-gold bg-luxury-gold text-black' : 'border-luxury-gold/50 text-luxury-gold' }} border hover:bg-luxury-gold hover:text-black transition-all px-5 py-2">CONTROL PANEL</a>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'border-luxury-gold bg-luxury-gold text-black' : 'border-luxury-gold/50 text-luxury-gold' }} border hover:bg-luxury-gold hover:text-black transition-all px-5 py-2">ADMIN PANEL</a>
                @endauth
            </nav>

            <!-- Toggle Menu Mobile -->
            <button onclick="toggleMobileMenu()" class="md:hidden text-zinc-400 hover:text-white focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-zinc-950 border-t border-zinc-900 mt-4 px-6 py-4 space-y-4 text-xs font-semibold tracking-[0.15em]">
            <a href="{{ route('home') }}" class="block {{ request()->routeIs('home') ? 'text-luxury-gold' : 'text-zinc-400' }} py-2">INVENTORY</a>
            <a href="{{ route('booking.track') }}" class="block {{ request()->routeIs('booking.track') ? 'text-luxury-gold' : 'text-zinc-400' }} py-2">CEK STATUS & KWITANSI</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="block text-luxury-gold border border-luxury-gold/30 text-center py-2">CONTROL PANEL</a>
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="w-full text-center text-red-400 border border-red-900/30 py-2">LOGOUT</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-luxury-gold border border-luxury-gold/30 text-center py-2">ADMIN PANEL</a>
            @endauth
        </div>
    </header>

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
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- FOOTER (No-Print) -->
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

    <!-- Script for mobile menu -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
    @yield('scripts')
</body>
</html>
