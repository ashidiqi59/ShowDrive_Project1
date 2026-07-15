@extends('layouts.admin')

@section('title', 'Laporan Keuangan - ShowDrive Admin')

@section('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; color: #000 !important; }
        .print-only { display: block !important; }
        table { font-size: 10px !important; }
    }
    .print-only { display: none; }
</style>
@endsection

@section('content')
<div class="space-y-8">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-8 border-b border-zinc-900 gap-4 no-print">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-zinc-500 hover:text-luxury-gold transition-colors text-xs tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-1"></i> KEMBALI KE DASHBOARD
                </a>
            </div>
            <h1 class="text-3xl font-black tracking-wider mt-2">LAPORAN KEUANGAN</h1>
            <p class="text-zinc-500 text-xs mt-1">
                {{ $company->name ?? 'ShowDrive Showroom' }} &mdash;
                Periode: {{ $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan' }} {{ $tahun }}
            </p>
        </div>
        <button onclick="window.print()" class="bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold px-5 py-2.5 text-[10px] tracking-[0.2em] uppercase flex items-center gap-2 transition-all no-print">
            <i class="fa-solid fa-print"></i> CETAK / UNDUH PDF
        </button>
    </div>

    {{-- ===== FILTER PANEL ===== --}}
    <div class="bg-zinc-950 border border-zinc-900 p-5 no-print">
        <h3 class="text-xs font-bold tracking-[0.2em] text-zinc-400 uppercase mb-4">
            <i class="fa-solid fa-filter text-luxury-gold mr-1.5"></i>FILTER LAPORAN
        </h3>
        <form action="{{ route('admin.laporan') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex flex-col gap-1.5">
                <label class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Bulan</label>
                <select name="bulan" class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-2 focus:border-luxury-gold focus:outline-none">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Tahun</label>
                <select name="tahun" class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-2 focus:border-luxury-gold focus:outline-none">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Status Pembayaran</label>
                <select name="status" class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs px-3 py-2 focus:border-luxury-gold focus:outline-none">
                    <option value="all"                {{ $status === 'all'                ? 'selected' : '' }}>Semua Status</option>
                    <option value="Paid"               {{ $status === 'Paid'               ? 'selected' : '' }}>Paid (Lunas)</option>
                    <option value="Down Payment"       {{ $status === 'Down Payment'       ? 'selected' : '' }}>Down Payment</option>
                    <option value="Pending Validation" {{ $status === 'Pending Validation' ? 'selected' : '' }}>Pending Validation</option>
                    <option value="Unpaid"             {{ $status === 'Unpaid'             ? 'selected' : '' }}>Unpaid</option>
                    <option value="Cancelled"          {{ $status === 'Cancelled'          ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                </select>
            </div>
            <button type="submit" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold px-4 py-2 text-xs tracking-wider uppercase transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-magnifying-glass text-[10px]"></i> TERAPKAN
            </button>
            <a href="{{ route('admin.laporan') }}" class="text-zinc-500 hover:text-white text-xs py-2 px-3 border border-zinc-800 hover:border-zinc-600 transition-colors">
                Reset
            </a>
        </form>
    </div>

    {{-- ===== 5 KARTU STATISTIK ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 no-print">
        <div class="bg-zinc-950 border border-zinc-900 p-4 hover:border-zinc-700 transition-all">
            <p class="text-zinc-500 text-[9px] uppercase font-bold tracking-wider mb-2">Total Transaksi</p>
            <span class="text-2xl font-black font-mono text-white">{{ $totalTransaksi }}</span>
            <p class="text-zinc-600 text-[9px] mt-1">Semua Invoice</p>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-4 hover:border-red-900/50 transition-all">
            <p class="text-zinc-500 text-[9px] uppercase font-bold tracking-wider mb-2">Unit Terjual</p>
            <span class="text-2xl font-black font-mono text-red-400">{{ $unitTerjual }}</span>
            <p class="text-zinc-600 text-[9px] mt-1">Status Paid</p>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-4 hover:border-emerald-900/50 transition-all">
            <p class="text-zinc-500 text-[9px] uppercase font-bold tracking-wider mb-2">Total Pendapatan</p>
            <span class="text-sm font-black font-mono text-emerald-400">IDR {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            <p class="text-zinc-600 text-[9px] mt-1">Dari Invoice Lunas</p>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-4 hover:border-blue-900/50 transition-all">
            <p class="text-zinc-500 text-[9px] uppercase font-bold tracking-wider mb-2">Total DP Masuk</p>
            <span class="text-sm font-black font-mono text-blue-400">IDR {{ number_format($totalDP, 0, ',', '.') }}</span>
            <p class="text-zinc-600 text-[9px] mt-1">Uang Muka Aktif</p>
        </div>
        <div class="bg-zinc-950 border border-zinc-900 p-4 hover:border-amber-900/50 transition-all">
            <p class="text-zinc-500 text-[9px] uppercase font-bold tracking-wider mb-2">Pending Verifikasi</p>
            <span class="text-2xl font-black font-mono {{ $totalPending > 0 ? 'text-amber-400' : 'text-zinc-400' }}">{{ $totalPending }}</span>
            <p class="text-zinc-600 text-[9px] mt-1">Menunggu Kasir</p>
        </div>
    </div>

    {{-- ===== TABEL LAPORAN ===== --}}
    <div class="bg-zinc-950 border border-zinc-900 p-6">
        <div class="flex justify-between items-center mb-5 border-b border-zinc-900 pb-3">
            <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">
                <i class="fa-solid fa-table-list text-luxury-gold mr-1.5"></i>RINCIAN TRANSAKSI
            </h3>
            <span class="text-[9px] text-zinc-500 font-mono">Total: {{ $invoices->total() }} baris data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="laporan-table">
                <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                    <tr>
                        <th class="p-3">No.</th>
                        <th class="p-3">Kode Invoice</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Unit Kendaraan</th>
                        <th class="p-3">Tipe Bayar</th>
                        <th class="p-3">Nominal (IDR)</th>
                        <th class="p-3">Status Bayar</th>
                        <th class="p-3">Status Inspeksi</th>
                        <th class="p-3">Disahkan Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @forelse($invoices as $i => $inv)
                        @php
                            $payBadge = 'text-zinc-400';
                            if ($inv->payment_status === 'Paid')               $payBadge = 'text-emerald-400 font-bold';
                            elseif ($inv->payment_status === 'Down Payment')    $payBadge = 'text-blue-400 font-bold';
                            elseif ($inv->payment_status === 'Pending Validation') $payBadge = 'text-amber-400 font-bold';
                        @endphp
                        <tr class="hover:bg-zinc-900/30 transition-colors">
                            <td class="p-3 text-zinc-600 font-mono">{{ $invoices->firstItem() + $loop->index }}</td>
                            <td class="p-3 font-mono text-luxury-gold/80 text-[10px] font-semibold">{{ $inv->invoice_code }}</td>
                            <td class="p-3 text-zinc-400 text-[10px] font-mono">{{ \Carbon\Carbon::parse($inv->created_at)->format('d/m/Y') }}</td>
                            <td class="p-3">
                                {{-- Fix #5: gunakan relasi customer, bukan accessor lama --}}
                                <span class="font-bold text-zinc-200 block">{{ $inv->customer?->name ?? '—' }}</span>
                                <span class="text-zinc-500 text-[10px]">{{ $inv->customer?->phone ?? '—' }}</span>
                            </td>
                            <td class="p-3 text-zinc-400">
                                {{-- Fix #5: gunakan relasi item, bukan alias car --}}
                                {{ $inv->item?->brand }} {{ $inv->item?->model }}
                            </td>
                            <td class="p-3 text-zinc-400 text-[10px]">{{ $inv->payment_type }}</td>
                            <td class="p-3 font-mono font-bold {{ $inv->payment_status === 'Paid' ? 'text-emerald-400' : 'text-zinc-400' }}">
                                IDR {{ number_format($inv->paid_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 {{ $payBadge }} text-[10px] uppercase">{{ $inv->payment_status }}</td>
                            <td class="p-3 text-[10px] {{ $inv->status === 'Approved' ? 'text-emerald-400' : ($inv->status === 'Rejected' ? 'text-red-400' : 'text-amber-400') }} uppercase font-bold">
                                {{ $inv->status }}
                            </td>
                            <td class="p-3 text-zinc-500 text-[10px]">{{ $inv->cashier?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-10 text-center text-zinc-500 italic text-xs">
                                <i class="fa-solid fa-inbox text-2xl mb-2 block text-zinc-700"></i>
                                Tidak ada data transaksi untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($invoices->count() > 0)
                <tfoot class="border-t-2 border-zinc-700 bg-zinc-900/30">
                    <tr>
                        <td colspan="6" class="p-3 text-right text-zinc-400 text-[10px] font-bold uppercase tracking-wider">Total Penerimaan (Lunas):</td>
                        <td class="p-3 font-black font-mono text-emerald-400 text-sm">IDR {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Pagination laporan --}}
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-zinc-900 flex flex-col sm:flex-row items-center justify-between gap-3 no-print">
                <p class="text-zinc-600 text-[10px] font-mono">
                    Menampilkan {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} dari {{ $invoices->total() }} transaksi
                </p>
                <nav class="flex items-center gap-1">
                    @if($invoices->onFirstPage())
                        <span class="px-3 py-1.5 text-[10px] font-bold text-zinc-700 border border-zinc-900 cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $invoices->previousPageUrl() }}"
                           class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach($invoices->getUrlRange(1, $invoices->lastPage()) as $page => $url)
                        @if($page == $invoices->currentPage())
                            <span class="px-3 py-1.5 text-[10px] font-bold bg-luxury-gold text-black">{{ $page }}</span>
                        @elseif(abs($page - $invoices->currentPage()) <= 2)
                            <a href="{{ $url }}" class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">{{ $page }}</a>
                        @elseif(abs($page - $invoices->currentPage()) == 3)
                            <span class="px-2 py-1.5 text-[10px] text-zinc-700">...</span>
                        @endif
                    @endforeach

                    @if($invoices->hasMorePages())
                        <a href="{{ $invoices->nextPageUrl() }}"
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

    {{-- ===== PRINT-ONLY HEADER (hanya muncul saat cetak/PDF) ===== --}}
    <div class="print-only px-8 py-6">
        <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-black tracking-widest text-black">SHOW<span style="color:#D4AF37">DRIVE</span></h1>
                <p class="text-xs text-gray-600 mt-1">{{ $company->name ?? 'Exclusive Luxury Showroom' }}</p>
                @if($company?->address) <p class="text-xs text-gray-500">{{ $company->address }}</p> @endif
                @if($company?->phone)   <p class="text-xs text-gray-500">Tel: {{ $company->phone }}</p> @endif
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-black">LAPORAN KEUANGAN</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Periode: {{ $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan' }} {{ $tahun }}
                </p>
                <p class="text-xs text-gray-500">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
                @if($company?->tax_id) <p class="text-xs text-gray-500">NPWP: {{ $company->tax_id }}</p> @endif
            </div>
        </div>
    </div>

</div>{{-- end .space-y-8 --}}
@endsection
