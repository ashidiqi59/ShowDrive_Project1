@extends('layouts.app')

@section('title', 'Booking Berhasil — ' . $invoice->invoice_code . ' | ShowDrive')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-14">

    {{-- ── SUCCESS BADGE ── --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-900/40 border-2 border-emerald-500/60 mb-5">
            <i class="fa-solid fa-circle-check text-emerald-400 text-3xl"></i>
        </div>
        <div class="inline-flex items-center gap-2 bg-emerald-900/30 border border-emerald-700/50 text-emerald-400 font-black text-xs tracking-[0.3em] uppercase px-5 py-2 mb-4">
            <i class="fa-solid fa-check"></i> BOOKING BERHASIL
        </div>
        <h1 class="text-2xl md:text-3xl font-black tracking-wider text-white mt-3">
            Reservasi Anda Telah Diterima
        </h1>
        <p class="text-zinc-500 text-sm mt-2 max-w-md mx-auto">
            Simpan nomor invoice berikut. Anda akan memerlukannya untuk melacak status dan mengunggah bukti pembayaran.
        </p>
        <div class="w-12 h-[2px] bg-emerald-500 mx-auto mt-5"></div>
    </div>

    {{-- ── INVOICE CODE CARD ── --}}
    <div class="bg-zinc-950 border border-emerald-900/50 p-6 md:p-8 mb-6 text-center relative overflow-hidden">
        {{-- subtle glow --}}
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-950/20 to-transparent pointer-events-none"></div>
        <p class="text-[10px] font-bold tracking-[0.35em] text-zinc-500 uppercase mb-2">Nomor Invoice</p>
        <p class="font-mono text-3xl md:text-4xl font-black text-white tracking-[0.15em] text-glow">
            {{ $invoice->invoice_code }}
        </p>
        <p class="text-zinc-600 text-[10px] mt-2 font-mono">
            Dibuat: {{ $invoice->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY — HH:mm') }} WIB
        </p>
    </div>

    {{-- ── BOOKING SUMMARY GRID ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        {{-- Unit --}}
        <div class="bg-zinc-950 border border-zinc-800 p-5">
            <p class="text-[10px] font-bold tracking-[0.25em] text-zinc-500 uppercase mb-3 flex items-center gap-2">
                <i class="fa-solid fa-car text-luxury-gold"></i> Unit yang Dipesan
            </p>
            <p class="text-white font-bold text-sm">
                {{ $invoice->item->brand }} {{ $invoice->item->model }}
            </p>
            <p class="text-zinc-400 text-xs mt-1">
                Tahun {{ $invoice->item->year }}
            </p>
            @if($invoice->item->vin)
                <p class="text-zinc-600 text-[10px] font-mono mt-1">VIN: {{ $invoice->item->vin }}</p>
            @endif
        </div>

        {{-- Pelanggan --}}
        <div class="bg-zinc-950 border border-zinc-800 p-5">
            <p class="text-[10px] font-bold tracking-[0.25em] text-zinc-500 uppercase mb-3 flex items-center gap-2">
                <i class="fa-solid fa-user text-luxury-gold"></i> Data Pelanggan
            </p>
            <p class="text-white font-bold text-sm">{{ $invoice->customer->name }}</p>
            <p class="text-zinc-400 text-xs mt-1">
                <i class="fa-solid fa-phone text-zinc-600 mr-1"></i>{{ $invoice->customer->phone }}
            </p>
            @if($invoice->customer->nik)
                <p class="text-zinc-600 text-[10px] font-mono mt-1">NIK: {{ $invoice->customer->nik }}</p>
            @endif
        </div>

        {{-- Pembayaran --}}
        <div class="bg-zinc-950 border border-zinc-800 p-5 md:col-span-2">
            <p class="text-[10px] font-bold tracking-[0.25em] text-zinc-500 uppercase mb-4 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-luxury-gold"></i> Rincian Pembayaran
            </p>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-400 text-xs">Tipe Pembayaran</span>
                    <span class="font-semibold text-white text-xs tracking-wide">{{ $invoice->payment_type }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-400 text-xs">Subtotal (Harga Unit)</span>
                    <span class="text-zinc-300 text-xs font-mono">
                        Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-400 text-xs">
                        PPN {{ number_format($invoice->tax_rate, 0) }}%
                        <span class="text-zinc-600 text-[10px] ml-1">(Perpajakan RI)</span>
                    </span>
                    <span class="text-zinc-300 text-xs font-mono">
                        + Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="border-t border-zinc-800 pt-2.5 flex justify-between items-center">
                    <span class="text-white font-bold text-xs tracking-wide">
                        @if($invoice->payment_type === 'Down Payment')
                            Jumlah DP yang Harus Dibayar
                        @else
                            Total yang Harus Dibayar (Lunas)
                        @endif
                    </span>
                    <span class="text-luxury-gold font-black font-mono text-base">
                        @if($invoice->payment_type === 'Down Payment')
                            {{-- Hitung nominal DP: dp_percentage dari harga unit (belum termasuk PPN) --}}
                            Rp {{ number_format(round($invoice->item->price * ($invoice->item->dp_percentage / 100)), 0, ',', '.') }}
                            <span class="text-zinc-500 text-[10px] font-sans ml-1">({{ $invoice->item->dp_percentage }}% dari harga)</span>
                        @else
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
                @if($invoice->payment_type === 'Down Payment')
                    <div class="flex justify-between items-center text-zinc-600 text-[10px]">
                        <span>Total keseluruhan (setelah pelunasan)</span>
                        <span class="font-mono">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── LANGKAH SELANJUTNYA ── --}}
    <div class="bg-zinc-950 border border-zinc-800 p-6 mb-8">
        <p class="text-[10px] font-bold tracking-[0.25em] text-zinc-500 uppercase mb-5 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-luxury-gold"></i> Langkah Selanjutnya
        </p>
        <ol class="space-y-4">
            <li class="flex items-start gap-4">
                <div class="w-7 h-7 rounded-full bg-luxury-gold/20 border border-luxury-gold/50 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="text-luxury-gold font-black text-[10px]">1</span>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Lakukan Transfer Pembayaran</p>
                    <p class="text-zinc-500 text-[11px] mt-0.5 leading-relaxed">
                        Transfer sejumlah nominal yang tertera ke rekening showroom. Informasi rekening tujuan dapat dilihat di halaman <strong class="text-zinc-400">Lacak Reservasi</strong>.
                    </p>
                </div>
            </li>
            <li class="flex items-start gap-4">
                <div class="w-7 h-7 rounded-full bg-luxury-gold/20 border border-luxury-gold/50 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="text-luxury-gold font-black text-[10px]">2</span>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Unggah Bukti Transfer</p>
                    <p class="text-zinc-500 text-[11px] mt-0.5 leading-relaxed">
                        Di halaman <strong class="text-zinc-400">Lacak Reservasi</strong>, masuk menggunakan nomor HP Anda, lalu unggah foto bukti transfer untuk diverifikasi oleh tim admin kami.
                    </p>
                </div>
            </li>
            <li class="flex items-start gap-4">
                <div class="w-7 h-7 rounded-full bg-luxury-gold/20 border border-luxury-gold/50 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="text-luxury-gold font-black text-[10px]">3</span>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Tunggu Konfirmasi Admin</p>
                    <p class="text-zinc-500 text-[11px] mt-0.5 leading-relaxed">
                        Tim kami akan memverifikasi pembayaran Anda dalam waktu 1×24 jam kerja. Status reservasi akan diperbarui secara otomatis.
                    </p>
                </div>
            </li>
            <li class="flex items-start gap-4">
                <div class="w-7 h-7 rounded-full bg-emerald-900/50 border border-emerald-700/50 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="text-emerald-400 font-black text-[10px]">4</span>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Serah Terima Kendaraan</p>
                    <p class="text-zinc-500 text-[11px] mt-0.5 leading-relaxed">
                        Setelah pembayaran terverifikasi dan proses inspeksi selesai, Anda akan dihubungi untuk jadwal serah terima kendaraan.
                    </p>
                </div>
            </li>
        </ol>
    </div>

    {{-- ── CTA BUTTONS ── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('booking.track') }}"
           class="flex-1 flex items-center justify-center gap-2.5 bg-luxury-gold hover:bg-luxury-goldHover text-black font-black text-xs tracking-[0.2em] uppercase py-4 transition-all duration-200 text-center">
            <i class="fa-solid fa-receipt text-sm"></i>
            Lacak Reservasi Saya
        </a>
        <a href="{{ route('home') }}"
           class="flex-1 flex items-center justify-center gap-2.5 border border-zinc-700 text-zinc-400 hover:border-zinc-500 hover:text-white font-bold text-xs tracking-[0.2em] uppercase py-4 transition-all duration-200 text-center">
            <i class="fa-solid fa-car text-sm"></i>
            Lihat Katalog
        </a>
    </div>

    {{-- ── FOOTER NOTE ── --}}
    <p class="text-center text-zinc-700 text-[10px] mt-8 tracking-wider">
        Butuh bantuan? Hubungi tim ShowDrive melalui kontak yang tertera di halaman utama.
    </p>

</div>
@endsection
