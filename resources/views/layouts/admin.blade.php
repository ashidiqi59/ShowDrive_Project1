<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - ShowDrive Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        luxury: {
                            gold: '#D4AF37',
                            goldHover: '#AA8417',
                            darkBg: '#09090b'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-luxury-darkBg text-zinc-100 min-h-screen font-sans selection:bg-luxury-gold selection:text-black">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen relative">
        <!-- BACKDROP OVERLAY (Mobile) -->
        <div
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 z-40 md:hidden"
            style="display: none;">
        </div>

        <!-- SIDEBAR LEFT (Persistent) -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="w-64 bg-zinc-950 border-r border-zinc-900 p-6 flex flex-col justify-between fixed h-full z-50 transition-transform duration-300 md:translate-x-0 md:sticky md:top-0">
            <div class="space-y-8">
                <!-- Brand Logo Header -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-luxury-gold text-black flex items-center justify-center font-black rounded">SD</div>
                    <span class="text-lg font-black tracking-[0.2em] text-white">SHOW<span class="text-luxury-gold">DRIVE</span></span>
                </div>

                <!-- Cashier Profile Section -->
                <div class="flex items-center gap-3 p-3 bg-zinc-900/50 border border-zinc-800 rounded">
                    <div class="w-10 h-10 rounded-full border border-luxury-gold/40 flex items-center justify-center text-luxury-gold bg-zinc-950 font-bold uppercase">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Cashier Admin' }}</h4>
                        <span class="text-[9px] text-zinc-500 uppercase tracking-widest">{{ Auth::user()->role ?? 'Kasir' }}</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1.5 overflow-y-auto">
                    <span class="text-[9px] font-bold tracking-widest text-zinc-600 block px-3 mb-2">MENU UTAMA</span>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-chart-line text-sm w-5"></i>
                        <span>DASHBOARD METRIK</span>
                    </a>

                    <a href="{{ route('admin.items') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.items') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-car-side text-sm w-5"></i>
                        <span>DAFTAR INVENTARIS</span>
                    </a>

                    <a href="{{ route('admin.invoices') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.invoices') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-check-double text-sm w-5"></i>
                        <span>VALIDASI TRANSAKSI</span>
                    </a>

                    <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.laporan') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-file-invoice-dollar text-sm w-5"></i>
                        <span>LAPORAN KEUANGAN</span>
                    </a>

                    <a href="{{ route('admin.warehouses') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.warehouses') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-warehouse text-sm w-5"></i>
                        <span>MANAJEMEN GUDANG</span>
                    </a>

                    <a href="{{ route('admin.cashiers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.cashiers') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-users text-sm w-5"></i>
                        <span>MANAJEMEN KASIR</span>
                    </a>

                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.profile') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-building text-sm w-5"></i>
                        <span>PROFIL SHOWROOM</span>
                    </a>

                    <a href="{{ route('admin.payment_settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-xs font-bold tracking-wider transition-all {{ request()->routeIs('admin.payment_settings') ? 'bg-zinc-900 text-luxury-gold border-l-2 border-luxury-gold' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/40' }}">
                        <i class="fa-solid fa-credit-card text-sm w-5"></i>
                        <span>REKENING & QRIS</span>
                    </a>
                </nav>
            </div>

            <!-- Logout Form Footer -->
            <form action="{{ route('logout') }}" method="POST" class="border-t border-zinc-900 pt-4 mt-auto">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-xs font-bold tracking-wider text-red-500 hover:bg-red-950/20 rounded transition-all">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span>LOGOUT</span>
                </button>
            </form>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-grow flex flex-col min-h-screen bg-zinc-950 min-w-0">
            <!-- Mobile Header Bar -->
            <div class="md:hidden flex items-center justify-between bg-zinc-950 border-b border-zinc-900 p-4 sticky top-0 z-40">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-luxury-gold flex items-center justify-center font-black text-black text-xs shrink-0 rounded">SD</div>
                    <span class="text-sm font-black tracking-wider text-white">SHOW<span class="text-luxury-gold">DRIVE</span></span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-zinc-400 hover:text-white p-2">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

            <main class="flex-grow p-6 md:p-10 w-full max-w-7xl mx-auto">
                <!-- Flash Message Container -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-900 text-emerald-400 rounded text-xs font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-950/40 border border-red-900 text-red-400 rounded text-xs font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 bg-red-950/40 border border-red-900 text-red-400 p-4 rounded text-xs font-semibold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         GLOBAL TOAST NOTIFICATION (Alpine.js)
         ═══════════════════════════════════════ --}}
    <div
        x-data="{ show: false, type: 'success', message: '' }"
        x-on:show-toast.window="show = true; type = $event.detail.type; message = $event.detail.message; setTimeout(() => show = false, 4000)"
        class="fixed top-6 right-6 z-[300] pointer-events-none">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            :class="type === 'success' ? 'bg-emerald-950/95 border-emerald-800 text-emerald-300' : 'bg-red-950/95 border-red-800 text-red-300'"
            class="border px-5 py-3.5 text-xs font-semibold flex items-center gap-3 shadow-2xl backdrop-blur-md max-w-sm pointer-events-auto"
            style="display:none;">
            <i :class="type === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-triangle-exclamation'" class="text-base shrink-0"></i>
            <span x-text="message"></span>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
