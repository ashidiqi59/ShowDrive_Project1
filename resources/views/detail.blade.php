@extends('layouts.app')

@section('title', $car->brand . ' ' . $car->model . ' ' . $car->year . ' — ShowDrive')

@section('og_description', $car->brand . ' ' . $car->model . ' (' . $car->year . ') — Harga: IDR ' . number_format($car->price, 0, ',', '.') . ' · Warna: ' . $car->color . ' · Status: ' . $car->status . '. Lihat detail unit di ShowDrive.')

@section('og_image', $car->image)

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 no-print">
    <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-3 text-zinc-500 hover:text-luxury-gold font-bold tracking-[0.2em] text-[10px] transition-all">
        <i class="fa-solid fa-arrow-left-long text-xs"></i> KEMBALI KE INVENTARIS
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        <!-- Gallery bodi -->
        <div class="space-y-4">
            <div class="h-[450px] overflow-hidden border border-zinc-900 bg-zinc-950 relative">
                <img id="dt-main-img" src="{{ $car->image }}" alt="{{ $car->model }}" class="w-full h-full object-cover">
                @php
                    $badgeColor = 'bg-emerald-600';
                    if ($car->status === 'Booked') {
                        $badgeColor = 'bg-amber-600';
                    } elseif ($car->status === 'Sold') {
                        $badgeColor = 'bg-red-700';
                    }
                @endphp
                <span id="dt-status-badge" class="absolute top-4 left-4 {{ $badgeColor }} text-white font-bold text-[9px] px-3.5 py-1.5 uppercase tracking-[0.15em]">{{ $car->status }}</span>
            </div>
            <div class="grid grid-cols-4 gap-4">
                @php
                    $galleryImages = $car->images;
                @endphp
                @if($galleryImages->isEmpty())
                    {{-- Fallback jika belum ada data di tabel item_images (data lama) --}}
                    <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                        <img id="dt-thumb-1" src="{{ $car->image }}" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb 1">
                    </button>
                    <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb 2">
                    </button>
                    <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                        <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb 3">
                    </button>
                    <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                        <img src="https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb 4">
                    </button>
                @else
                    @foreach($galleryImages as $index => $img)
                        <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                            <img src="{{ $img->url }}" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb {{ $index + 1 }}">
                        </button>
                    @endforeach
                    @for($i = count($galleryImages); $i < 4; $i++)
                        <div class="border border-zinc-900 bg-zinc-950/40 h-20 flex items-center justify-center text-zinc-800">
                            <i class="fa-regular fa-image text-lg"></i>
                        </div>
                    @endfor
                @endif
            </div>
        </div>

        <!-- Specs Sheet -->
        <div class="flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-zinc-500 font-bold tracking-[0.2em] text-xs">{{ $car->year }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-luxury-gold"></span>
                    <span class="text-luxury-gold font-bold tracking-[0.2em] text-xs">{{ strtoupper($car->brand) }}</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4 uppercase tracking-wide">{{ $car->brand }} {{ $car->model }}</h2>
                <h3 class="text-xl md:text-2xl font-light text-zinc-300 tracking-wider mb-8">IDR {{ number_format($car->price, 0, ',', '.') }}</h3>

                <div class="border-t border-zinc-900 pt-6 mt-6 space-y-4">
                    <h4 class="font-bold text-xs tracking-[0.25em] text-zinc-300 uppercase mb-4">SPESIFIKASI DATA UNIT</h4>

                    <div class="grid grid-cols-2 gap-y-6 border-b border-zinc-900 pb-6 text-xs tracking-wider">
                        <div>
                            <span class="text-zinc-500 block mb-1">NOMOR RANGKA (VIN)</span>
                            <span class="font-bold text-white font-mono text-sm tracking-widest text-glow">{{ $car->vin }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">KAPASITAS MESIN</span>
                            <span class="font-semibold text-zinc-200">{{ $car->engine }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">TRANSMISI</span>
                            <span class="font-semibold text-zinc-200">{{ $car->transmission }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">WARNA BODY</span>
                            <span class="font-semibold text-zinc-200">{{ $car->color }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="mt-8 space-y-4">
                @if($car->status === 'Available')
                    <button
                        onclick="openBookingModal()"
                        class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-4 text-xs tracking-[0.2em] transition-all duration-300 flex items-center justify-center gap-2 group">
                        <i class="fa-solid fa-calendar-check transition-transform group-hover:scale-110"></i>
                        JADWALKAN PERTEMUAN (INQUIRE)
                    </button>
                    <a href="https://wa.me/?text={{ urlencode('Halo! Cek unit ini di ShowDrive: '.$car->brand.' '.$car->model.' ('.$car->year.') — '.route('car.detail', $car->id)) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-full border border-green-600 text-green-400 hover:bg-green-600 hover:text-white font-extrabold py-4 text-xs tracking-[0.2em] transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i> Bagikan via WhatsApp
                    </a>
                @elseif($car->status === 'Booked')
                    <button disabled class="w-full bg-zinc-800 text-zinc-500 font-extrabold py-4 text-xs tracking-[0.2em] cursor-not-allowed">
                        <i class="fa-solid fa-lock mr-2"></i>TERPESAN (BOOKED)
                    </button>
                @else
                    <button disabled class="w-full bg-zinc-800 text-zinc-500 font-extrabold py-4 text-xs tracking-[0.2em] cursor-not-allowed">
                        <i class="fa-solid fa-lock mr-2"></i>TERJUAL (SOLD)
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL BOOKING — Alpine.js Reactive Component
     Catatan: semua Alpine event menggunakan x-on: bukan @
     agar tidak konflik dengan Blade directive parser
     ============================================================ --}}

{{-- Deteksi jika ada server-side error untuk auto-buka modal --}}
@php
    $hasErrors = $errors->any() || old('customer_name') || old('phone');
    $hasErrorsJs = $hasErrors ? 'true' : 'false';
    $oldName = old('customer_name', '');
    $oldPhone = old('phone', '');
    $oldDate = old('date', '');
@endphp

<div
    x-data="{
        open: {{ $hasErrorsJs }},
        loading: false,
        name: '{{ addslashes($oldName) }}',
        phone: '{{ addslashes($oldPhone) }}',
        date: '{{ addslashes($oldDate) }}',
        time: '{{ old('time', '10:00') }}',
        payment_type: '{{ old('payment_type', 'Down Payment') }}',
        nik: '{{ old('nik', '') }}',
        nameError: '',
        phoneError: '',
        dateError: '',

        // Pembatasan tanggal secara dinamis untuk H+1 s/d H+7
        tomorrowStr: new Date(Date.now() + 86400000).toISOString().split('T')[0],
        maxDateStr: new Date(Date.now() + 86400000 * 7).toISOString().split('T')[0],

        get nameValid() { return this.name.length >= 3 && /^[\u00C0-\u024Fa-zA-Z\s\.\'\-]+$/.test(this.name); },
        get phoneValid() { return /^(08|628|\+628)[0-9]{7,11}$/.test(this.phone); },
        get dateValid() {
            if (!this.date) return false;
            return this.date >= this.tomorrowStr && this.date <= this.maxDateStr;
        },
        get formValid() { return this.nameValid && this.phoneValid && this.dateValid && this.time >= '08:00' && this.time <= '17:00'; },

        validateName() {
            if (!this.name) { this.nameError = 'Nama lengkap wajib diisi.'; return; }
            if (this.name.length < 3) { this.nameError = 'Nama minimal 3 karakter.'; return; }
            if (!/^[\u00C0-\u024Fa-zA-Z\s\.\'\-]+$/.test(this.name)) { this.nameError = 'Nama hanya boleh mengandung huruf dan spasi.'; return; }
            this.nameError = '';
        },
        validatePhone() {
            if (!this.phone) { this.phoneError = 'Nomor HP wajib diisi.'; return; }
            if (!/^(08|628|\+628)[0-9]{7,11}$/.test(this.phone)) { this.phoneError = 'Format: 08xx, 628xx, atau +628xx.'; return; }
            this.phoneError = '';
        },
        validateDate() {
            if (!this.date) { this.dateError = 'Tanggal inspeksi wajib diisi.'; return; }
            if (this.date < this.tomorrowStr) { this.dateError = 'Paling cepat adalah besok.'; return; }
            if (this.date > this.maxDateStr) { this.dateError = 'Paling lambat adalah 7 hari ke depan.'; return; }
            this.dateError = '';
        },
    }"
    x-on:open-booking-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    class="no-print">

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="open = false"
        class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[99]"
        style="display: none;">
    </div>

    {{-- Modal Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="fixed inset-0 flex items-center justify-center p-4 z-[100] pointer-events-none"
        style="display: none;">

        <div class="bg-zinc-950 border border-zinc-800 w-full max-w-md p-6 relative max-h-[90vh] overflow-y-auto rounded-lg shadow-2xl pointer-events-auto" x-on:click.stop>

            {{-- Close button --}}
            <button x-on:click="open = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all rounded">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-5 bg-luxury-gold"></div>
                    <h4 class="text-xl font-bold tracking-wider uppercase">RESERVASI JADWAL TEMU</h4>
                </div>
                <p class="text-zinc-500 text-[10px] uppercase tracking-wider pl-3">Unit: {{ $car->brand }} {{ $car->model }} &middot; {{ $car->year }}</p>
            </div>

            {{-- Server-side errors --}}
            @if($errors->any())
                <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-3 mb-4 text-xs rounded">
                    <div class="flex items-center gap-2 mb-1 font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i> Periksa kembali isian Anda:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Booking Form --}}
            <form action="{{ route('booking.store') }}" method="POST" class="space-y-4 text-xs" x-on:submit="loading = true" data-no-lock="true">
                @csrf
                <input type="hidden" name="car_id" value="{{ $car->id }}">

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        Nama Lengkap Pemohon <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        name="customer_name"
                        x-model="name"
                        x-on:blur="validateName()"
                        x-on:input="if(nameError) validateName()"
                        placeholder="Contoh: Aris Ashidiqi"
                        :class="nameError ? 'border-red-500 bg-red-950/20' : (nameValid && name ? 'border-emerald-600 bg-emerald-950/10' : 'border-zinc-800')"
                        class="w-full bg-zinc-900 border text-white p-3 focus:outline-none transition-colors focus:border-luxury-gold"
                        required>
                    <p x-show="nameError" class="text-red-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span x-text="nameError"></span>
                    </p>
                    <p x-show="nameValid && name && !nameError" class="text-emerald-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> <span>Nama valid</span>
                    </p>
                </div>

                {{-- Nomor HP --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        Nomor Kontak WhatsApp <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        x-model="phone"
                        x-on:blur="validatePhone()"
                        x-on:input="if(phoneError) validatePhone()"
                        placeholder="Contoh: 08123456789"
                        :class="phoneError ? 'border-red-500 bg-red-950/20' : (phoneValid && phone ? 'border-emerald-600 bg-emerald-950/10' : 'border-zinc-800')"
                        class="w-full bg-zinc-900 border text-white p-3 focus:outline-none transition-colors focus:border-luxury-gold"
                        required>
                    <p x-show="phoneError" class="text-red-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span x-text="phoneError"></span>
                    </p>
                    <p x-show="phoneValid && phone && !phoneError" class="text-emerald-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> <span>Nomor valid</span>
                    </p>
                </div>

                {{-- NIK (Opsional) --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        NIK / No. KTP
                        <span class="text-zinc-600 normal-case font-normal ml-1">(opsional, untuk pengurusan BPKB/STNK)</span>
                    </label>
                    <input
                        type="text"
                        name="nik"
                        x-model="nik"
                        placeholder="16 digit NIK sesuai KTP"
                        maxlength="16"
                        inputmode="numeric"
                        pattern="[0-9]{16}"
                        :class="nik && nik.length > 0 && nik.length !== 16 ? 'border-amber-500 bg-amber-950/10' : 'border-zinc-800'"
                        class="w-full bg-zinc-900 border text-white p-3 focus:outline-none transition-colors focus:border-luxury-gold font-mono tracking-widest">
                    <p x-show="nik && nik.length > 0 && nik.length !== 16"
                       class="text-amber-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>NIK harus tepat 16 digit angka.</span>
                    </p>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        Pilih Tanggal Rencana Inspeksi (Maksimal H+7) <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="date"
                        name="date"
                        x-model="date"
                        x-on:blur="validateDate()"
                        x-on:change="if(dateError) validateDate()"
                        :min="tomorrowStr"
                        :max="maxDateStr"
                        :class="dateError ? 'border-red-500 bg-red-950/20' : (dateValid && date ? 'border-emerald-600 bg-emerald-950/10' : 'border-zinc-800')"
                        class="w-full bg-zinc-900 border text-zinc-300 p-3 focus:outline-none transition-colors focus:border-luxury-gold calendar-gold"
                        required>
                    <p x-show="dateError" class="text-red-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span x-text="dateError"></span>
                    </p>
                </div>

                {{-- Jam --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        Pilih Jam Rencana Inspeksi <span class="text-red-400">*</span>
                        <span class="text-zinc-600 normal-case font-normal ml-1">(Jam operasional: 08:00–17:00)</span>
                    </label>
                    <input
                        type="time"
                        name="time"
                        x-model="time"
                        min="08:00"
                        max="17:00"
                        :class="time && (time < '08:00' || time > '17:00') ? 'border-red-500 bg-red-950/20' : (time ? 'border-emerald-600 bg-emerald-950/10' : 'border-zinc-800')"
                        class="w-full bg-zinc-900 border text-zinc-300 p-3 focus:outline-none transition-colors focus:border-luxury-gold calendar-gold"
                        required>
                    <p x-show="time && (time < '08:00' || time > '17:00')" class="text-red-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>Jam inspeksi harus antara 08:00 dan 17:00.</span>
                    </p>
                    <p x-show="time && time >= '08:00' && time <= '17:00'" class="text-emerald-400 text-[10px] mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> <span>Jam valid</span>
                    </p>
                </div>

                {{-- Tipe Pembayaran --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">
                        Pilihan Metode Pembayaran Komitmen <span class="text-red-400">*</span>
                    </label>
                    @php
                        $dpAmount   = (int) round($car->price * ($car->dp_percentage / 100));
                        $dpPct      = $car->dp_percentage;
                    @endphp
                    <select
                        name="payment_type"
                        x-model="payment_type"
                        class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-3 focus:outline-none focus:border-luxury-gold">
                        <option value="Down Payment">
                            Uang Muka / DP {{ $dpPct }}% &mdash; IDR {{ number_format($dpAmount, 0, ',', '.') }}
                        </option>
                        <option value="Paid">
                            Pelunasan Penuh &mdash; IDR {{ number_format($car->price, 0, ',', '.') }}
                        </option>
                    </select>
                </div>

                {{-- Info badge --}}
                <div class="bg-zinc-900/40 p-3 border border-zinc-800 text-[10px] text-zinc-500 leading-relaxed flex items-start gap-2">
                    <i class="fa-solid fa-lock text-luxury-gold mt-0.5 shrink-0"></i>
                    <span>Data disimpan aman dalam tabel transaksi <strong class="text-zinc-400">invoices</strong> dengan relasi Foreign Key ke tabel master kendaraan. Status unit akan otomatis berubah ke <strong class="text-amber-400">Booked</strong>.</span>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    :disabled="!formValid || loading"
                    :class="(!formValid || loading) ? 'bg-zinc-700 text-zinc-400 cursor-not-allowed' : 'bg-luxury-gold hover:bg-luxury-goldHover text-black cursor-pointer'"
                    class="w-full font-extrabold py-4 text-xs tracking-widest uppercase transition-all duration-300 mt-2 flex items-center justify-center gap-2">
                    <span x-show="!loading">
                        <i class="fa-solid fa-paper-plane mr-1"></i> KIRIM PERMINTAAN TEMU
                    </span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeDetailImage(src) {
        document.getElementById('dt-main-img').src = src;
    }

    function openBookingModal() {
        window.dispatchEvent(new CustomEvent('open-booking-modal'));
    }
</script>
@endsection
