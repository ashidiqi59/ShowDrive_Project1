@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    {{-- =====================================================
         HEADER PANEL
         ===================================================== --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-8 border-b border-zinc-900 gap-4 mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">DATABASE LIVE</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider">SHOWDRIVE CONTROL PANEL</h2>
            <p class="text-zinc-500 text-xs mt-1">Sistem Terpusat Manajemen Stok, Transaksi Keuangan & Validasi Transaksi</p>
        </div>
    </div>

    {{-- =====================================================
         4 CARD STATISTIK DASHBOARD
         ===================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

        {{-- Card 1: Total Unit --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6 hover:border-zinc-700 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-zinc-900/0 to-zinc-800/10 group-hover:from-zinc-800/20 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Total Unit</span>
                    <div class="w-8 h-8 bg-zinc-800 flex items-center justify-center rounded">
                        <i class="fa-solid fa-car-side text-luxury-gold text-sm"></i>
                    </div>
                </div>
                <span class="text-3xl font-black font-mono text-white">{{ $totalUnit }}</span>
                <p class="text-zinc-600 text-[10px] mt-1 uppercase tracking-wider">Unit di Inventaris</p>
            </div>
        </div>

        {{-- Card 2: Unit Sold --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6 hover:border-red-900/50 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-950/0 to-red-900/10 group-hover:from-red-950/20 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Unit Sold</span>
                    <div class="w-8 h-8 bg-red-950/50 flex items-center justify-center border border-red-900/30 rounded">
                        <i class="fa-solid fa-tags text-red-400 text-sm"></i>
                    </div>
                </div>
                <span class="text-3xl font-black font-mono text-red-400">{{ $unitSold }}</span>
                <p class="text-zinc-600 text-[10px] mt-1 uppercase tracking-wider">Unit Terjual Lunas</p>
            </div>
        </div>

        {{-- Card 3: Pending Verification --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6 hover:border-amber-900/50 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-950/0 to-amber-900/10 group-hover:from-amber-950/20 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Pending Verification</span>
                    <div class="w-8 h-8 bg-amber-950/50 flex items-center justify-center border border-amber-900/30 relative rounded">
                        <i class="fa-solid fa-hourglass-half text-amber-400 text-sm"></i>
                        @if($pendingVerification > 0)
                            <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-amber-500 text-black text-[8px] font-black rounded-full flex items-center justify-center">{{ $pendingVerification }}</span>
                        @endif
                    </div>
                </div>
                <span class="text-3xl font-black font-mono {{ $pendingVerification > 0 ? 'text-amber-400' : 'text-zinc-400' }}">{{ $pendingVerification }}</span>
                <p class="text-zinc-600 text-[10px] mt-1 uppercase tracking-wider">Menunggu Verifikasi</p>
            </div>
        </div>

        {{-- Card 4: Total Revenue --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6 hover:border-emerald-900/50 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/0 to-emerald-900/10 group-hover:from-emerald-950/20 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Total Revenue</span>
                    <div class="w-8 h-8 bg-emerald-950/50 flex items-center justify-center border border-emerald-900/30 rounded">
                        <i class="fa-solid fa-wallet text-emerald-400 text-sm"></i>
                    </div>
                </div>
                <span class="text-2xl font-black font-mono text-emerald-400">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                <p class="text-zinc-600 text-[10px] mt-1 uppercase tracking-wider">Total Penerimaan</p>
            </div>
        </div>
    </div>
@endsection
