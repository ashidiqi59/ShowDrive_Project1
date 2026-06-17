@extends('layouts.app')

@section('title', 'Admin Dashboard - ShowDrive')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 no-print">
    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-8 border-b border-zinc-900 gap-4 mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
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

    <!-- Analitika Box -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-12">
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-start mb-2">
                <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Total Unit</span>
                <i class="fa-solid fa-car text-luxury-gold text-sm"></i>
            </div>
            <span class="text-2xl font-bold font-mono">{{ $totalCars }} Unit</span>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-start mb-2">
                <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Available</span>
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
            </div>
            <span class="text-2xl font-bold font-mono text-emerald-400">{{ $availableCars }} Unit</span>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-start mb-2">
                <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Booked</span>
                <i class="fa-solid fa-calendar-days text-amber-500 text-sm"></i>
            </div>
            <span class="text-2xl font-bold font-mono text-amber-400">{{ $bookedCars }} Unit</span>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-start mb-2">
                <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Sold</span>
                <i class="fa-solid fa-tags text-red-500 text-sm"></i>
            </div>
            <span class="text-2xl font-bold font-mono text-red-400">{{ $soldCars }} Unit</span>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-6">
            <div class="flex justify-between items-start mb-2">
                <span class="text-zinc-500 text-[10px] uppercase font-bold tracking-wider">Dana Masuk</span>
                <i class="fa-solid fa-wallet text-emerald-400 text-sm"></i>
            </div>
            <span class="text-2xl font-bold font-mono text-emerald-400">IDR {{ number_format($incomingCash, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Panel CRUD & Financial Log -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Left: CRUD Table (Takes 2 Cols) -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Tabel Inventaris -->
            <div class="bg-zinc-950 border border-zinc-900 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                    <div>
                        <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">DAFTAR INVENTARIS</h3>
                        <p class="text-zinc-500 text-[10px]">Lakukan manajemen data kendaraan utama showroom.</p>
                    </div>
                    <span class="bg-zinc-900 text-zinc-400 text-[9px] font-bold px-3 py-1 rounded">Relational master_cars</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                            <tr>
                                <th class="p-4">KENDARAAN & SPEK</th>
                                <th class="p-4">VIN (UNIQUE)</th>
                                <th class="p-4">HARGA (IDR)</th>
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
                                            <div class="w-10 h-10 overflow-hidden bg-zinc-900 border border-zinc-800">
                                                <img src="{{ $car->image }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block uppercase tracking-wide">{{ $car->brand }} {{ $car->model }}</span>
                                                <span class="text-[10px] text-zinc-500">{{ $car->year }} | {{ $car->engine }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono font-semibold tracking-wider text-zinc-300 text-[11px]">{{ $car->vin }}</td>
                                    <td class="p-4 text-luxury-gold font-bold font-mono">IDR {{ number_format($car->price, 0, ',', '.') }}</td>
                                    <td class="p-4"><span class="px-2.5 py-1 border rounded text-[9px] font-extrabold {{ $statusClass }}">{{ $car->status }}</span></td>
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <div class="inline-flex gap-1.5">
                                            <button onclick="editCar({{ json_encode($car) }})" class="text-luxury-gold hover:text-white transition-colors py-1 px-2 border border-luxury-gold/20 hover:border-luxury-gold rounded text-[10px] bg-zinc-900/50">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?')" class="inline">
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
                </div>
            </div>

            <!-- Laporan Keuangan (Financial Log) -->
            <div class="bg-zinc-950 border border-zinc-900 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">LAPORAN KEUANGAN & PEMBAYARAN</h3>
                        <p class="text-zinc-500 text-[10px]">Log pencatatan verifikasi masuk, DP, pelunasan beserta bukti pembayaran.</p>
                    </div>
                    <span class="bg-emerald-950/40 text-emerald-400 border border-emerald-900/30 text-[9px] font-bold px-3 py-1 rounded">Financial Log</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                            <tr>
                                <th class="p-4">PELANGGAN</th>
                                <th class="p-4">UNIT KENDARAAN</th>
                                <th class="p-4">TIPE</th>
                                <th class="p-4">NOMINAL (IDR)</th>
                                <th class="p-4">VERIFIKASI BUKTI</th>
                                <th class="p-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-900">
                            @foreach($bookings as $bk)
                                @php
                                    $paymentBadgeClass = "bg-red-950/60 text-red-400 border-red-900/50";
                                    if ($bk->payment_status === 'Down Payment') $paymentBadgeClass = "bg-amber-950/60 text-amber-400 border-amber-900/50";
                                    if ($bk->payment_status === 'Paid') $paymentBadgeClass = "bg-emerald-950/60 text-emerald-400 border-emerald-900/50";
                                @endphp
                                <tr class="hover:bg-zinc-900/30 transition-colors">
                                    <td class="p-4 font-bold text-zinc-100">{{ $bk->customer_name }}</td>
                                    <td class="p-4 font-semibold text-zinc-400">{{ $bk->car->brand }} {{ $bk->car->model }}</td>
                                    <td class="p-4"><span class="px-2 py-0.5 border text-[9px] rounded font-bold {{ $paymentBadgeClass }}">{{ $bk->payment_status }}</span></td>
                                    <td class="p-4 font-bold font-mono text-emerald-400">IDR {{ number_format($bk->paid_amount, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        @if($bk->payment_proof)
                                            <div class="flex items-center gap-2">
                                                <span class="text-emerald-400 text-[10px]"><i class="fa-regular fa-image"></i> Ada Berkas</span>
                                                <a href="{{ asset('storage/' . $bk->payment_proof) }}" target="_blank" class="underline text-luxury-gold hover:text-white text-[9px]">Lihat</a>
                                            </div>
                                        @else
                                            <span class="text-zinc-500 italic">Belum Mengirim Bukti</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($bk->payment_status !== 'Paid' && $bk->payment_proof)
                                            <form action="{{ route('admin.booking.verify', $bk->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider">
                                                    <i class="fa-solid fa-check mr-1"></i> Sahkan Lunas
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-zinc-500 font-bold tracking-wider text-[9px] uppercase"><i class="fa-solid fa-lock text-zinc-600"></i> Locked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Form Input & Antrean Inspeksi -->
        <div class="space-y-6">
            <!-- Form Tambah/Edit Mobil -->
            <div class="bg-zinc-950 border border-zinc-900 p-6">
                <h3 id="form-action-title" class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase mb-4 border-b border-zinc-900 pb-3">
                    <i class="fa-solid fa-plus text-luxury-gold mr-1.5"></i> INPUT UNIT BARU
                </h3>
                <form id="carForm" action="{{ route('admin.cars.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">
                    <input type="hidden" id="form-edit-id" value="">
                    
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Merek (Brand)</label>
                        <select id="form-brand" name="brand" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                            <option value="Porsche">Porsche</option>
                            <option value="Ferrari">Ferrari</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="BMW">BMW</option>
                            <option value="Toyota">Toyota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Nama Model Kendaraan</label>
                        <input type="text" id="form-model" name="model" placeholder="Contoh: 911 GT3 RS" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Nomor Rangka (VIN - Unique)</label>
                        <input type="text" id="form-vin" name="vin" placeholder="17-Digit Alfanumerik" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none font-mono tracking-widest" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Tahun Unit</label>
                            <input type="number" id="form-year" name="year" value="2024" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Warna Body</label>
                            <input type="text" id="form-color" name="color" placeholder="Shark Blue" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Kapasitas Mesin</label>
                            <input type="text" id="form-engine" name="engine" placeholder="4.0L Flat-6" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Transmisi</label>
                            <input type="text" id="form-transmission" name="transmission" placeholder="7-Speed PDK" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Harga Unit (IDR)</label>
                        <input type="number" id="form-price" name="price" placeholder="Nilai Nominal Tanpa Titik" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Foto Sampul (Link URL)</label>
                        <input type="text" id="form-image" name="image" value="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=600&q=80" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-500 p-2.5 focus:border-luxury-gold focus:outline-none text-[10px]" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Status Ketersediaan</label>
                        <select id="form-status" name="status" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 p-2.5 focus:border-luxury-gold focus:outline-none">
                            <option value="Available">Available</option>
                            <option value="Booked">Booked</option>
                            <option value="Sold">Sold</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" id="btn-cancel-edit" onclick="resetFormState()" class="hidden w-1/3 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-3 tracking-widest uppercase transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="btn-submit-form" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 tracking-widest uppercase transition-all">
                            SIMPAN DATA
                        </button>
                    </div>
                </form>
            </div>

            <!-- Antrean Inspeksi -->
            <div class="bg-zinc-950 border border-zinc-900 p-6">
                <div class="flex justify-between items-center mb-4 border-b border-zinc-900 pb-3">
                    <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">
                        <i class="fa-solid fa-calendar-check text-amber-500 mr-1.5"></i> ANTREAN INSPEKSI
                    </h3>
                    <span class="bg-amber-950/60 border border-amber-900/50 text-amber-400 text-[9px] font-bold px-2 py-0.5 rounded-full">
                        {{ $bookings->where('status', 'Pending')->count() }}
                    </span>
                </div>
                <div id="booking-logs-container" class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse($bookings as $booking)
                        @php
                            $statusBadgeColor = "bg-amber-950/60 text-amber-400 border-amber-900/50";
                            if ($booking->status === 'Approved') $statusBadgeColor = "bg-emerald-950/60 text-emerald-400 border-emerald-900/50";
                            if ($booking->status === 'Rejected') $statusBadgeColor = "bg-red-950/60 text-red-400 border-red-900/50";
                        @endphp
                        <div class="bg-zinc-900/40 border border-zinc-900 p-4 flex flex-col justify-between gap-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] font-bold text-white block tracking-wide">{{ strtoupper($booking->customer_name) }}</span>
                                    <span class="text-[9px] text-zinc-500"><i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>{{ $booking->phone }}</span>
                                </div>
                                <span class="px-2 py-0.5 tracking-wider border rounded text-[8px] uppercase font-bold {{ $statusBadgeColor }}">{{ $booking->status }}</span>
                            </div>
                            <div class="pt-2 border-t border-zinc-900 flex justify-between items-center text-[9px] text-zinc-400">
                                <span>Unit: {{ $booking->car->brand }} {{ $booking->car->model }}</span>
                                <span class="font-mono text-luxury-gold font-semibold"><i class="fa-regular fa-calendar text-[8px] mr-1"></i> {{ $booking->date }}</span>
                            </div>
                            
                            @if($booking->status === 'Pending')
                                <div class="flex gap-2 mt-3 pt-3 border-t border-zinc-900">
                                    <form action="{{ route('admin.booking.status', $booking->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="Approved">
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1 text-[9px] uppercase tracking-wider rounded-none">
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.booking.status', $booking->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="w-full bg-zinc-800 hover:bg-red-900 text-zinc-400 hover:text-white font-bold py-1 text-[9px] uppercase tracking-wider rounded-none">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-zinc-500 text-[10px] text-center py-6 italic">Belum ada antrean jadwal booking.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editCar(car) {
        document.getElementById('form-edit-id').value = car.id;
        document.getElementById('form-brand').value = car.brand;
        document.getElementById('form-model').value = car.model;
        document.getElementById('form-vin').value = car.vin;
        document.getElementById('form-year').value = car.year;
        document.getElementById('form-color').value = car.color;
        document.getElementById('form-engine').value = car.engine;
        document.getElementById('form-transmission').value = car.transmission;
        document.getElementById('form-price').value = car.price;
        document.getElementById('form-image').value = car.image;
        document.getElementById('form-status').value = car.status;

        // Update form action and method
        const form = document.getElementById('carForm');
        form.action = `/admin/cars/${car.id}`;
        document.getElementById('form-method').value = 'PUT';

        document.getElementById('form-action-title').innerHTML = `<i class="fa-solid fa-pen-to-square text-luxury-gold mr-1.5"></i> EDIT DATA MOBIL`;
        document.getElementById('btn-submit-form').innerText = "UPDATE DATA UNIT";
        document.getElementById('btn-cancel-edit').classList.remove('hidden');
        
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function resetFormState() {
        document.getElementById('form-edit-id').value = '';
        document.getElementById('form-brand').value = 'Porsche';
        document.getElementById('form-model').value = '';
        document.getElementById('form-vin').value = '';
        document.getElementById('form-year').value = '2024';
        document.getElementById('form-color').value = '';
        document.getElementById('form-engine').value = '';
        document.getElementById('form-transmission').value = '';
        document.getElementById('form-price').value = '';
        document.getElementById('form-image').value = 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=600&q=80';
        document.getElementById('form-status').value = 'Available';

        const form = document.getElementById('carForm');
        form.action = "{{ route('admin.cars.store') }}";
        document.getElementById('form-method').value = 'POST';

        document.getElementById('form-action-title').innerHTML = `<i class="fa-solid fa-plus text-luxury-gold mr-1.5"></i> INPUT UNIT BARU`;
        document.getElementById('btn-submit-form').innerText = "SIMPAN DATA";
        document.getElementById('btn-cancel-edit').classList.add('hidden');
    }
</script>
@endsection
