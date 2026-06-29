@extends('layouts.app')

@section('title', 'Admin Dashboard - ShowDrive')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 no-print">

    {{-- =====================================================
         TOAST NOTIFICATION (Alpine.js global)
         ===================================================== --}}
    <div
        x-data="{
            show: false,
            type: 'success',
            message: '',
            showToast(type, msg) {
                this.type = type;
                this.message = msg;
                this.show = true;
                setTimeout(() => this.show = false, 4000);
            }
        }"
        x-on:show-toast.window="showToast($event.detail.type, $event.detail.message)"
        class="fixed top-20 right-6 z-[200] pointer-events-none">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            :class="type === 'success' ? 'bg-emerald-950/90 border-emerald-800 text-emerald-300' : 'bg-red-950/90 border-red-800 text-red-300'"
            class="border px-5 py-3.5 text-xs font-semibold flex items-center gap-3 shadow-2xl backdrop-blur-md max-w-sm pointer-events-auto"
            style="display: none;">
            <i :class="type === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-triangle-exclamation'" class="text-base shrink-0"></i>
            <span x-text="message"></span>
        </div>
    </div>

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
            <p class="text-zinc-500 text-xs mt-1">Sistem Terpusat Manajemen Stok, Transaksi Keuangan & Antrean Inspeksi</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('home') }}" class="bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white text-[10px] tracking-[0.15em] font-bold px-5 py-2.5 transition-all">
                KELUAR PANEL
            </a>
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
                    <div class="w-8 h-8 bg-zinc-800 flex items-center justify-center">
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
                    <div class="w-8 h-8 bg-red-950/50 flex items-center justify-center border border-red-900/30">
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
                    <div class="w-8 h-8 bg-amber-950/50 flex items-center justify-center border border-amber-900/30 relative">
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
                    <div class="w-8 h-8 bg-emerald-950/50 flex items-center justify-center border border-emerald-900/30">
                        <i class="fa-solid fa-wallet text-emerald-400 text-sm"></i>
                    </div>
                </div>
                <span class="text-2xl font-black font-mono text-emerald-400">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                <p class="text-zinc-600 text-[10px] mt-1 uppercase tracking-wider">Total Penerimaan</p>
            </div>
        </div>
    </div>

    {{-- =====================================================
         PANEL UTAMA: INVENTARIS + TRANSAKSI
         ===================================================== --}}
    <div class="space-y-10">

        {{-- ─────────────────────────────────────────────────
             TABEL INVENTARIS + TOMBOL TAMBAH
             ───────────────────────────────────────────────── --}}
        <div
            x-data="{
                modalOpen: false,
                editMode: false,
                editId: null,
                form: {
                    warehouse_id: '{{ optional($warehouses->first())->id ?? '' }}',
                    brand: 'Porsche',
                    model: '',
                    vin: '',
                    year: 2024,
                    color: '',
                    engine: '',
                    transmission: '',
                    price: '',
                    status: 'Available',
                },

                imageFiles: [],
                imagePreviews: [],
                imageError: '',

                handleImageUpload(event) {
                    const files = Array.from(event.target.files);
                    const selected = files.slice(0, 5);
                    if (files.length > 5) {
                        this.imageError = 'Maksimal 5 foto. Hanya 5 foto pertama yang diproses.';
                    } else {
                        this.imageError = '';
                    }
                    this.imageFiles = selected;
                    this.imagePreviews = new Array(selected.length).fill(null);
                    selected.forEach((file, idx) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreviews[idx] = e.target.result;
                            this.imagePreviews = [...this.imagePreviews];
                        };
                        reader.readAsDataURL(file);
                    });
                },

                removeImage(index) {
                    this.imagePreviews.splice(index, 1);
                    this.imageFiles.splice(index, 1);
                    if (this.imageFiles.length === 0) {
                        const inp = document.getElementById('imageUploadInput');
                        if (inp) inp.value = '';
                    }
                },

                openAdd() {
                    this.editMode = false;
                    this.editId = null;
                    this.imageFiles = [];
                    this.imagePreviews = [];
                    this.imageError = '';
                    const inp = document.getElementById('imageUploadInput');
                    if (inp) inp.value = '';
                    this.form = {
                        warehouse_id: '{{ optional($warehouses->first())->id ?? '' }}',
                        brand: 'Porsche', model: '', vin: '',
                        year: 2024, color: '', engine: '', transmission: '',
                        price: '',
                        status: 'Available',
                    };
                    this.modalOpen = true;
                },

                openEdit(car) {
                    this.editMode = true;
                    this.editId = car.id;
                    this.imageFiles = [];
                    this.imageError = '';
                    const inp = document.getElementById('imageUploadInput');
                    if (inp) inp.value = '';

                    // Ambil seluruh foto pendukung dari relasi database jika tersedia
                    if (car.images && car.images.length > 0) {
                        this.imagePreviews = car.images.map(img => {
                            if (img.image_path.startsWith('http://') || img.image_path.startsWith('https://')) {
                                return img.image_path;
                            }
                            return '/storage/' + img.image_path;
                        });
                    } else if (car.image_url) {
                        let url = car.image_url;
                        if (!url.startsWith('http://') && !url.startsWith('https://')) {
                            url = '/storage/' + url;
                        }
                        this.imagePreviews = [url];
                    } else {
                        this.imagePreviews = [];
                    }

                    this.form = {
                        warehouse_id: car.warehouse_id,
                        brand: car.brand,
                        model: car.model,
                        vin: car.vin,
                        year: car.year,
                        color: car.color,
                        engine: car.engine,
                        transmission: car.transmission,
                        price: car.price,
                        status: car.status === 'Invoiced' ? 'Booked' : car.status,
                    };
                    this.modalOpen = true;
                }
            }"
            x-on:keydown.escape.window="modalOpen = false">

            <div class="bg-zinc-950 border border-zinc-900 p-6">
                {{-- Header Inventaris --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <div>
                        <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">DAFTAR INVENTARIS</h3>
                        <p class="text-zinc-500 text-[10px]">Lakukan manajemen data kendaraan utama showroom.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex gap-2">
                            @if(request('status_filter'))
                                <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                            @endif
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari brand, model, VIN..." class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-[10px] px-3 py-2 focus:border-luxury-gold focus:outline-none w-52">
                            <button type="submit" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-3 py-2 text-[10px] font-bold transition-colors">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                        <button
                            x-on:click="openAdd()"
                            class="bg-luxury-gold hover:bg-luxury-goldHover text-black text-[10px] font-extrabold px-4 py-2.5 tracking-wider uppercase transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i> TAMBAH UNIT
                        </button>
                    </div>
                </div>

                {{-- Tabel Kendaraan --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                            <tr>
                                <th class="p-4">KENDARAAN & SPEK</th>
                                <th class="p-4">VIN (UNIQUE)</th>
                                <th class="p-4">HARGA (IDR)</th>
                                <th class="p-4">GUDANG</th>
                                <th class="p-4">STATUS</th>
                                <th class="p-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-900">
                            @foreach($cars as $car)
                                @php
                                    $statusClass = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';
                                    if ($car->status === 'Booked') $statusClass = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                                    if ($car->status === 'Sold') $statusClass = 'bg-red-950/60 text-red-400 border-red-900/50';
                                @endphp
                                <tr class="hover:bg-zinc-900/30 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 overflow-hidden bg-zinc-900 border border-zinc-800 shrink-0">
                                                <img src="{{ $car->image }}" class="w-full h-full object-cover" alt="{{ $car->model }}">
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block uppercase tracking-wide">{{ $car->brand }} {{ $car->model }}</span>
                                                <span class="text-[10px] text-zinc-500">{{ $car->year }} &middot; {{ $car->engine }} &middot; {{ $car->color }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono font-semibold tracking-wider text-zinc-300 text-[11px]">{{ $car->vin }}</td>
                                    <td class="p-4 text-luxury-gold font-bold font-mono">IDR {{ number_format($car->price, 0, ',', '.') }}</td>
                                    <td class="p-4 text-zinc-400 text-[10px]">{{ $car->warehouse?->name ?? '-' }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 border rounded text-[9px] font-extrabold {{ $statusClass }}">
                                            {{ $car->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <div class="inline-flex gap-1.5">
                                            <button
                                                x-on:click="openEdit({{ json_encode($car) }})"
                                                class="text-luxury-gold hover:text-white transition-colors py-1 px-2 border border-luxury-gold/20 hover:border-luxury-gold rounded text-[10px] bg-zinc-900/50">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus unit ini? Aksi tidak bisa dibatalkan.')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-400 transition-colors py-1 px-2 border border-red-900/30 hover:border-red-500 rounded text-[10px] bg-red-950/20">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($cars->isEmpty())
                        <div class="text-center py-10 text-zinc-500 italic text-xs">
                            <i class="fa-solid fa-inbox text-2xl mb-2 block text-zinc-700"></i>
                            Tidak ada unit kendaraan yang sesuai filter.
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── MODAL CRUD INVENTARIS ─── --}}
            <div
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-on:click.self="modalOpen = false"
                class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
                style="display: none;">

                <div
                    x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-zinc-950 border border-zinc-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl relative"
                    x-on:click.stop>

                    {{-- Modal Header --}}
                    <div class="sticky top-0 bg-zinc-950 border-b border-zinc-900 px-6 py-4 flex justify-between items-center z-10">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-5 bg-luxury-gold"></div>
                                <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-200 uppercase" x-text="editMode ? 'EDIT DATA UNIT' : 'INPUT UNIT BARU'"></h3>
                            </div>
                            <p class="text-zinc-500 text-[10px] pl-3 mt-0.5" x-text="editMode ? 'Perbarui data unit kendaraan yang dipilih.' : 'Tambahkan unit kendaraan baru ke inventaris showroom.'"></p>
                        </div>
                        <button x-on:click="modalOpen = false" class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all rounded">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    {{-- Modal Form --}}
                    <div class="p-6">
                        <form
                            id="carModalForm"
                            :action="editMode ? `/admin/cars/${editId}` : '{{ route('admin.cars.store') }}'"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-4 text-xs">
                            @csrf
                            <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Warehouse --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Lokasi Gudang (Warehouse) <span class="text-red-400">*</span></label>
                                    <select name="warehouse_id" x-model="form.warehouse_id" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->location }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Brand --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Merek (Brand) <span class="text-red-400">*</span></label>
                                    <select name="brand" x-model="form.brand" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                        @foreach(['Porsche','Ferrari','Lamborghini','Chevrolet','BMW','Mercedes-Benz','Toyota','Audi','McLaren'] as $b)
                                            <option value="{{ $b }}">{{ $b }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Model --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Nama Model <span class="text-red-400">*</span></label>
                                    <input type="text" name="model" x-model="form.model" placeholder="Contoh: 911 GT3 RS" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- VIN --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Nomor Rangka (VIN — 17 Karakter) <span class="text-red-400">*</span></label>
                                    <input type="text" name="vin" x-model="form.vin" placeholder="17-Digit Alfanumerik" maxlength="17" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none font-mono tracking-widest" required>
                                    <p class="text-zinc-600 text-[10px] mt-1">Panjang saat ini: <span x-text="(form.vin || '').length"></span>/17 karakter</p>
                                </div>

                                {{-- Tahun --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Tahun Unit <span class="text-red-400">*</span></label>
                                    <input type="number" name="year" x-model="form.year" min="1990" max="2030" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- Warna --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Warna Body <span class="text-red-400">*</span></label>
                                    <input type="text" name="color" x-model="form.color" placeholder="Shark Blue" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- Mesin --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Kapasitas Mesin <span class="text-red-400">*</span></label>
                                    <input type="text" name="engine" x-model="form.engine" placeholder="4.0L Flat-6" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- Transmisi --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Transmisi <span class="text-red-400">*</span></label>
                                    <input type="text" name="transmission" x-model="form.transmission" placeholder="7-Speed PDK" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- Harga --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Harga Unit (IDR) <span class="text-red-400">*</span></label>
                                    <input type="number" name="price" x-model="form.price" placeholder="Tanpa titik/koma" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1.5">Status Ketersediaan <span class="text-red-400">*</span></label>
                                    <select name="status" x-model="form.status" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none">
                                        <option value="Available">Available</option>
                                        <option value="Booked">Booked</option>
                                        <option value="Sold">Sold</option>
                                    </select>
                                </div>


                                {{-- ═══ MULTI-IMAGE UPLOAD ZONE ═══ --}}
                                <div class="sm:col-span-2">
                                    <div class="flex justify-between items-baseline mb-1.5">
                                        <label class="block text-zinc-500 font-bold uppercase tracking-wider">
                                            Foto Kendaraan <span class="text-red-400">*</span>
                                        </label>
                                        <span class="text-zinc-600 text-[10px] font-normal">Format: JPG, PNG, WEBP &bull; Maks. 5 foto</span>
                                    </div>

                                    {{-- Click / Drop Zone --}}
                                    <label
                                        for="imageUploadInput"
                                        :class="imagePreviews.length > 0 ? 'border-luxury-gold/40 bg-zinc-900/80' : 'border-zinc-700 hover:border-luxury-gold/60 bg-zinc-900/50 hover:bg-zinc-900'"
                                        class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed cursor-pointer transition-all duration-300 py-5 px-4 group">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <i
                                                class="fa-solid fa-cloud-arrow-up text-2xl transition-all duration-300"
                                                :class="imagePreviews.length > 0 ? 'text-luxury-gold' : 'text-zinc-600 group-hover:text-luxury-gold/70'"></i>
                                            <span
                                                class="text-[11px] font-semibold transition-colors"
                                                :class="imagePreviews.length > 0 ? 'text-zinc-300' : 'text-zinc-500 group-hover:text-zinc-300'"
                                                x-text="imagePreviews.length > 0 ? 'Klik untuk ganti / tambah foto' : 'Klik untuk memilih foto'"></span>
                                        </div>
                                        <span
                                            class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border transition-all"
                                            :class="imagePreviews.length === 0
                                                ? 'text-zinc-600 border-zinc-800 bg-transparent'
                                                : imagePreviews.length >= 5
                                                    ? 'text-luxury-gold border-luxury-gold/50 bg-luxury-gold/10'
                                                    : 'text-zinc-400 border-zinc-700 bg-zinc-800'"
                                            x-text="imagePreviews.length + ' / 5 foto dipilih'">
                                        </span>
                                    </label>

                                    {{-- Hidden File Input --}}
                                    <input
                                        id="imageUploadInput"
                                        type="file"
                                        name="images[]"
                                        multiple
                                        accept="image/jpeg,image/png,image/webp,image/jpg"
                                        class="hidden"
                                        @change="handleImageUpload($event)">

                                    {{-- Pesan Error --}}
                                    <p
                                        x-show="imageError"
                                        x-text="imageError"
                                        x-transition
                                        class="text-amber-400 text-[10px] mt-1.5 flex items-center gap-1 font-semibold">
                                    </p>

                                    {{-- Grid Preview 5 Slot --}}
                                    <div
                                        x-show="imagePreviews.length > 0"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="mt-3">
                                        <p class="text-zinc-600 text-[10px] uppercase tracking-wider font-bold mb-2">Preview Foto</p>

                                        <div class="grid grid-cols-5 gap-2">
                                            {{-- Foto Terpilih --}}
                                            <template x-for="(src, i) in imagePreviews" :key="i">
                                                <div class="relative group aspect-square bg-zinc-900 border border-zinc-800 overflow-hidden">
                                                    <img
                                                        :src="src"
                                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                        alt="Preview foto">

                                                    {{-- Overlay hover --}}
                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all duration-200 flex items-center justify-center">
                                                        <button
                                                            type="button"
                                                            @click.prevent="removeImage(i)"
                                                            class="w-6 h-6 bg-red-600 hover:bg-red-500 text-white text-[10px] font-black opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center shadow-lg"
                                                            title="Hapus foto ini">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Label foto utama (index 0) --}}
                                                    <div
                                                        x-show="i === 0"
                                                        class="absolute bottom-0 left-0 right-0 bg-luxury-gold text-black text-[8px] font-black uppercase tracking-widest text-center py-0.5 leading-tight">
                                                        Utama
                                                    </div>

                                                    {{-- Nomor untuk foto lain --}}
                                                    <div
                                                        x-show="i !== 0"
                                                        class="absolute top-1 left-1 w-4 h-4 bg-black/70 text-zinc-300 text-[9px] font-bold flex items-center justify-center">
                                                        <span x-text="i + 1"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Slot Kosong Placeholder --}}
                                            <template x-for="n in Math.max(0, 5 - imagePreviews.length)" :key="'ph-' + n">
                                                <div class="aspect-square bg-zinc-900/40 border border-dashed border-zinc-800 flex items-center justify-center">
                                                    <i class="fa-regular fa-image text-zinc-700 text-sm"></i>
                                                </div>
                                            </template>
                                        </div>

                                        <p class="text-zinc-600 text-[10px] mt-2 leading-relaxed">
                                            Foto <span class="text-luxury-gold font-semibold">pertama</span> akan jadi foto utama katalog.
                                            Klik <span class="text-red-400 font-bold">&#10005;</span> pada thumbnail untuk menghapus.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3 pt-4 border-t border-zinc-900">
                                <button type="button" x-on:click="modalOpen = false" class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-3 tracking-widest uppercase transition-colors text-xs">
                                    BATAL
                                </button>
                                <button type="submit" class="flex-[2] bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 tracking-widest uppercase transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span x-text="editMode ? 'UPDATE DATA UNIT' : 'SIMPAN DATA UNIT'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────
             TABEL TRANSAKSI + FILTER + QUICK ACTIONS
             ───────────────────────────────────────────────── --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                <div>
                    <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">LAPORAN KEUANGAN & TRANSAKSI</h3>
                    <p class="text-zinc-500 text-[10px]">Log verifikasi pembayaran, DP, pelunasan, dan bukti transfer.</p>
                </div>
                <span class="bg-emerald-950/40 text-emerald-400 border border-emerald-900/30 text-[9px] font-bold px-3 py-1 rounded">Financial Log</span>
            </div>

            {{-- Filter Tab Buttons --}}
            <div class="flex flex-wrap gap-2 mb-6">
                @php
                    $filters = [
                        'all'      => ['label' => 'Semua', 'icon' => 'fa-list'],
                        'pending'  => ['label' => 'Pending Validation', 'icon' => 'fa-hourglass-half'],
                        'verified' => ['label' => 'Down Payment', 'icon' => 'fa-hand-holding-dollar'],
                        'paid'     => ['label' => 'Paid', 'icon' => 'fa-circle-check'],
                    ];
                @endphp
                @foreach($filters as $key => $f)
                    <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['status_filter' => $key])) }}"
                       class="text-[10px] font-bold px-4 py-2 border tracking-wider transition-all flex items-center gap-1.5
                              {{ $statusFilter === $key
                                  ? 'bg-luxury-gold text-black border-luxury-gold'
                                  : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:border-zinc-600 hover:text-white' }}">
                        <i class="fa-solid {{ $f['icon'] }} text-[9px]"></i> {{ $f['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Tabel Transaksi --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                        <tr>
                            <th class="p-4">INVOICE</th>
                            <th class="p-4">PELANGGAN</th>
                            <th class="p-4">UNIT KENDARAAN</th>
                            <th class="p-4">TIPE</th>
                            <th class="p-4">NOMINAL (IDR)</th>
                            <th class="p-4">BUKTI</th>
                            <th class="p-4">STATUS</th>
                            <th class="p-4 text-right">QUICK ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900" id="transactions-tbody">
                        @forelse($bookings as $bk)
                            @php
                                $paymentBadgeClass = 'bg-zinc-950 text-zinc-400 border-zinc-800';
                                if ($bk->payment_status === 'Pending Validation') $paymentBadgeClass = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                                if ($bk->payment_status === 'Down Payment') $paymentBadgeClass = 'bg-blue-950/60 text-blue-400 border-blue-900/50';
                                if ($bk->payment_status === 'Paid') $paymentBadgeClass = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';
                            @endphp
                            <tr class="hover:bg-zinc-900/30 transition-colors" id="bk-row-{{ $bk->id }}">
                                <td class="p-4">
                                    <span class="font-mono text-[10px] text-luxury-gold/80 font-semibold">{{ $bk->invoice_code }}</span>
                                    <span class="block text-zinc-600 text-[9px]">{{ $bk->date }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-zinc-100 block">{{ $bk->customer_name }}</span>
                                    <span class="text-[10px] text-zinc-500"><i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>{{ $bk->phone }}</span>
                                </td>
                                <td class="p-4 font-semibold text-zinc-400">{{ $bk->car?->brand }} {{ $bk->car?->model }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 border text-[9px] rounded font-bold {{ $paymentBadgeClass }}" id="bk-status-badge-{{ $bk->id }}">
                                        {{ $bk->payment_status }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold font-mono text-emerald-400">IDR {{ number_format($bk->paid_amount, 0, ',', '.') }}</td>
                                <td class="p-4">
                                    @if($bk->payment_proof)
                                        <a href="{{ asset('storage/' . $bk->payment_proof) }}" target="_blank" class="text-luxury-gold hover:text-white text-[9px] underline flex items-center gap-1">
                                            <i class="fa-regular fa-image"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-zinc-600 italic text-[10px]">Belum ada</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @php
                                        $inspBadge = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                                        if ($bk->status === 'Approved') $inspBadge = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';
                                        if ($bk->status === 'Rejected') $inspBadge = 'bg-red-950/60 text-red-400 border-red-900/50';
                                    @endphp
                                    <span class="px-2 py-0.5 border text-[9px] rounded font-bold {{ $inspBadge }}" id="bk-insp-badge-{{ $bk->id }}">
                                        {{ $bk->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-1.5 flex-wrap" id="bk-actions-{{ $bk->id }}">
                                        {{-- Verifikasi Pembayaran --}}
                                        @if($bk->payment_status === 'Pending Validation')
                                            <button
                                                onclick="ajaxVerifyPayment({{ $bk->id }})"
                                                class="bg-emerald-700 hover:bg-emerald-600 text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider flex items-center gap-1 transition-colors">
                                                <i class="fa-solid fa-check"></i> Sahkan
                                            </button>
                                        @endif

                                        {{-- Setujui/Tolak Inspeksi --}}
                                        @if($bk->status === 'Pending')
                                            <button
                                                onclick="ajaxProcessInspection({{ $bk->id }}, 'Approved')"
                                                class="bg-zinc-700 hover:bg-emerald-800 text-zinc-300 hover:text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider flex items-center gap-1 transition-colors">
                                                <i class="fa-solid fa-calendar-check"></i> Setujui
                                            </button>
                                            <button
                                                onclick="ajaxProcessInspection({{ $bk->id }}, 'Rejected')"
                                                class="bg-zinc-700 hover:bg-red-900 text-zinc-300 hover:text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider flex items-center gap-1 transition-colors">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        @endif

                                        @if($bk->payment_status === 'Paid' && $bk->status === 'Approved')
                                            <span class="text-emerald-400 text-[9px] font-bold flex items-center gap-1">
                                                <i class="fa-solid fa-lock"></i> Selesai
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-zinc-500 italic text-xs">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-zinc-700"></i>
                                    Tidak ada transaksi yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────
             ANTREAN INSPEKSI
             ───────────────────────────────────────────────── --}}
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-center mb-4 border-b border-zinc-900 pb-3">
                <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">
                    <i class="fa-solid fa-calendar-check text-amber-500 mr-1.5"></i> ANTREAN INSPEKSI PENDING
                </h3>
                @php $pendingInspCount = $bookings->where('status', 'Pending')->count(); @endphp
                <span class="bg-amber-950/60 border border-amber-900/50 text-amber-400 text-[9px] font-bold px-2 py-0.5 rounded-full">
                    {{ $pendingInspCount }}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($bookings->where('status', 'Pending') as $booking)
                    @php
                        $inspStatusColor = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                    @endphp
                    <div class="bg-zinc-900/40 border border-zinc-900 p-4" id="insp-card-{{ $booking->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="text-[10px] font-bold text-white block tracking-wide">{{ strtoupper($booking->customer_name) }}</span>
                                <span class="text-[9px] text-zinc-500"><i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>{{ $booking->phone }}</span>
                            </div>
                            <span class="px-2 py-0.5 tracking-wider border rounded text-[8px] uppercase font-bold {{ $inspStatusColor }}">Pending</span>
                        </div>
                        <div class="text-[9px] text-zinc-400 mb-3 pb-3 border-b border-zinc-900">
                            <p>Unit: <strong class="text-zinc-300">{{ $booking->car?->brand }} {{ $booking->car?->model }}</strong></p>
                            <p class="font-mono text-luxury-gold mt-1"><i class="fa-regular fa-calendar text-[8px] mr-1"></i>{{ $booking->date }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                onclick="ajaxProcessInspection({{ $booking->id }}, 'Approved')"
                                class="flex-1 bg-emerald-700 hover:bg-emerald-600 text-white font-bold py-1.5 text-[9px] uppercase tracking-wider transition-colors">
                                <i class="fa-solid fa-check mr-1"></i>Setujui
                            </button>
                            <button
                                onclick="ajaxProcessInspection({{ $booking->id }}, 'Rejected')"
                                class="flex-1 bg-zinc-800 hover:bg-red-900 text-zinc-400 hover:text-white font-bold py-1.5 text-[9px] uppercase tracking-wider transition-colors">
                                <i class="fa-solid fa-xmark mr-1"></i>Tolak
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-zinc-500 text-[10px] text-center py-6 italic">
                        <i class="fa-solid fa-check-double text-emerald-600 text-xl mb-2 block"></i>
                        Tidak ada antrean inspeksi yang menunggu.
                    </div>
                @endforelse
            </div>
        </div>

    </div>{{-- end .space-y-10 --}}
</div>
@endsection

@section('scripts')
<script>
// ============================================================
// AJAX Quick Action Helper
// ============================================================
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function showToast(type, message) {
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: { type, message }
    }));
}

function ajaxPost(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    }).then(res => res.json());
}

// ============================================================
// Verifikasi Pembayaran AJAX
// ============================================================
async function ajaxVerifyPayment(invoiceId) {
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

    try {
        const data = await ajaxPost(`/admin/booking/${invoiceId}/verify-ajax`, {});

        if (data.success) {
            showToast('success', data.message);

            const badge = document.getElementById(`bk-status-badge-${invoiceId}`);
            if (badge) {
                badge.textContent = data.new_payment_status;
                badge.className = data.new_payment_status === 'Paid'
                    ? 'px-2 py-0.5 border text-[9px] rounded font-bold bg-emerald-950/60 text-emerald-400 border-emerald-900/50'
                    : 'px-2 py-0.5 border text-[9px] rounded font-bold bg-blue-950/60 text-blue-400 border-blue-900/50';
            }

            const actionsDiv = document.getElementById(`bk-actions-${invoiceId}`);
            if (actionsDiv) {
                const verifyBtn = actionsDiv.querySelector('button[onclick*="ajaxVerifyPayment"]');
                if (verifyBtn) verifyBtn.remove();

                if (data.new_payment_status === 'Paid' && actionsDiv.querySelectorAll('button').length === 0) {
                    actionsDiv.innerHTML = '<span class="text-emerald-400 text-[9px] font-bold flex items-center gap-1"><i class="fa-solid fa-lock"></i> Selesai</span>';
                }
            }
        } else {
            showToast('error', data.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Sahkan';
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Sahkan';
    }
}

// ============================================================
// Proses Inspeksi AJAX
// ============================================================
async function ajaxProcessInspection(invoiceId, status) {
    const btn = event.currentTarget;
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const data = await ajaxPost(`/admin/booking/${invoiceId}/status-ajax`, { status });

        if (data.success) {
            showToast('success', data.message);

            const inspBadge = document.getElementById(`bk-insp-badge-${invoiceId}`);
            if (inspBadge) {
                inspBadge.textContent = data.new_status;
                inspBadge.className = data.new_status === 'Approved'
                    ? 'px-2 py-0.5 border text-[9px] rounded font-bold bg-emerald-950/60 text-emerald-400 border-emerald-900/50'
                    : 'px-2 py-0.5 border text-[9px] rounded font-bold bg-red-950/60 text-red-400 border-red-900/50';
            }

            const actionsDiv = document.getElementById(`bk-actions-${invoiceId}`);
            if (actionsDiv) {
                const approveBtn = actionsDiv.querySelector('button[onclick*="Approved"]');
                const rejectBtn = actionsDiv.querySelector('button[onclick*="Rejected"]');
                if (approveBtn) approveBtn.remove();
                if (rejectBtn) rejectBtn.remove();
            }

            const card = document.getElementById(`insp-card-${invoiceId}`);
            if (card) {
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            }
        } else {
            showToast('error', data.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}
</script>
@endsection
