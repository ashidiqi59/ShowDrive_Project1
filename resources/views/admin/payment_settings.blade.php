@extends('layouts.admin')

@section('title', 'Pengaturan Rekening & QRIS')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">PAYMENT SETTINGS</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">REKENING & QRIS SHOWROOM</h2>
            <p class="text-zinc-500 text-xs mt-1">Konfigurasi rekening bank dan kode QRIS untuk target pembayaran pelanggan.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-zinc-950 border border-zinc-900 p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-luxury-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

        <form action="{{ route('admin.payment_settings.update') }}" method="POST" enctype="multipart/form-data" class="relative z-10 space-y-6 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Bank --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $company->bank_name ?? '') }}" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">
                </div>

                {{-- Nomor Rekening --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nomor Rekening</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account', $company->bank_account ?? '') }}" placeholder="Contoh: 8820123456" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors font-mono">
                </div>

                {{-- Nama Pemilik Rekening --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $company->bank_account_holder ?? '') }}" placeholder="Contoh: PT ShowDrive Premium Corp" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">
                </div>

                {{-- Upload QRIS --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Gambar QR Code QRIS</label>
                    <input type="file" name="qris_image" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:border file:border-zinc-800 file:bg-zinc-900 file:text-zinc-300 file:text-xs focus:outline-none">
                    @if($company && $company->qris_image)
                        <div class="mt-3 flex items-start gap-3 bg-zinc-900/60 p-3 border border-zinc-900 rounded-sm w-fit">
                            <img src="{{ asset('storage/' . $company->qris_image) }}" alt="QRIS" class="w-24 h-24 object-cover border border-zinc-800 bg-white p-1 rounded-sm">
                            <div>
                                <span class="text-[10px] text-zinc-400 font-bold block mb-1">QRIS Saat Ini</span>
                                <span class="text-[9px] text-zinc-650 font-mono block break-all max-w-[200px]">{{ basename($company->qris_image) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-6 border-t border-zinc-900 flex justify-end">
                <button type="submit" class="bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 px-8 tracking-[0.2em] uppercase transition-all flex items-center gap-2 text-[10px]">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
