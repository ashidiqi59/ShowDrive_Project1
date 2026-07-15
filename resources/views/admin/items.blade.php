@extends('layouts.admin')

@section('title', 'Inventaris Kendaraan')

@section('content')
<div class="space-y-8" x-data="{
    modalOpen: false,
    editMode: false,
    editId: null,
    form: {
        brand: 'Porsche', model: '', vin: '', year: 2024,
        color: '', engine: '', transmission: '', price: '',
        dp_percentage: 20, status: 'Available', warehouse_id: ''
    },
    openAdd() {
        this.editMode = false;
        this.editId = null;
        this.form = { brand: 'Porsche', model: '', vin: '', year: 2024, color: '', engine: '', transmission: '', price: '', dp_percentage: 20, status: 'Available', warehouse_id: '' };
        this.modalOpen = true;
    },
    openEdit(car) {
        this.editMode = true;
        this.editId = car.id;
        this.form = {
            brand: car.brand, model: car.model, vin: car.vin, year: car.year,
            color: car.color, engine: car.engine, transmission: car.transmission,
            price: car.price, dp_percentage: car.dp_percentage ?? 20,
            status: car.status, warehouse_id: car.warehouse_id
        };
        this.modalOpen = true;
    }
}" @keydown.escape.window="modalOpen = false">

    {{-- ══════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">INVENTORY SYSTEM</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">INVENTARIS KENDARAAN</h2>
            <p class="text-zinc-500 text-xs mt-1">Kelola data stok kendaraan, spesifikasi, dan ketersediaan unit.</p>
        </div>
        <button @click="openAdd()" class="flex-shrink-0 bg-luxury-gold hover:bg-luxury-goldHover text-black text-[10px] font-extrabold px-6 py-3 tracking-wider uppercase transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> INPUT UNIT BARU
        </button>
    </div>


    {{-- ══════════════════════════════════════════
         TABEL INVENTARIS
    ══════════════════════════════════════════ --}}
    <div class="bg-zinc-950 border border-zinc-900 shadow-2xl">
        {{-- Search bar --}}
        <div class="px-6 py-4 border-b border-zinc-900 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-luxury-gold"></div>
                <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">DAFTAR UNIT</h3>
                <span class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-[9px] font-bold px-2.5 py-0.5 rounded-full">{{ $cars->count() }} Unit</span>
            </div>
            <form method="GET" action="{{ route('admin.items') }}" class="flex gap-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-zinc-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari brand, model, atau VIN..."
                        class="w-64 bg-zinc-900 border border-zinc-800 text-xs p-2.5 pl-9 focus:border-luxury-gold focus:outline-none text-zinc-300 placeholder-zinc-600">
                </div>
                <button type="submit" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 text-[10px] font-bold uppercase tracking-wider transition-colors">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.items') }}" class="bg-zinc-900 hover:bg-zinc-800 text-zinc-500 hover:text-white px-3 py-2 text-[10px] font-bold transition-colors border border-zinc-800">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-zinc-900/60 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                    <tr>
                        <th class="p-4">KENDARAAN & SPESIFIKASI</th>
                        <th class="p-4">VIN (UNIQUE)</th>
                        <th class="p-4">GUDANG</th>
                        <th class="p-4">HARGA (IDR)</th>
                        <th class="p-4 text-center">STATUS</th>
                        <th class="p-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @forelse($cars as $car)
                        @php
                            $statusClass = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';
                            $statusText  = 'Available';
                            if ($car->status === 'Booked' || $car->status === 'Invoiced') {
                                $statusClass = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                                $statusText  = 'Booked';
                            }
                            if ($car->status === 'Sold') {
                                $statusClass = 'bg-red-950/60 text-red-400 border-red-900/50';
                                $statusText  = 'Sold';
                            }
                            // Get primary image
                            $imgSrc = $car->images && $car->images->count() > 0
                                ? asset('storage/' . $car->images->first()->image_path)
                                : ($car->image_url ? asset('storage/' . $car->image_url) : 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=200&q=60');
                        @endphp
                        <tr class="hover:bg-zinc-900/30 transition-colors group" id="car-row-{{ $car->id }}">
                            {{-- Kendaraan & Spek --}}
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 overflow-hidden bg-zinc-900 border border-zinc-800 flex-shrink-0">
                                        <img src="{{ $imgSrc }}" alt="{{ $car->brand }} {{ $car->model }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=200&q=60'">
                                    </div>
                                    <div>
                                        <span class="font-black text-white block uppercase tracking-wide text-[11px]">{{ $car->brand }} {{ $car->model }}</span>
                                        <span class="text-[9px] text-zinc-500 block">{{ $car->year }} &bull; {{ $car->color }} &bull; {{ $car->engine }}</span>
                                        <span class="text-[9px] text-zinc-600">{{ $car->transmission }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- VIN --}}
                            <td class="p-4 font-mono font-semibold tracking-widest text-zinc-300 text-[10px]">{{ $car->vin }}</td>

                            {{-- Gudang --}}
                            <td class="p-4 text-zinc-500 text-[10px]">
                                @if($car->warehouse)
                                    <span class="block text-zinc-300 font-semibold">{{ $car->warehouse->name }}</span>
                                    <span class="text-zinc-600">{{ strlen($car->warehouse->location ?? '') > 25 ? substr($car->warehouse->location, 0, 25) . '...' : ($car->warehouse->location ?? '—') }}</span>
                                @else
                                    <span class="italic text-zinc-700">—</span>
                                @endif
                            </td>

                            {{-- Harga --}}
                            <td class="p-4 font-black font-mono text-luxury-gold">
                                IDR {{ number_format($car->price, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 border rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="inline-flex gap-1.5">
                                    <button @click="openEdit({{ json_encode($car->load('images')) }})"
                                        class="text-luxury-gold hover:text-white transition-colors py-1.5 px-3 border border-luxury-gold/20 hover:border-luxury-gold rounded text-[10px] bg-zinc-900/50 hover:bg-zinc-800">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i>Edit
                                    </button>
                                    <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus unit {{ $car->brand }} {{ $car->model }}? Pastikan tidak ada invoice aktif.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-300 transition-colors py-1.5 px-2.5 border border-red-900/30 hover:border-red-500 rounded text-[10px] bg-red-950/20 hover:bg-red-950/40">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-zinc-500 italic text-xs">
                                <i class="fa-solid fa-car text-4xl mb-3 block text-zinc-800"></i>
                                @if(request('search'))
                                    Tidak ditemukan unit dengan kata kunci "<strong class="text-zinc-400">{{ request('search') }}</strong>".
                                @else
                                    Belum ada unit kendaraan yang terdaftar di inventaris.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($cars->hasPages())
            <div class="px-6 py-4 border-t border-zinc-900 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-zinc-600 text-[10px] font-mono">
                    Menampilkan {{ $cars->firstItem() }}–{{ $cars->lastItem() }} dari {{ $cars->total() }} unit
                </p>
                <nav class="flex items-center gap-1">
                    @if($cars->onFirstPage())
                        <span class="px-3 py-1.5 text-[10px] font-bold text-zinc-700 border border-zinc-900 cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $cars->previousPageUrl() }}"
                           class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach($cars->getUrlRange(1, $cars->lastPage()) as $page => $url)
                        @if($page == $cars->currentPage())
                            <span class="px-3 py-1.5 text-[10px] font-bold bg-luxury-gold text-black">{{ $page }}</span>
                        @elseif(abs($page - $cars->currentPage()) <= 2)
                            <a href="{{ $url }}" class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">{{ $page }}</a>
                        @elseif(abs($page - $cars->currentPage()) == 3)
                            <span class="px-2 py-1.5 text-[10px] text-zinc-700">...</span>
                        @endif
                    @endforeach

                    @if($cars->hasMorePages())
                        <a href="{{ $cars->nextPageUrl() }}"
                           class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-[10px] font-bold text-zinc-700 border border-zinc-900 cursor-not-allowed">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         MODAL FORM UNIT
    ══════════════════════════════════════════ --}}
    <div x-show="modalOpen" style="display:none;"
         class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] flex items-start justify-center p-4 overflow-y-auto">
        <div x-show="modalOpen" @click.stop
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-zinc-950 border border-zinc-800 w-full max-w-2xl shadow-2xl my-8 relative">

            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-zinc-900 flex justify-between items-center sticky top-0 bg-zinc-950 z-10">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5 bg-luxury-gold"></div>
                    <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-200 uppercase"
                        x-text="editMode ? 'EDIT DATA UNIT' : 'INPUT UNIT BARU'"></h3>
                </div>
                <button @click="modalOpen = false" class="text-zinc-500 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Modal Body: Form --}}
            <form :action="editMode ? `/admin/cars/${editId}` : '{{ route('admin.cars.store') }}'"
                  method="POST" enctype="multipart/form-data" class="p-6 space-y-5 text-xs">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                {{-- Baris 1: Brand & Model --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Merek (Brand) <span class="text-red-500">*</span></label>
                        <input type="text" name="brand" x-model="form.brand" placeholder="Porsche, Ferrari, BMW..." class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Nama Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" x-model="form.model" placeholder="Contoh: 911 GT3 RS" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                </div>

                {{-- Baris 2: VIN --}}
                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Nomor Rangka VIN (17 Karakter) <span class="text-red-500">*</span></label>
                    <input type="text" name="vin" x-model="form.vin" placeholder="WP0ZZZ99ZTS392124" maxlength="17"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors font-mono tracking-widest" required>
                </div>

                {{-- Baris 3: Tahun & Warna --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Tahun Produksi <span class="text-red-500">*</span></label>
                        <input type="number" name="year" x-model="form.year" min="1990" max="2030" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Warna Body <span class="text-red-500">*</span></label>
                        <input type="text" name="color" x-model="form.color" placeholder="Shark Blue Metallic" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                </div>

                {{-- Baris 4: Mesin & Transmisi --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Kapasitas Mesin <span class="text-red-500">*</span></label>
                        <input type="text" name="engine" x-model="form.engine" placeholder="4.0L Flat-6 Natural" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Transmisi <span class="text-red-500">*</span></label>
                        <input type="text" name="transmission" x-model="form.transmission" placeholder="7-Speed PDK" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                    </div>
                </div>

                {{-- Baris 5: Harga, DP %, & Gudang --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Harga Jual (IDR) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" x-model="form.price" placeholder="Nominal tanpa titik" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required min="0">
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">
                            DP % <span class="text-red-500">*</span>
                            <span class="text-zinc-600 normal-case font-normal ml-1">(1–100)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="dp_percentage" x-model="form.dp_percentage"
                                min="1" max="100" placeholder="20"
                                class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 pr-8 focus:border-luxury-gold focus:outline-none transition-colors"
                                required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs font-bold pointer-events-none">%</span>
                        </div>
                        {{-- Preview nominal DP secara real-time --}}
                        <p class="text-[9px] text-luxury-gold/70 font-mono mt-1"
                           x-show="form.price && form.dp_percentage"
                           x-text="'DP: IDR ' + new Intl.NumberFormat('id-ID').format(Math.round(form.price * form.dp_percentage / 100))">
                        </p>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Lokasi Gudang <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" x-model="form.warehouse_id" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                            <option value="">-- Pilih Gudang --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Baris 6: Status & Foto --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Status Ketersediaan <span class="text-red-500">*</span></label>
                        <select name="status" x-model="form.status" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                            <option value="Available">Available</option>
                            <option value="Booked">Booked</option>
                            <option value="Sold">Sold</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Foto Unit</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                            class="w-full bg-zinc-900 border border-zinc-800 text-zinc-400 p-2.5 focus:border-luxury-gold focus:outline-none transition-colors file:bg-zinc-800 file:text-zinc-300 file:border-0 file:px-3 file:py-1 file:text-[10px] file:font-bold file:uppercase file:mr-3">
                        <p x-show="editMode" class="text-[9px] text-zinc-600 mt-1 italic">* Kosongkan jika tidak ingin mengganti foto.</p>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-5 border-t border-zinc-900 mt-4">
                    <button type="button" @click="modalOpen = false"
                        class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-3 tracking-widest uppercase transition-colors text-[10px]">
                        BATAL
                    </button>
                    <button type="submit"
                        class="flex-[2] bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 tracking-widest uppercase transition-all flex items-center justify-center gap-2 text-[10px]">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span x-text="editMode ? 'UPDATE DATA UNIT' : 'SIMPAN UNIT BARU'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
