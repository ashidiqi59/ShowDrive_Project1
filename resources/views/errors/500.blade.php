@extends('layouts.app')

@section('title', '500 — Kesalahan Server')

@section('content')
<section class="min-h-[70vh] flex flex-col items-center justify-center text-center px-6 py-24 max-w-2xl mx-auto">

    {{-- Kode error --}}
    <div class="relative mb-8">
        <span class="text-[120px] md:text-[160px] font-black text-zinc-900 leading-none select-none font-mono">
            500
        </span>
    </div>

    {{-- Ikon peringatan --}}
    <div class="w-14 h-14 rounded-full border border-amber-900/50 bg-amber-950/30 flex items-center justify-center mb-6">
        <i class="fa-solid fa-triangle-exclamation text-amber-400 text-xl"></i>
    </div>

    {{-- Garis amber --}}
    <div class="w-16 h-0.5 bg-amber-600 mb-8"></div>

    {{-- Judul & deskripsi --}}
    <h1 class="text-2xl md:text-3xl font-black tracking-wider text-white uppercase mb-4">
        Kesalahan Server Internal
    </h1>
    <p class="text-zinc-500 text-sm leading-relaxed mb-3 max-w-md">
        Terjadi kesalahan pada server kami. Tim teknis telah diberitahu dan sedang menangani masalah ini.
    </p>
    <p class="text-zinc-600 text-xs font-mono mb-10">
        Coba kembali beberapa saat lagi, atau hubungi admin jika masalah berlanjut.
    </p>

    {{-- Tombol aksi --}}
    <div class="flex flex-col sm:flex-row gap-4 items-center">
        <a href="{{ route('home') }}"
           class="border border-luxury-gold text-luxury-gold hover:bg-luxury-gold hover:text-black font-bold px-8 py-3 text-xs tracking-[0.2em] uppercase transition-all duration-300">
            <i class="fa-solid fa-arrow-left mr-2"></i> KEMBALI KE KATALOG
        </a>
        <button onclick="window.location.reload()"
           class="border border-zinc-700 text-zinc-400 hover:border-zinc-500 hover:text-white font-bold px-8 py-3 text-xs tracking-[0.2em] uppercase transition-all duration-300 cursor-pointer">
            <i class="fa-solid fa-rotate-right mr-2"></i> COBA LAGI
        </button>
    </div>

</section>
@endsection
