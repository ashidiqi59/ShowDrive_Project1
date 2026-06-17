@extends('layouts.app')

@section('title', 'Cek Status & Pembayaran Pelanggan - ShowDrive')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 no-print">
    <div class="text-center mb-10">
        <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-1">PELAYANAN MANDIRI</span>
        <h2 class="text-3xl font-black tracking-wider">CEK RESERVASI & PEMBAYARAN</h2>
        <div class="w-12 h-[2px] bg-luxury-gold mx-auto mt-3"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Form Pencarian Booking -->
        <div class="bg-zinc-950 border border-zinc-900 p-6 h-fit">
            <h3 class="font-bold text-xs tracking-wider uppercase text-zinc-300 mb-4"><i class="fa-solid fa-search text-luxury-gold mr-1.5"></i> Lacak Nomor HP</h3>
            <form action="{{ route('booking.track') }}" method="GET" class="space-y-4">
                <p class="text-[11px] text-zinc-500">Masukkan No. WhatsApp yang didaftarkan untuk mengunggah bukti pembayaran atau mencetak Kwitansi.</p>
                <input type="tel" name="phone" value="{{ $phone }}" placeholder="Contoh: 08112233445" class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold text-white p-3 text-xs focus:outline-none" required>
                <button type="submit" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-bold py-3 text-xs tracking-wider transition-all">
                    CARI RESERVASI
                </button>
            </form>
        </div>

        <!-- Hasil Pencarian & Unggah Bukti Bayar -->
        <div class="md:col-span-2 space-y-6">
            @if(!$phone)
                <div id="tracking-result-placeholder" class="bg-zinc-950 border border-zinc-900 p-8 text-center text-zinc-500 italic text-xs">
                    Silakan cari berdasarkan nomor kontak di panel kiri untuk memuat data.
                </div>
            @else
                @forelse($bookings as $booking)
                    @php
                        $car = $booking->car;
                        
                        $badgeColor = "bg-amber-950/60 text-amber-400 border-amber-900/50";
                        if ($booking->status === 'Approved') {
                            $badgeColor = "bg-emerald-950/60 text-emerald-400 border-emerald-900/50";
                        } elseif ($booking->status === 'Rejected') {
                            $badgeColor = "bg-red-950/60 text-red-400 border-red-900/50";
                        }

                        $payStatusColor = "bg-red-950/60 text-red-400 border-red-900/50";
                        if ($booking->payment_status === 'Down Payment') {
                            $payStatusColor = "bg-amber-950/60 text-amber-400 border-amber-900/50";
                        } elseif ($booking->payment_status === 'Paid') {
                            $payStatusColor = "bg-emerald-950/60 text-emerald-400 border-emerald-900/50";
                        }
                    @endphp

                    <div class="bg-zinc-950 border border-zinc-900 p-6 space-y-4">
                        <div class="flex justify-between items-start border-b border-zinc-900 pb-3">
                            <div>
                                <h4 class="text-sm font-black text-white uppercase">{{ $booking->customer_name }}</h4>
                                <p class="text-[10px] text-zinc-500 mt-0.5">Kode Booking: SD-BK-00{{ $booking->id }}</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 border text-[9px] font-bold uppercase {{ $badgeColor }}">Status Inspeksi: {{ $booking->status }}</span>
                                <span class="px-2 py-0.5 border text-[9px] font-bold uppercase {{ $payStatusColor }}">Status Bayar: {{ $booking->payment_status }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs leading-relaxed">
                            <div>
                                <span class="text-zinc-500 block mb-0.5 uppercase text-[9px]">Model Mobil:</span>
                                <span class="font-extrabold text-zinc-200 block">{{ $car->brand }} {{ $car->model }}</span>
                                <span class="text-zinc-500 block font-mono text-[10px] mt-1">Harga: IDR {{ number_format($car->price, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 block mb-0.5 uppercase text-[9px]">Rencana Jadwal Temu:</span>
                                <span class="font-bold text-zinc-200 block"><i class="fa-regular fa-calendar mr-1 text-luxury-gold"></i>{{ $booking->date }}</span>
                                <span class="text-zinc-500 block text-[10px] mt-1">Telah Ditransfer: <strong class="text-emerald-400 font-mono">IDR {{ number_format($booking->paid_amount, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>

                        @if($booking->payment_status !== 'Paid')
                            <div class="border-t border-zinc-900 pt-4 mt-4 bg-zinc-900/10 p-4 border border-zinc-900">
                                <h4 class="font-extrabold text-[10px] tracking-wider text-luxury-gold uppercase mb-3">KIRIM DOKUMEN BUKTI PEMBAYARAN MANUAL</h4>
                                <form action="{{ route('booking.upload_proof', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-3 text-[11px]">
                                        <div>
                                            <label class="block text-zinc-500 font-bold mb-1 uppercase">Tipe Pembayaran</label>
                                            <select name="payment_type" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 p-2 focus:outline-none focus:border-luxury-gold">
                                                <option value="Down Payment">Uang Muka / DP (IDR 50.000.000)</option>
                                                <option value="Paid">Pelunasan Penuh (IDR {{ number_format($car->price, 0, '', '') }})</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-zinc-500 font-bold mb-1 uppercase">Nominal Ditransfer (IDR)</label>
                                            <input type="number" name="paid_amount" placeholder="Contoh: 50000000" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 p-2 focus:outline-none focus:border-luxury-gold" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-zinc-500 font-bold mb-1 uppercase text-[11px]">Pilih File Bukti Transfer</label>
                                        <input type="file" name="payment_proof" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:border file:border-zinc-800 file:bg-zinc-900 file:text-zinc-300 file:text-xs focus:outline-none" required>
                                    </div>
                                    <button type="submit" class="w-full bg-luxury-gold text-black font-bold py-2 text-xs uppercase tracking-wider transition-colors mt-2">
                                        UNGGAH BUKTI & KIRIM
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-emerald-950/20 border border-emerald-900/30 p-4 text-[11px] text-emerald-400 flex justify-between items-center">
                                <span><i class="fa-solid fa-circle-check mr-2"></i> Pembayaran Lunas & Diverifikasi</span>
                                <a href="{{ route('booking.invoice', $booking->id) }}" class="bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-1.5 px-3 text-[10px] uppercase tracking-wider rounded-sm">
                                    <i class="fa-solid fa-file-invoice mr-1"></i> Cetak Kwitansi
                                </a>
                            </div>
                        @endif

                        @if($booking->payment_status === 'Down Payment')
                            <div class="mt-2 text-right">
                                <a href="{{ route('booking.invoice', $booking->id) }}" class="border border-luxury-gold text-luxury-gold hover:bg-luxury-gold/10 font-bold py-1 px-3 text-[10px] uppercase tracking-wider rounded-sm inline-block">
                                    <i class="fa-solid fa-file-invoice mr-1"></i> Cetak Invoice DP
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-zinc-950 border border-zinc-900 p-8 text-center text-zinc-500 italic text-xs">
                        Data tidak ditemukan. Pastikan nomor WhatsApp yang Anda masukkan sesuai.
                    </div>
                @endforelse
            @endif
        </div>
    </div>
</div>
@endsection
