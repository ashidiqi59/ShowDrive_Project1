@extends('layouts.admin')

@section('title', 'Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">COMPANY SETTINGS</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">PROFIL SHOWROOM</h2>
            <p class="text-zinc-500 text-xs mt-1">Konfigurasi data utama perusahaan untuk keperluan dokumen dan invoice.</p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-900/50 text-emerald-400 p-4 mb-8 text-xs font-bold tracking-wider uppercase flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-4 mb-8 text-xs font-bold tracking-wider uppercase flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-4 mb-8 text-xs font-bold tracking-wider uppercase flex flex-col gap-2">
            <div class="flex items-center gap-3"><i class="fa-solid fa-triangle-exclamation"></i> Terjadi Kesalahan:</div>
            <ul class="list-disc list-inside text-[10px] pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-zinc-950 border border-zinc-900 p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-luxury-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
        
        <form action="{{ route('admin.company.update') }}" method="POST" class="relative z-10 space-y-6 text-xs">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Perusahaan --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nama Showroom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" placeholder="Contoh: ShowDrive Premium Autos" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors duration-300" required>
                </div>

                {{-- NPWP / Tax ID --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nomor Pokok Wajib Pajak (NPWP) / Tax ID</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id ?? '') }}" placeholder="Opsional" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors font-mono tracking-widest">
                </div>

                {{-- Nomor Telepon --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nomor Kontak Resmi</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" placeholder="+62 812 3456 7890" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">
                </div>

                {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Alamat Lengkap</label>
                    <textarea name="address" rows="3" placeholder="Alamat showroom utama" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">{{ old('address', $company->address ?? '') }}</textarea>
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
