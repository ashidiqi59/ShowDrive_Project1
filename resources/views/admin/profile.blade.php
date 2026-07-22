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
            <p class="text-zinc-500 text-xs mt-1">Konfigurasi data utama perusahaan, logo, dan favicon website.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-zinc-950 border border-zinc-900 p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-luxury-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

        <form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data" class="relative z-10 space-y-6 text-xs">
            @csrf
            @method('PUT')

            {{-- ── SEKSI: INFO PERUSAHAAN ── --}}
            <div class="pb-4 border-b border-zinc-900">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 flex items-center gap-2">
                    <i class="fa-solid fa-building text-luxury-gold"></i> Informasi Perusahaan
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Perusahaan --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nama Showroom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}"
                        placeholder="Contoh: ShowDrive Premium Autos"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors duration-300" required>
                </div>

                {{-- NPWP / Tax ID --}}
                <div class="md:col-span-2">
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nomor Pokok Wajib Pajak (NPWP) / Tax ID</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id ?? '') }}"
                        placeholder="Opsional"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors font-mono tracking-widest">
                </div>

                {{-- Telepon --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Nomor Kontak Resmi</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                        placeholder="+62 812 3456 7890"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-2 text-[10px]">Alamat Lengkap</label>
                    <textarea name="address" rows="2"
                        placeholder="Alamat showroom utama"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors">{{ old('address', $company->address ?? '') }}</textarea>
                </div>
            </div>

            {{-- ── SEKSI: BRANDING ── --}}
            <div class="pt-4 pb-4 border-t border-b border-zinc-900">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 flex items-center gap-2">
                    <i class="fa-solid fa-palette text-luxury-gold"></i> Branding — Logo &amp; Favicon
                </h3>
                <p class="text-[10px] text-zinc-600 mt-1">Logo ditampilkan di navbar, sidebar admin, invoice, dan footer. Favicon muncul di tab browser.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- LOGO --}}
                <div class="bg-zinc-900/40 border border-zinc-800 p-5 rounded space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-image text-luxury-gold text-xs"></i>
                        <label class="block text-zinc-300 font-bold uppercase tracking-widest text-[10px]">Logo Showroom</label>
                    </div>
                    {{-- Preview logo aktif --}}
                    @if($company && $company->logo_url)
                        <div class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-900 rounded">
                            <img src="{{ asset('storage/' . $company->logo_url) }}"
                                 alt="Logo Showroom" class="h-12 max-w-[120px] object-contain bg-zinc-800 p-1 rounded border border-zinc-700">
                            <div>
                                <p class="text-[9px] text-emerald-400 font-bold"><i class="fa-solid fa-circle-check mr-1"></i>Logo aktif</p>
                                <p class="text-[9px] text-zinc-600 font-mono truncate max-w-[140px]">{{ basename($company->logo_url) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-zinc-950 border border-dashed border-zinc-800 rounded">
                            <div class="w-12 h-12 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center">
                                <span class="text-xs font-black text-luxury-gold">SD</span>
                            </div>
                            <p class="text-[10px] text-zinc-600 italic">Belum ada logo — menggunakan teks default "SD"</p>
                        </div>
                    @endif
                    <div>
                        <input type="file" name="logo_url" accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml"
                            class="w-full text-zinc-400 text-[10px] file:mr-3 file:py-1.5 file:px-3 file:border file:border-zinc-700 file:bg-zinc-900 file:text-zinc-300 file:text-[10px] file:font-bold focus:outline-none cursor-pointer">
                        <p class="text-[10px] text-zinc-600 mt-1.5">Format: JPG, PNG, WebP, SVG — Maks. 2MB</p>
                        <p class="text-[10px] text-zinc-600">Rekomendasi: <span class="text-zinc-400">horizontal, rasio 3:1, latar transparan (PNG/SVG)</span></p>
                    </div>
                </div>

                {{-- FAVICON --}}
                <div class="bg-zinc-900/40 border border-zinc-800 p-5 rounded space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-star text-luxury-gold text-xs"></i>
                        <label class="block text-zinc-300 font-bold uppercase tracking-widest text-[10px]">Favicon (Icon Tab Browser)</label>
                    </div>
                    {{-- Preview favicon aktif --}}
                    @if($company && $company->favicon_url)
                        <div class="flex items-center gap-3 p-3 bg-zinc-950 border border-zinc-900 rounded">
                            <img src="{{ asset('storage/' . $company->favicon_url) }}"
                                 alt="Favicon" class="w-10 h-10 object-contain bg-zinc-800 p-1 rounded border border-zinc-700">
                            <div>
                                <p class="text-[9px] text-emerald-400 font-bold"><i class="fa-solid fa-circle-check mr-1"></i>Favicon aktif</p>
                                <p class="text-[9px] text-zinc-600 font-mono truncate max-w-[140px]">{{ basename($company->favicon_url) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-zinc-950 border border-dashed border-zinc-800 rounded">
                            <div class="w-10 h-10 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center">
                                <i class="fa-solid fa-globe text-zinc-600 text-xs"></i>
                            </div>
                            <p class="text-[10px] text-zinc-600 italic">Belum ada favicon — browser pakai icon default</p>
                        </div>
                    @endif
                    <div>
                        <input type="file" name="favicon_url" accept="image/x-icon,image/png,image/svg+xml,image/jpeg,image/webp"
                            class="w-full text-zinc-400 text-[10px] file:mr-3 file:py-1.5 file:px-3 file:border file:border-zinc-700 file:bg-zinc-900 file:text-zinc-300 file:text-[10px] file:font-bold focus:outline-none cursor-pointer">
                        <p class="text-[10px] text-zinc-600 mt-1.5">Format: ICO, PNG, SVG — Maks. 512KB</p>
                        <p class="text-[10px] text-zinc-600">Rekomendasi: <span class="text-zinc-400">ukuran 32×32px atau 64×64px</span></p>
                        <p class="text-[10px] text-amber-600 mt-1"><i class="fa-solid fa-circle-info mr-1"></i>Browser meng-cache favicon agresif. Tekan Ctrl+Shift+R setelah mengganti.</p>
                    </div>
                </div>

            </div>

            <div class="pt-6 border-t border-zinc-900 flex justify-end">
                <button type="submit" class="bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 px-8 tracking-[0.2em] uppercase transition-all flex items-center gap-2 text-[10px]">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN SEMUA PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
