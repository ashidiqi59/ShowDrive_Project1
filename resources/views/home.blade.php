@extends('layouts.app')

@section('title', 'ShowDrive - Sistem Manajemen Showroom Otomotif Premium')

@section('content')
<!-- Hero Banner Premium -->
<section class="relative h-[65vh] bg-cover bg-center flex items-center" style="background-image: linear-gradient(rgba(9,9,11,0.1), rgba(9,9,11,1)), url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1920&q=80');">
    <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
        <div class="inline-flex items-center gap-2 bg-black/50 border border-zinc-800 px-3.5 py-1.5 mb-5 rounded-none">
            <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
            <span class="text-[9px] font-bold tracking-[0.3em] text-zinc-300 uppercase">Proyek 1 TI-ULBI - Kelompok 18</span>
        </div>
        <h6 class="text-luxury-gold font-bold tracking-[0.4em] mb-4 text-xs md:text-sm uppercase">THE ART OF DRIVING</h6>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black mb-6 leading-none tracking-tight font-sans">FIND YOUR<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-zinc-400 to-luxury-gold">MASTERPIECE</span></h1>
        <p class="text-zinc-400 max-w-xl text-xs md:text-sm leading-relaxed mb-8 font-light">Platform kurasi supercar mewah dengan penegasan transparansi nomor rangka (VIN) dan pelaporan finansial yang akurat serta kredibel.</p>
        <a href="#catalog-section" class="inline-block border border-luxury-gold text-luxury-gold font-bold hover:bg-luxury-gold hover:text-black px-8 py-3.5 tracking-[0.2em] text-[10px] transition-all duration-300">JELAJAHI UNIT</a>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-luxury-darkBg via-transparent to-black/30"></div>
</section>

<!-- Grid Katalog -->
<section id="catalog-section" class="max-w-7xl mx-auto px-6 py-20">
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-12 border-b border-zinc-900 pb-6 gap-4">
        <div>
            <span class="text-luxury-gold font-bold tracking-[0.2em] text-xs block mb-1">PROYEK 1 FONDASI</span>
            <h2 class="text-2xl md:text-3xl font-light tracking-[0.3em]">UNIT KENDARAAN</h2>
        </div>
        <!-- Filter Reaktif -->
        <form action="{{ route('home') }}" method="GET" class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-3 flex items-center text-zinc-500">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari Model, Brand, atau VIN..." class="w-full bg-zinc-950 border border-zinc-800 text-xs p-3.5 pl-10 focus:border-luxury-gold focus:outline-none tracking-wider text-zinc-300">
            @if($search)
                <a href="{{ route('home') }}" class="absolute inset-y-0 right-3 flex items-center text-zinc-500 hover:text-white">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($cars as $car)
            @php
                $badgeColor = 'bg-emerald-600';
                if ($car->status === 'Booked') {
                    $badgeColor = 'bg-amber-600';
                } elseif ($car->status === 'Sold') {
                    $badgeColor = 'bg-red-700';
                }
            @endphp
            <div class="bg-zinc-950 border border-zinc-900 hover:border-zinc-700 transition-all duration-500 group overflow-hidden flex flex-col justify-between">
                <div class="h-64 overflow-hidden relative bg-zinc-900">
                    <img src="{{ $car->image }}" alt="{{ $car->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                    <span class="absolute top-4 left-4 {{ $badgeColor }} text-white font-bold text-[9px] px-3 py-1.5 uppercase tracking-wider rounded-none">{{ $car->status }}</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-1.5 text-[10px] font-bold tracking-wider text-zinc-500">
                        <span>{{ $car->year }}</span>
                        <span class="w-1 h-1 rounded-full bg-zinc-700"></span>
                        <span>{{ strtoupper($car->brand) }}</span>
                    </div>
                    <h3 class="text-lg font-black mb-1 group-hover:text-luxury-gold transition-colors duration-300 uppercase tracking-wide">{{ $car->brand }} {{ $car->model }}</h3>
                    <p class="text-[11px] text-zinc-500 mb-6 font-mono tracking-widest">VIN: {{ substr($car->vin, 0, 12) }}...</p>
                    <div class="flex justify-between items-center pt-4 border-t border-zinc-900">
                        <span class="text-luxury-gold font-bold text-base font-mono">IDR {{ number_format($car->price, 0, ',', '.') }}</span>
                        <a href="{{ route('car.detail', $car->id) }}" class="border border-luxury-gold/40 hover:border-luxury-gold text-luxury-gold text-[10px] tracking-[0.15em] font-extrabold px-4 py-2 transition-all duration-300">
                            INQUIRE NOW
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-zinc-500 italic text-xs">
                Tidak ada unit kendaraan yang sesuai dengan filter pencarian.
            </div>
        @endforelse
    </div>
</section>
@endsection
