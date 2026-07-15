@extends('layouts.app')

@section('title', '403 — Akses Ditolak')

@section('content')
<section class="min-h-[70vh] flex flex-col items-center justify-center text-center px-6 py-24 max-w-2xl mx-auto">

    {{-- Kode error --}}
    <div class="relative mb-8">
        <span class="text-[120px] md:text-[160px] font-black text-zinc-900 leading-none select-none font-mono">
            403
        </span>
    </div>

    {{-- Ikon kunci --}}
    <div class="w-14 h-14 rounded-full border border-red-900/50 bg-red-950/30 flex items-center justify-center mb-6">
        <i class="fa-solid fa-lock text-red-400 text-xl"></i>
    </div>

    {{-- Garis merah --}}
    <div class="w-16 h-0.5 bg-red-600 mb-8"></div>

    {{-- Judul & deskripsi --}}
    <h1 class="text-2xl md:text-3xl font-black tracking-wider text-white uppercase mb-4">
        Akses Ditolak
    </h1>
    <p class="text-zinc-500 text-sm leading-relaxed mb-3 max-w-md">
        Anda tidak memiliki izin untuk mengakses halaman atau sumber daya ini.
    </p>

    @if($exception->getMessage())
        <div class="bg-red-950/30 border border-red-900/40 px-5 py-3 mb-8 max-w-md">
            <p class="text-red-400 text-xs font-mono">{{ $exception->getMessage() }}</p>
        </div>
    @else
        <p class="text-zinc-600 text-xs font-mono mb-10">
            Jika Anda merasa ini adalah kesalahan, silakan hubungi admin showroom.
        </p>
    @endif

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
