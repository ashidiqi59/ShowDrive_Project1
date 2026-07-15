@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<section class="min-h-[70vh] flex flex-col items-center justify-center text-center px-6 py-24 max-w-2xl mx-auto">

    {{-- Kode error --}}
    <div class="relative mb-8">
        <span class="text-[120px] md:text-[160px] font-black text-zinc-900 leading-none select-none font-mono">
            404
        </span>
        <span class="absolute inset-0 flex items-center justify-center text-[120px] md:text-[160px] font-black leading-none select-none font-mono text-transparent"
              style="--tw-gradient-from: #D4AF37; background: linear-gradient(135deg, #D4AF37 0%, #AA8417 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; opacity: 0.12;">
            404
        </span>
    </div>

    {{-- Garis emas --}}
    <div class="w-16 h-0.5 bg-luxury-gold mb-8"></div>

    {{-- Judul & deskripsi --}}
    <h1 class="text-2xl md:text-3xl font-black tracking-wider text-white uppercase mb-4">
        Halaman Tidak Ditemukan
    </h1>
    <p class="text-zinc-500 text-sm leading-relaxed mb-3 max-w-md">
        Halaman yang Anda cari tidak tersedia, telah dipindahkan, atau memang tidak pernah ada.
    </p>
    <p class="text-zinc-600 text-xs font-mono mb-10">
        URL: <span class="text-zinc-400">{{ request()->fullUrl() }}</span>
    </p>

    {{-- Tombol aksi --}}
    <div class="flex flex-col sm:flex-row gap-4 items-center">
        <a href="{{ route('home') }}"
           class="border border-luxury-gold text-luxury-gold hover:bg-luxury-gold hover:text-black font-bold px-8 py-3 text-xs tracking-[0.2em] uppercase transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> KEMBALI KE KATALOG
        </a>
        <a href="{{ route('booking.track') }}"
           class="border border-zinc-700 text-zinc-400 hover:border-zinc-500 hover:text-white font-bold px-8 py-3 text-xs tracking-[0.2em] uppercase transition-all duration-300">
            <i class="fa-solid fa-magnifying-glass mr-2"></i> LACAK RESERVASI
        </a>
    </div>

</section>
@endsection
