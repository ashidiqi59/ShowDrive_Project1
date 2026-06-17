@extends('layouts.app')

@section('title', $car->brand . ' ' . $car->model . ' - ShowDrive')

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
                <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                    <img id="dt-thumb-1" src="{{ $car->image }}" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb">
                </button>
                <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb">
                </button>
                <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                    <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb">
                </button>
                <button onclick="changeDetailImage(this.children[0].src)" class="border border-zinc-800 focus:border-luxury-gold overflow-hidden h-20 bg-zinc-950">
                    <img src="https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover opacity-65 hover:opacity-100 transition-all" alt="Thumb">
                </button>
            </div>
        </div>

        <!-- Specs Sheet -->
        <div class="flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span id="dt-year" class="text-zinc-500 font-bold tracking-[0.2em] text-xs">{{ $car->year }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-luxury-gold"></span>
                    <span id="dt-brand" class="text-luxury-gold font-bold tracking-[0.2em] text-xs">{{ strtoupper($car->brand) }}</span>
                </div>
                <h2 id="dt-model" class="text-3xl md:text-5xl font-extrabold mb-4 uppercase tracking-wide">{{ $car->brand }} {{ $car->model }}</h2>
                <h3 id="dt-price" class="text-xl md:text-2xl font-light text-zinc-300 tracking-wider mb-8">IDR {{ number_format($car->price, 0, ',', '.') }}</h3>

                <div class="border-t border-zinc-900 pt-6 mt-6 space-y-4">
                    <h4 class="font-bold text-xs tracking-[0.25em] text-zinc-300 uppercase mb-4">SPESIFIKASI DATA UNIT</h4>
                    
                    <div class="grid grid-cols-2 gap-y-6 border-b border-zinc-900 pb-6 text-xs tracking-wider">
                        <div>
                            <span class="text-zinc-500 block mb-1">NOMOR RANGKA (VIN)</span>
                            <span id="dt-vin" class="font-bold text-white font-mono text-sm tracking-widest text-glow">{{ $car->vin }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">KAPASITAS MESIN</span>
                            <span id="dt-engine" class="font-semibold text-zinc-200">{{ $car->engine }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">TRANSMISI</span>
                            <span id="dt-trans" class="font-semibold text-zinc-200">{{ $car->transmission }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block mb-1">WARNA BODY</span>
                            <span id="dt-color" class="font-semibold text-zinc-200">{{ $car->color }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="mt-8 space-y-4">
                @if($car->status === 'Available')
                    <button onclick="triggerBookingModal()" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-4.5 text-xs tracking-[0.2em] transition-all">
                        JADWALKAN PERTEMUAN (INQUIRE)
                    </button>
                @elseif($car->status === 'Booked')
                    <button disabled class="w-full bg-zinc-800 text-zinc-500 font-extrabold py-4.5 text-xs tracking-[0.2em] cursor-not-allowed">
                        TERPESAN (BOOKED)
                    </button>
                @else
                    <button disabled class="w-full bg-zinc-800 text-zinc-500 font-extrabold py-4.5 text-xs tracking-[0.2em] cursor-not-allowed">
                        TERJUAL (SOLD)
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- INTERACTIVE MODAL: BOOKING FORM -->
<div id="booking-modal" class="no-print fixed inset-0 bg-black/95 backdrop-blur-sm flex items-center justify-center p-4 z-[100] hidden">
    <div class="bg-zinc-950 border border-zinc-800 w-full max-w-md p-8 relative">
        <button onclick="closeBookingModal()" class="absolute top-4 right-4 text-zinc-500 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        <h4 class="text-xl font-bold mb-1 tracking-wider uppercase">RESERVASI JADWAL TEMU</h4>
        <p class="text-zinc-500 text-[10px] uppercase tracking-wider mb-6">Merekondisi ketersediaan unit ke tabel booking</p>
        
        <form action="{{ route('booking.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="car_id" value="{{ $car->id }}">
            
            <div>
                <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Nama Lengkap Pemohon</label>
                <input type="text" name="customer_name" placeholder="Contoh: Aris Ashidiqi" class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold focus:outline-none text-white p-3" required>
            </div>
            <div>
                <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Nomor Kontak WhatsApp</label>
                <input type="tel" name="phone" placeholder="Contoh: 08123456789" class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold focus:outline-none text-white p-3" required>
            </div>
            <div>
                <label class="block text-zinc-500 font-bold uppercase tracking-wider mb-1">Pilih Tanggal Rencana Inspeksi</label>
                <input type="date" name="date" class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold focus:outline-none text-zinc-300 p-3" required>
            </div>
            
            <div class="bg-zinc-900/40 p-3 border border-zinc-800 text-[10px] text-zinc-500 leading-normal">
                <i class="fa-solid fa-lock text-luxury-gold mr-1"></i> Data disimpan aman dalam tabel transaksi <strong>'bookings'</strong> dengan relasi Foreign Key ke tabel master mobil.
            </div>

            <button type="submit" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-4 text-xs tracking-widest uppercase transition-colors mt-6">
                KIRIM PERMINTAAN TEMU
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeDetailImage(src) {
        document.getElementById('dt-main-img').src = src;
    }

    function triggerBookingModal() {
        document.getElementById('booking-modal').classList.remove('hidden');
    }

    function closeBookingModal() {
        document.getElementById('booking-modal').classList.add('hidden');
    }
</script>
@endsection
