@extends('layouts.admin')

@section('title', 'Validasi Transaksi & Inspeksi')

@section('content')
<div class="space-y-8">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">FINANCIAL GATEWAY</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">VALIDASI TRANSAKSI</h2>
            <p class="text-zinc-500 text-xs mt-1">Verifikasi pembayaran dan setujui jadwal inspeksi dalam satu aksi.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @php
                $pendingCount  = $summaryPending  ?? 0;
                $verifiedCount = $summaryVerified ?? 0;
                $paidCount     = $summaryPaid     ?? 0;
            @endphp
            <div class="bg-amber-950/40 border border-amber-900/40 px-4 py-2 text-center">
                <span class="text-[18px] font-black font-mono text-amber-400">{{ $pendingCount }}</span>
                <p class="text-[9px] text-amber-600 font-bold uppercase tracking-wider">Pending</p>
            </div>
            <div class="bg-blue-950/40 border border-blue-900/40 px-4 py-2 text-center">
                <span class="text-[18px] font-black font-mono text-blue-400">{{ $verifiedCount }}</span>
                <p class="text-[9px] text-blue-600 font-bold uppercase tracking-wider">Down Payment</p>
            </div>
            <div class="bg-emerald-950/40 border border-emerald-900/40 px-4 py-2 text-center">
                <span class="text-[18px] font-black font-mono text-emerald-400">{{ $paidCount }}</span>
                <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider">Lunas</p>
            </div>
        </div>
    </div>

    {{-- FILTER STATUS TABS --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['all' => 'Semua Transaksi', 'pending' => 'Pending Validasi', 'verified' => 'Down Payment', 'paid' => 'Lunas', 'Cancelled' => 'Dibatalkan'] as $key => $label)
            <a href="{{ route('admin.invoices', ['status_filter' => $key]) }}"
               class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider border transition-all
                   {{ $statusFilter === $key
                       ? 'bg-luxury-gold text-black border-luxury-gold'
                       : 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:border-zinc-600 hover:text-white' }}">
                {{ $label }}
                @if($key === 'pending' && $pendingCount > 0)
                    <span class="ml-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[9px] px-1.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- TABEL LOG TRANSAKSI --}}
    <div class="bg-zinc-950 border border-zinc-900 shadow-2xl">
        <div class="px-6 py-4 border-b border-zinc-900 flex items-center gap-2">
            <div class="w-1 h-5 bg-emerald-500"></div>
            <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">LOG TRANSAKSI & PEMBAYARAN</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-zinc-900/60 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                    <tr>
                        <th class="p-4">PELANGGAN</th>
                        <th class="p-4">UNIT KENDARAAN</th>
                        <th class="p-4">STATUS BAYAR & INSPEKSI</th>
                        <th class="p-4">NOMINAL (IDR)</th>
                        <th class="p-4">BUKTI TRANSFER</th>
                        <th class="p-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900" id="invoices-table-body">
                    @forelse($bookings as $bk)
                        @php
                            $payBadge = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                            if ($bk->payment_status === 'Down Payment')   $payBadge = 'bg-blue-950/60 text-blue-400 border-blue-900/50';
                            if ($bk->payment_status === 'Paid')           $payBadge = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';
                            if ($bk->payment_status === 'Cancelled')      $payBadge = 'bg-zinc-900 text-zinc-500 border-zinc-700';
                            if ($bk->payment_status === 'Unpaid')         $payBadge = 'bg-amber-950/60 text-amber-400 border-amber-900/50';

                            $inspBadge = 'bg-amber-950/40 text-amber-500 border-amber-900/40';
                            if ($bk->status === 'Approved')  $inspBadge = 'bg-emerald-950/40 text-emerald-500 border-emerald-900/40';
                            if ($bk->status === 'Rejected')  $inspBadge = 'bg-red-950/40 text-red-400 border-red-900/40';
                            if ($bk->status === 'Cancelled') $inspBadge = 'bg-zinc-900 text-zinc-500 border-zinc-700';
                        @endphp
                        <tr class="hover:bg-zinc-900/25 transition-colors" id="invoice-row-{{ $bk->id }}">

                            {{-- Pelanggan --}}
                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 flex-shrink-0 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-luxury-gold font-black text-xs">
                                        {{ strtoupper(substr($bk->customer?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block">{{ $bk->customer?->name ?? '—' }}</span>
                                        <span class="text-[9px] text-zinc-500">
                                            <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>{{ $bk->customer?->phone ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Unit --}}
                            <td class="p-4">
                                <span class="font-bold text-zinc-200 block">{{ $bk->item?->brand }} {{ $bk->item?->model }}</span>
                                <span class="text-[9px] text-zinc-600 font-mono">{{ $bk->item?->vin }}</span>
                            </td>

                            {{-- Status Bayar + Inspeksi --}}
                            <td class="p-4">
                                <div class="flex flex-col gap-1.5">
                                    {{-- Badge payment_status --}}
                                    <span class="px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $payBadge }}"
                                          id="pay-badge-{{ $bk->id }}">{{ $bk->payment_status }}</span>
                                    {{-- Badge status inspeksi --}}
                                    <span class="px-2.5 py-0.5 border rounded-full text-[9px] font-bold uppercase tracking-wider {{ $inspBadge }}"
                                          id="insp-badge-{{ $bk->id }}">
                                        <i class="fa-solid fa-calendar-check text-[8px] mr-0.5"></i>
                                        {{ $bk->status }}
                                    </span>
                                    <span class="text-[9px] text-zinc-600 uppercase font-bold">{{ $bk->payment_type ?? '—' }}</span>
                                    <span class="text-[9px] text-zinc-500 font-mono flex items-center gap-1">
                                        <i class="fa-regular fa-calendar text-luxury-gold/70"></i>
                                        {{ $bk->date?->translatedFormat('d M Y') ?? '—' }}
                                        @if($bk->date && $bk->date->format('H:i') !== '00:00')
                                            <i class="fa-regular fa-clock text-luxury-gold/70 ml-1"></i>
                                            {{ $bk->date->format('H:i') }} WIB
                                        @endif
                                    </span>
                                    {{-- Catatan penolakan admin --}}
                                    @if($bk->rejection_note)
                                        <div class="text-[9px] text-red-400 max-w-[220px]">
                                            <i class="fa-solid fa-circle-xmark mr-0.5"></i>
                                            <span class="font-bold not-italic">Admin:</span>
                                            <span class="italic note-text overflow-hidden" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $bk->rejection_note }}</span>
                                            @if(strlen($bk->rejection_note) > 80)
                                                <button onclick="toggleNote(this)" class="text-red-300 underline not-italic font-bold ml-0.5 hover:text-white transition-colors" data-expanded="0">Lihat semua</button>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- Catatan pembatalan pelanggan --}}
                                    @if($bk->cancellation_note)
                                        <div class="text-[9px] text-orange-400 max-w-[220px]">
                                            <i class="fa-solid fa-ban mr-0.5"></i>
                                            <span class="font-bold not-italic">Pelanggan:</span>
                                            <span class="italic note-text overflow-hidden" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $bk->cancellation_note }}</span>
                                            @if(strlen($bk->cancellation_note) > 80)
                                                <button onclick="toggleNote(this)" class="text-orange-300 underline not-italic font-bold ml-0.5 hover:text-white transition-colors" data-expanded="0">Lihat semua</button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Nominal --}}
                            <td class="p-4 font-black font-mono text-emerald-400">
                                IDR {{ number_format($bk->paid_amount ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Bukti --}}
                            <td class="p-4">
                                @if($bk->authentic_receipt)
                                    <div class="flex flex-col gap-1">
                                        <span class="text-emerald-400 text-[9px] flex items-center gap-1">
                                            <i class="fa-regular fa-image"></i> Ada Berkas
                                        </span>
                                        <a href="{{ asset('storage/' . $bk->authentic_receipt) }}" target="_blank"
                                           class="underline text-luxury-gold hover:text-white text-[9px] transition-colors">
                                            Lihat <i class="fa-solid fa-external-link-alt text-[8px]"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-zinc-600 italic text-[9px]">Belum ada bukti</span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="p-4 text-right whitespace-nowrap">
                                <div id="verify-container-{{ $bk->id }}" class="flex flex-col gap-1 items-end">
                                    {{-- Status Informatif Visual --}}
                                    <div class="flex flex-col gap-0.5 mb-1.5">
                                        @if($bk->payment_status === 'Down Payment')
                                            <span class="text-blue-400 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider">
                                                <i class="fa-solid fa-circle-check"></i> DP Verif
                                            </span>
                                            <span class="text-emerald-500 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider">
                                                <i class="fa-solid fa-calendar-check"></i> Inspeksi OK
                                            </span>
                                        @elseif($bk->payment_status === 'Paid')
                                            <span class="text-emerald-400 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider">
                                                <i class="fa-solid fa-lock"></i> Lunas (Sold)
                                            </span>
                                            @if($bk->handed_over_at)
                                                <span class="text-zinc-400 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider">
                                                    <i class="fa-solid fa-truck-ramp-box"></i> Serah Terima OK
                                                </span>
                                            @else
                                                <span class="text-amber-500 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider animate-pulse">
                                                    <i class="fa-solid fa-clock"></i> Tunggu Handover
                                                </span>
                                            @endif
                                        @elseif($bk->payment_status === 'Cancelled')
                                            <span class="text-zinc-500 text-[8px] font-bold flex items-center gap-1 justify-end uppercase tracking-wider">
                                                <i class="fa-solid fa-ban"></i> Dibatalkan
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Dropdown Pengelolaan --}}
                                    <div class="relative inline-block text-left select-none admin-action-dropdown" id="dropdown-parent-{{ $bk->id }}">
                                        <button onclick="toggleActionDropdown({{ $bk->id }}, event)"
                                                class="bg-zinc-900 border border-zinc-800 hover:border-luxury-gold text-zinc-300 hover:text-white font-bold py-1 px-3 text-[9px] uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                                            Kelola <i class="fa-solid fa-chevron-down text-[7px] transition-transform duration-350" id="dropdown-icon-{{ $bk->id }}"></i>
                                        </button>
                                        <div id="dropdown-menu-{{ $bk->id }}"
                                             class="hidden absolute right-0 mt-1.5 w-44 bg-zinc-950 border border-zinc-850 shadow-2xl z-40 text-left py-1 text-[9px] font-bold uppercase tracking-wider">

                                            {{-- Setujui (Jika Pending Validation) --}}
                                            @if($bk->payment_status === 'Pending Validation')
                                                <button onclick="ajaxApprove({{ $bk->id }}, this, '{{ addslashes($bk->customer?->name ?? '') }}', '{{ $bk->customer?->phone ?? '' }}', '{{ $bk->invoice_code }}', '{{ addslashes(($bk->item?->brand ?? '') . ' ' . ($bk->item?->model ?? '')) }}')"
                                                        class="w-full text-left px-4 py-2 hover:bg-emerald-950/40 text-emerald-400 hover:text-emerald-300 flex items-center gap-2 border-b border-zinc-900/60 transition-colors">
                                                    <i class="fa-solid fa-check-double text-[10px] w-4"></i> Setujui & Sahkan
                                                </button>
                                                <button onclick="openRejectModal({{ $bk->id }}, '{{ addslashes($bk->customer?->name ?? '') }}', '{{ $bk->customer?->phone ?? '' }}', '{{ $bk->invoice_code }}', '{{ addslashes(($bk->item?->brand ?? '') . ' ' . ($bk->item?->model ?? '')) }}')"
                                                        class="w-full text-left px-4 py-2 hover:bg-red-950/40 text-red-400 hover:text-red-300 flex items-center gap-2 border-b border-zinc-900/60 transition-colors">
                                                    <i class="fa-solid fa-xmark text-[10px] w-4"></i> Tolak Bukti
                                                </button>
                                            @endif

                                            {{-- Konfirmasi Serah Terima Unit (Hanya jika Paid dan belum Handover) --}}
                                            @if($bk->payment_status === 'Paid' && !$bk->handed_over_at)
                                                <button onclick="ajaxConfirmHandover({{ $bk->id }}, this)"
                                                        class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-luxury-gold flex items-center gap-2 border-b border-zinc-900/60 transition-colors">
                                                    <i class="fa-solid fa-truck-ramp-box text-[10px] w-4"></i> Serah Terima Unit
                                                </button>
                                            @endif

                                            {{-- Cetak Invoice / Kwitansi Dinamis --}}
                                            @if($bk->payment_type === 'Down Payment')
                                                @if($bk->payment_status === 'Paid')
                                                    <a href="{{ route('booking.invoice', $bk->id) }}?type=dp" target="_blank"
                                                       class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-luxury-gold flex items-center gap-2 transition-colors border-b border-zinc-900/40 block">
                                                        <i class="fa-solid fa-file-invoice text-[10px] w-4"></i> Cetak Invoice DP
                                                    </a>
                                                    <a href="{{ route('booking.invoice', $bk->id) }}?type=lunas" target="_blank"
                                                       class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-emerald-400 flex items-center gap-2 transition-colors border-b border-zinc-900/60 block">
                                                        <i class="fa-solid fa-file-signature text-[10px] w-4"></i> Cetak Kwitansi Lunas
                                                    </a>
                                                @else
                                                    <a href="{{ route('booking.invoice', $bk->id) }}?type=dp" target="_blank"
                                                       class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-luxury-gold flex items-center gap-2 transition-colors border-b border-zinc-900/60 block">
                                                        <i class="fa-solid fa-file-invoice text-[10px] w-4"></i> Cetak Invoice DP
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('booking.invoice', $bk->id) }}" target="_blank"
                                                   class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-luxury-gold flex items-center gap-2 transition-colors border-b border-zinc-900/60 block">
                                                    <i class="fa-solid fa-file-invoice-dollar text-[10px] w-4"></i> Cetak Invoice Lunas
                                                </a>
                                            @endif

                                            {{-- Edit Pelanggan & Amend & Batalkan (Hanya jika belum Handover dan belum Cancelled) --}}
                                            @if($bk->payment_status !== 'Cancelled' && !$bk->handed_over_at)
                                                <button onclick="openEditCustomerModal({{ $bk->id }}, '{{ addslashes($bk->customer?->name ?? '') }}', '{{ $bk->customer?->nik ?? '' }}')"
                                                        class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-sky-400 flex items-center gap-2 transition-colors">
                                                    <i class="fa-solid fa-user-pen text-[10px] w-4"></i> Edit Pelanggan
                                                </button>
                                                <button onclick="openAmendModal({{ $bk->id }}, '{{ $bk->date?->format('Y-m-d') ?? '' }}', '{{ $bk->date?->format('H:i') ?? '10:00' }}', '{{ $bk->payment_type }}')"
                                                        class="w-full text-left px-4 py-2 hover:bg-zinc-900 text-violet-400 flex items-center gap-2 transition-colors border-b border-zinc-900/60">
                                                    <i class="fa-solid fa-calendar-day text-[10px] w-4"></i> Amend Jadwal
                                                </button>
                                                <button onclick="openAdminCancelModal({{ $bk->id }}, '{{ addslashes(($bk->item?->brand ?? '') . ' ' . ($bk->item?->model ?? '')) }}', '{{ $bk->invoice_code }}')"
                                                        class="w-full text-left px-4 py-2 hover:bg-red-950/20 text-red-500 hover:text-red-400 flex items-center gap-2 transition-colors">
                                                    <i class="fa-solid fa-ban text-[10px] w-4"></i> Batalkan
                                                </button>
                                            @else
                                                <div class="px-4 py-2 text-zinc-600 italic cursor-not-allowed flex items-center gap-2">
                                                    <i class="fa-solid fa-lock text-[10px] w-4"></i> Data Terkunci
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-zinc-500 italic text-xs">
                                <i class="fa-solid fa-receipt text-4xl mb-3 block text-zinc-800"></i>
                                @if($statusFilter !== 'all')
                                    Tidak ada transaksi dengan filter yang dipilih.
                                @else
                                    Belum ada data transaksi yang masuk.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
            <div class="px-6 py-4 border-t border-zinc-900 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-zinc-600 text-[10px] font-mono">
                    Menampilkan {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }} transaksi
                </p>
                <nav class="flex items-center gap-1">
                    @if($bookings->onFirstPage())
                        <span class="px-3 py-1.5 text-[10px] font-bold text-zinc-700 border border-zinc-900 cursor-not-allowed">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $bookings->previousPageUrl() }}"
                           class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif
                    @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                        @if($page == $bookings->currentPage())
                            <span class="px-3 py-1.5 text-[10px] font-bold bg-luxury-gold text-black">{{ $page }}</span>
                        @elseif(abs($page - $bookings->currentPage()) <= 2)
                            <a href="{{ $url }}" class="px-3 py-1.5 text-[10px] font-bold text-zinc-400 border border-zinc-800 hover:border-luxury-gold hover:text-luxury-gold transition-all">{{ $page }}</a>
                        @elseif(abs($page - $bookings->currentPage()) == 3)
                            <span class="px-2 py-1.5 text-[10px] text-zinc-700">...</span>
                        @endif
                    @endforeach
                    @if($bookings->hasMorePages())
                        <a href="{{ $bookings->nextPageUrl() }}"
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
         MODAL: ALASAN PENOLAKAN BUKTI BAYAR
    ══════════════════════════════════════════ --}}
    <div id="reject-modal"
         class="fixed inset-0 bg-black/85 z-[200] items-center justify-center p-4"
         style="display:none;">
        <div class="bg-zinc-950 border border-red-900/50 w-full max-w-md p-6 shadow-2xl mx-auto">

            {{-- Header modal --}}
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-zinc-900">
                <div class="w-9 h-9 rounded-full bg-red-950 border border-red-900/50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-xmark text-red-400 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-white tracking-wider uppercase">Tolak Bukti Pembayaran</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5" id="reject-modal-subtitle">—</p>
                </div>
                <button onclick="closeRejectModal()" class="ml-auto text-zinc-600 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Peringatan --}}
            <div class="bg-amber-950/30 border border-amber-900/40 p-3 text-[10px] text-amber-400 flex items-start gap-2 mb-4 rounded">
                <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
                <span>Penolakan akan <strong>mengembalikan status unit ke Available</strong>. Pelanggan dapat melakukan booking ulang atau mengupload ulang bukti bayar yang benar.</span>
            </div>

            {{-- Input alasan --}}
            <div class="mb-4">
                <label class="block text-zinc-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                    Alasan Penolakan <span class="text-red-400">*</span>
                    <span class="text-zinc-600 normal-case font-normal ml-1">(min. 10 karakter)</span>
                </label>
                <textarea id="reject-reason-input"
                    class="w-full bg-zinc-900 border border-zinc-800 text-white text-xs p-3 focus:outline-none focus:border-red-500 resize-none transition-colors"
                    rows="4"
                    placeholder="Contoh: Nominal transfer tidak sesuai. Seharusnya Rp 890.000.000 namun yang ditransfer hanya Rp 89.000.000..."
                    maxlength="500"
                    oninput="updateRejectCharCount(this)"></textarea>
                <div class="flex justify-between items-center mt-1.5">
                    <p id="reject-error-msg" class="text-red-400 text-[10px] hidden">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>Alasan wajib diisi minimal 10 karakter.
                    </p>
                    <span id="reject-char-count" class="text-zinc-600 text-[10px] ml-auto font-mono">0 / 500</span>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-3">
                <button onclick="closeRejectModal()"
                    class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-2.5 text-[10px] uppercase tracking-wider transition-colors">
                    Batal
                </button>
                <button id="reject-submit-btn" onclick="submitReject()"
                    class="flex-[2] bg-red-800 hover:bg-red-700 text-white font-bold py-2.5 text-[10px] uppercase tracking-wider transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Konfirmasi Penolakan
                </button>
            </div>
        </div>
    </div>

</div>{{-- end space-y-8 --}}
@endsection

@section('scripts')
<script>
// ─────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────
function showToast(type, message) {
    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type, message } }));
}

// Toggle expand/collapse catatan panjang di tabel
function toggleNote(btn) {
    const noteSpan = btn.previousElementSibling;
    const isExpanded = btn.dataset.expanded === '1';
    if (isExpanded) {
        noteSpan.style.cssText = 'display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;';
        btn.textContent = 'Lihat semua';
        btn.dataset.expanded = '0';
    } else {
        noteSpan.style.cssText = 'display:block;overflow:visible;';
        btn.textContent = 'Sembunyikan';
        btn.dataset.expanded = '1';
    }
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) { showToast('error', 'Token keamanan tidak ditemukan. Refresh halaman.'); return null; }
    return meta.content;
}

// Normalise phone: 08xx → 628xx (format internasional untuk wa.me)
function toWaPhone(phone) {
    if (!phone) return '';
    return phone.replace(/\D/g, '').replace(/^0/, '62');
}

// ─────────────────────────────────────────────────────
// MODAL PENOLAKAN — state & helpers
// ─────────────────────────────────────────────────────
let rejectState = {};

function openRejectModal(invoiceId, customerName, customerPhone, invoiceCode, unitName) {
    rejectState = { invoiceId, customerName, customerPhone, invoiceCode, unitName };
    document.getElementById('reject-modal-subtitle').textContent = `${customerName} — ${invoiceCode}`;
    document.getElementById('reject-reason-input').value = '';
    document.getElementById('reject-char-count').textContent = '0 / 500';
    document.getElementById('reject-error-msg').classList.add('hidden');
    const btn = document.getElementById('reject-submit-btn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-xmark mr-1"></i> Konfirmasi Penolakan';
    const modal = document.getElementById('reject-modal');
    modal.style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
    rejectState = {};
}

function updateRejectCharCount(el) {
    document.getElementById('reject-char-count').textContent = `${el.value.length} / 500`;
    if (el.value.length >= 10) document.getElementById('reject-error-msg').classList.add('hidden');
}

// Tutup modal saat klik backdrop
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// ─────────────────────────────────────────────────────
// AJAX: APPROVE — Setujui bukti + inspeksi sekaligus
// ─────────────────────────────────────────────────────
async function ajaxApprove(invoiceId, btn, customerName, customerPhone, invoiceCode, unitName) {
    if (!confirm(`Setujui pembayaran & jadwal inspeksi untuk:\n\n${customerName} — ${unitName}\nInvoice: ${invoiceCode}\n\nAksi ini mengubah status pembayaran DAN inspeksi sekaligus.`)) return;

    const csrfToken = getCsrfToken();
    if (!csrfToken) return;

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

    try {
        const res = await fetch("{{ url('admin/booking') }}/" + invoiceId + "/approve-ajax", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.success) {
            showToast('success', `${data.invoice_code} disetujui! Notifikasi WA disiapkan...`);

            // Update badge payment
            const payBadge = document.getElementById(`pay-badge-${invoiceId}`);
            if (payBadge) {
                payBadge.textContent = data.new_payment_status;
                const cls = data.new_payment_status === 'Paid'
                    ? 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50'
                    : 'bg-blue-950/60 text-blue-400 border-blue-900/50';
                payBadge.className = `px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wider ${cls}`;
            }

            // Update badge inspeksi
            const inspBadge = document.getElementById(`insp-badge-${invoiceId}`);
            if (inspBadge) {
                inspBadge.innerHTML = '<i class="fa-solid fa-calendar-check text-[8px] mr-0.5"></i> Approved';
                inspBadge.className = 'px-2.5 py-0.5 border rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-950/40 text-emerald-500 border-emerald-900/40';
            }

            // Update container aksi
            const container = document.getElementById(`verify-container-${invoiceId}`);
            if (container) {
                const isPaid = data.new_payment_status === 'Paid';
                container.innerHTML = `
                    <span class="${isPaid ? 'text-emerald-400' : 'text-blue-400'} text-[9px] font-bold flex items-center gap-1 justify-end">
                        <i class="fa-solid ${isPaid ? 'fa-lock' : 'fa-circle-check'}"></i>
                        ${isPaid ? 'Selesai (Lunas)' : 'DP Terverifikasi'}
                    </span>
                    <span class="text-emerald-500 text-[9px] font-bold flex items-center gap-1 justify-end">
                        <i class="fa-solid fa-calendar-check"></i> Inspeksi Approved
                    </span>`;
            }

            // Hapus dari antrean inspeksi
            const card = document.getElementById(`insp-card-${invoiceId}`);
            if (card) {
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                card.style.opacity = '0'; card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 420);
            }

            // Reload halaman setelah 2 detik agar dropdown aksi terupdate
            // (dropdown berisi kondisi Blade yang hanya bisa diperbarui dari server)
            setTimeout(() => window.location.reload(), 2000);

            // Buka WA prefilled setelah 1.2 detik
            const waMsg = encodeURIComponent(
                `Yth. ${data.customer_name},\n\nKami informasikan bahwa pembayaran Anda untuk unit *${data.unit_name}* (Invoice: *${data.invoice_code}*) telah *DIVERIFIKASI* ✅\n\nJadwal inspeksi Anda juga telah *DISETUJUI* 🎉\n\nSilakan tunjukkan kwitansi digital Anda kepada staf kami saat kedatangan.\n\nTerima kasih telah memilih ShowDrive!`
            );
            const waUrl = `https://wa.me/${toWaPhone(data.customer_phone)}?text=${waMsg}`;

            // Konfirmasi visual sebelum buka WA
            const toastEl = document.createElement('div');
            toastEl.className = 'fixed bottom-6 right-6 z-[300] bg-emerald-950 border border-emerald-800 text-emerald-300 px-5 py-3 text-xs font-semibold flex items-center gap-3 shadow-2xl max-w-sm cursor-pointer';
            toastEl.innerHTML = `<i class="fa-brands fa-whatsapp text-emerald-400 text-base shrink-0"></i>
                <span>Notifikasi WA untuk <strong>${data.customer_name}</strong> siap dikirim. <u>Klik di sini untuk buka WhatsApp.</u></span>`;
            toastEl.onclick = () => { window.open(waUrl, '_blank'); toastEl.remove(); };
            document.body.appendChild(toastEl);
            setTimeout(() => { if (toastEl.parentNode) toastEl.remove(); }, 8000);

        } else {
            showToast('error', data.message || 'Gagal menyetujui invoice.');
            btn.disabled = false; btn.innerHTML = originalHTML;
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        btn.disabled = false; btn.innerHTML = originalHTML;
    }
}

// ─────────────────────────────────────────────────────
// AJAX: REJECT — submit modal penolakan
// ─────────────────────────────────────────────────────
async function submitReject() {
    const reason = document.getElementById('reject-reason-input').value.trim();
    if (reason.length < 10) {
        document.getElementById('reject-error-msg').classList.remove('hidden');
        return;
    }

    const csrfToken = getCsrfToken();
    if (!csrfToken) return;

    const btn = document.getElementById('reject-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

    try {
        const res = await fetch("{{ url('admin/booking') }}/" + rejectState.invoiceId + "/reject-ajax", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ rejection_note: reason }),
        });
        const data = await res.json();

        if (data.success) {
            closeRejectModal();
            showToast('error', 'Bukti pembayaran ditolak. Unit dikembalikan ke Available.');

            // Update badge payment → Unpaid
            const payBadge = document.getElementById(`pay-badge-${data.invoice_id}`);
            if (payBadge) {
                payBadge.textContent = 'Unpaid';
                payBadge.className = 'px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-amber-950/60 text-amber-400 border-amber-900/50';
            }

            // Update badge inspeksi → Rejected
            const inspBadge = document.getElementById(`insp-badge-${data.invoice_id}`);
            if (inspBadge) {
                inspBadge.innerHTML = '<i class="fa-solid fa-xmark text-[8px] mr-0.5"></i> Rejected';
                inspBadge.className = 'px-2.5 py-0.5 border rounded-full text-[9px] font-bold uppercase tracking-wider bg-red-950/40 text-red-400 border-red-900/40';
            }

            // Update container aksi
            const container = document.getElementById(`verify-container-${data.invoice_id}`);
            if (container) {
                container.innerHTML = `
                    <span class="text-red-400 text-[9px] font-bold flex items-center gap-1 justify-end">
                        <i class="fa-solid fa-circle-xmark"></i> Bukti Ditolak
                    </span>`;
            }

            // Hapus dari antrean inspeksi jika ada
            const card = document.getElementById(`insp-card-${data.invoice_id}`);
            if (card) {
                card.style.transition = 'opacity 0.4s ease'; card.style.opacity = '0';
                setTimeout(() => card.remove(), 420);
            }

            // Reload halaman setelah 2 detik agar dropdown aksi terupdate
            setTimeout(() => window.location.reload(), 2000);

            // Buka WA prefilled notifikasi penolakan + info refund
            const waMsg = encodeURIComponent(
                `Yth. ${data.customer_name},\n\nMohon maaf, bukti pembayaran Anda untuk unit *${data.unit_name}* (Invoice: *${data.invoice_code}*) telah kami tinjau dan *DITOLAK* ❌\n\n*Alasan:*\n_${data.rejection_note}_\n\nUntuk proses pengembalian dana atau jika ada pertanyaan, silakan hubungi kami langsung. Unit Anda telah dikembalikan ke daftar tersedia.\n\nTerima kasih,\nTim ShowDrive`
            );
            setTimeout(() => window.open(`https://wa.me/${toWaPhone(data.customer_phone)}?text=${waMsg}`, '_blank'), 800);

        } else {
            showToast('error', data.message || 'Gagal menolak invoice.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-xmark mr-1"></i> Konfirmasi Penolakan';
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-xmark mr-1"></i> Konfirmasi Penolakan';
    }
}

// ─────────────────────────────────────────────────────
// AJAX: CANCEL — batalkan invoice
// ─────────────────────────────────────────────────────
// =========================================================
// ADMIN CANCEL MODAL
// =========================================================
let adminCancelInvoiceId = null;

function openAdminCancelModal(invoiceId, unitName, invoiceCode) {
    adminCancelInvoiceId = invoiceId;
    document.getElementById('admin-cancel-subtitle').textContent = unitName + ' — ' + invoiceCode;
    document.getElementById('admin-cancel-reason-input').value = '';
    document.getElementById('admin-cancel-char-count').textContent = '0 / 300';
    document.getElementById('admin-cancel-error-msg').classList.add('hidden');
    const btn = document.getElementById('admin-cancel-submit-btn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Konfirmasi Pembatalan';
    document.getElementById('admin-cancel-modal').style.display = 'flex';
}

function closeAdminCancelModal() {
    document.getElementById('admin-cancel-modal').style.display = 'none';
    adminCancelInvoiceId = null;
}

function updateAdminCancelCharCount(el) {
    document.getElementById('admin-cancel-char-count').textContent = el.value.length + ' / 300';
    if (el.value.trim().length >= 5) {
        document.getElementById('admin-cancel-error-msg').classList.add('hidden');
    }
}

async function submitAdminCancelInvoice() {
    const reason = document.getElementById('admin-cancel-reason-input').value.trim();

    if (reason.length < 5) {
        document.getElementById('admin-cancel-error-msg').classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('admin-cancel-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Membatalkan...';

    const csrfToken = getCsrfToken();
    if (!csrfToken) return;

    try {
        const res = await fetch(`{{ url('admin/booking') }}/${adminCancelInvoiceId}/cancel-ajax`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ cancellation_note: reason }),
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', 'Invoice berhasil dibatalkan oleh admin.');
            const payBadge = document.getElementById(`pay-badge-${adminCancelInvoiceId}`);
            if (payBadge) { payBadge.textContent = 'Cancelled'; payBadge.className = 'px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-zinc-900 text-zinc-500 border-zinc-700'; }
            const container = document.getElementById(`verify-container-${adminCancelInvoiceId}`);
            if (container) container.innerHTML = `<span class="text-zinc-500 text-[9px] font-bold flex items-center gap-1 justify-end"><i class="fa-solid fa-ban"></i> Dibatalkan</span>`;
            closeAdminCancelModal();
        } else {
            showToast('error', data.message || 'Gagal membatalkan invoice.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Konfirmasi Pembatalan';
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Konfirmasi Pembatalan';
    }
}

async function ajaxConfirmHandover(invoiceId, btn) {
    if (!confirm('Apakah Anda yakin ingin mengonfirmasi serah terima fisik unit kendaraan ini? Aksi ini akan mengunci data transaksi.')) return;

    const csrfToken = getCsrfToken();
    if (!csrfToken) return;

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

    try {
        const res = await fetch(`{{ url('admin/booking') }}/${invoiceId}/handover-ajax`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            showToast('success', 'Serah terima unit berhasil dikonfirmasi.');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('error', data.message || 'Gagal memproses serah terima.');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah.');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const adminCancelModalEl = document.getElementById('admin-cancel-modal');
    if (adminCancelModalEl) {
        adminCancelModalEl.addEventListener('click', function (e) {
            if (e.target === this) closeAdminCancelModal();
        });
    }
});

// =========================================================
// ACTION DROPDOWN MANAGEMENT
// =========================================================
function toggleActionDropdown(invoiceId, event) {
    event.stopPropagation();

    // Tutup semua dropdown lain yang terbuka terlebih dahulu
    const allMenus = document.querySelectorAll('[id^="dropdown-menu-"]');
    const allIcons = document.querySelectorAll('[id^="dropdown-icon-"]');

    allMenus.forEach(menu => {
        if (menu.id !== `dropdown-menu-${invoiceId}`) {
            menu.classList.add('hidden');
        }
    });

    allIcons.forEach(icon => {
        if (icon.id !== `dropdown-icon-${invoiceId}`) {
            icon.classList.remove('rotate-180');
        }
    });

    // Toggle dropdown yang ditarget
    const currentMenu = document.getElementById(`dropdown-menu-${invoiceId}`);
    const currentIcon = document.getElementById(`dropdown-icon-${invoiceId}`);

    if (currentMenu) {
        const isHidden = currentMenu.classList.contains('hidden');
        if (isHidden) {
            currentMenu.classList.remove('hidden');
            if (currentIcon) currentIcon.classList.add('rotate-180');
        } else {
            currentMenu.classList.add('hidden');
            if (currentIcon) currentIcon.classList.remove('rotate-180');
        }
    }
}

// Handler klik di luar untuk menutup semua dropdown aksi yang terbuka
window.addEventListener('click', function(e) {
    if (!e.target.closest('.admin-action-dropdown')) {
        const allMenus = document.querySelectorAll('[id^="dropdown-menu-"]');
        const allIcons = document.querySelectorAll('[id^="dropdown-icon-"]');
        allMenus.forEach(menu => menu.classList.add('hidden'));
        allIcons.forEach(icon => icon.classList.remove('rotate-180'));
    }
});

// =========================================================
// EDIT PELANGGAN MODAL
// =========================================================
let editCustomerInvoiceId = null;

function openEditCustomerModal(invoiceId, name, nik) {
    editCustomerInvoiceId = invoiceId;
    document.getElementById('edit-customer-name').value = name;
    document.getElementById('edit-customer-nik').value  = nik || '';
    document.getElementById('edit-customer-error').textContent = '';
    document.getElementById('edit-customer-modal').classList.remove('hidden');
}

function closeEditCustomerModal() {
    document.getElementById('edit-customer-modal').classList.add('hidden');
    editCustomerInvoiceId = null;
}

async function submitEditCustomer() {
    const csrfToken = getCsrfToken();
    if (!csrfToken) return;
    const btn = document.getElementById('edit-customer-submit-btn');
    const errEl = document.getElementById('edit-customer-error');
    errEl.textContent = '';
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';
    try {
        const res = await fetch("{{ url('admin/booking') }}/" + editCustomerInvoiceId + "/update-customer-ajax", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('edit-customer-name').value,
                nik:  document.getElementById('edit-customer-nik').value || null,
            }),
        });
        const data = await res.json();
        if (data.success) {
            // Update nama di baris tabel secara real-time
            const row = document.getElementById(`invoice-row-${editCustomerInvoiceId}`);
            if (row) {
                const nameEl = row.querySelector('.customer-name-text');
                if (nameEl) nameEl.textContent = data.new_name;
            }
            showToast('success', 'Data pelanggan berhasil diperbarui.');
            closeEditCustomerModal();
        } else {
            errEl.textContent = data.message || 'Gagal memperbarui data.';
        }
    } catch (err) {
        errEl.textContent = 'Koneksi bermasalah. Coba lagi.';
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

// =========================================================
// AMEND RESERVASI MODAL
// =========================================================
let amendInvoiceId = null;

function openAmendModal(invoiceId, currentDate, currentTime, currentPaymentType) {
    amendInvoiceId = invoiceId;
    document.getElementById('amend-date').value = currentDate;
    document.getElementById('amend-time').value = currentTime || '10:00';
    document.getElementById('amend-payment-type').value = currentPaymentType;
    document.getElementById('amend-error').textContent = '';
    // Set min/max date
    const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate() + 1);
    const maxDate  = new Date(); maxDate.setDate(maxDate.getDate() + 7);
    document.getElementById('amend-date').min = tomorrow.toISOString().split('T')[0];
    document.getElementById('amend-date').max = maxDate.toISOString().split('T')[0];
    document.getElementById('amend-modal').classList.remove('hidden');
}

function closeAmendModal() {
    document.getElementById('amend-modal').classList.add('hidden');
    amendInvoiceId = null;
}

async function submitAmend() {
    const csrfToken = getCsrfToken();
    if (!csrfToken) return;
    const btn = document.getElementById('amend-submit-btn');
    const errEl = document.getElementById('amend-error');
    errEl.textContent = '';
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';
    try {
        const res = await fetch("{{ url('admin/booking') }}/" + amendInvoiceId + "/amend-ajax", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({
                date:         document.getElementById('amend-date').value,
                time:         document.getElementById('amend-time').value,
                payment_type: document.getElementById('amend-payment-type').value,
            }),
        });
        const data = await res.json();
        if (data.success) {
            // Update badge payment_type di baris tabel secara real-time
            const row = document.getElementById(`invoice-row-${amendInvoiceId}`);
            if (row) {
                const ptEl = row.querySelector('.payment-type-text');
                if (ptEl) ptEl.textContent = data.new_payment_type;
            }
            showToast('success', `Reservasi diperbarui. Tgl: ${data.new_date} | Tipe: ${data.new_payment_type}`);
            closeAmendModal();
        } else {
            errEl.textContent = data.message || 'Gagal memperbarui reservasi.';
        }
    } catch (err) {
        errEl.textContent = 'Koneksi bermasalah. Coba lagi.';
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}
</script>

{{-- ============================================================ --}}
{{-- MODAL: EDIT DATA PELANGGAN                                   --}}
{{-- ============================================================ --}}
<div id="edit-customer-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeEditCustomerModal()">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
    <div class="relative bg-zinc-950 border border-zinc-800 shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-zinc-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-sky-500"></div>
                <h3 class="font-bold text-sm tracking-[0.15em] text-zinc-200 uppercase">Edit Data Pelanggan</h3>
            </div>
            <button onclick="closeEditCustomerModal()" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-xs text-zinc-500">Koreksi nama atau NIK pelanggan. Nomor HP tidak dapat diubah.</p>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">Nama Pelanggan <span class="text-red-400">*</span></label>
                <input id="edit-customer-name" type="text"
                    class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm px-3 py-2 focus:outline-none focus:border-sky-500 transition-colors"
                    placeholder="Nama lengkap pelanggan" />
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">NIK (Opsional)</label>
                <input id="edit-customer-nik" type="text" maxlength="16"
                    class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm px-3 py-2 font-mono focus:outline-none focus:border-sky-500 transition-colors"
                    placeholder="16 digit nomor KTP" />
                <p class="text-[10px] text-zinc-600 mt-1">Kosongkan jika tidak ingin mengubah NIK.</p>
            </div>
            <p id="edit-customer-error" class="text-red-400 text-xs min-h-[1rem]"></p>
        </div>
        <div class="px-6 py-4 border-t border-zinc-900 flex justify-end gap-3">
            <button onclick="closeEditCustomerModal()"
                class="border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 font-bold py-2 px-5 text-xs uppercase tracking-wider transition-all">
                Batal
            </button>
            <button id="edit-customer-submit-btn" onclick="submitEditCustomer()"
                class="bg-sky-700 hover:bg-sky-600 text-white font-bold py-2 px-5 text-xs uppercase tracking-wider flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-user-check"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: AMEND RESERVASI                                       --}}
{{-- ============================================================ --}}
<div id="amend-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeAmendModal()">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
    <div class="relative bg-zinc-950 border border-zinc-800 shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-zinc-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-violet-500"></div>
                <h3 class="font-bold text-sm tracking-[0.15em] text-zinc-200 uppercase">Amend Reservasi</h3>
            </div>
            <button onclick="closeAmendModal()" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="bg-violet-950/30 border border-violet-900/40 px-4 py-3 text-xs text-violet-300">
                <i class="fa-solid fa-circle-info mr-1.5"></i>
                Perubahan hanya bisa dilakukan selama reservasi masih berstatus <strong>Belum Bayar</strong>.
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">Tanggal Inspeksi <span class="text-red-400">*</span></label>
                <input id="amend-date" type="date"
                    class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm px-3 py-2 focus:outline-none focus:border-violet-500 transition-colors calendar-gold" />
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">
                    Jam Inspeksi <span class="text-red-400">*</span>
                    <span class="text-zinc-600 normal-case font-normal ml-1">(08:00–17:00)</span>
                </label>
                <input id="amend-time" type="time"
                    min="08:00" max="17:00"
                    class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm px-3 py-2 focus:outline-none focus:border-violet-500 transition-colors calendar-gold" />
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1.5">Tipe Pembayaran <span class="text-red-400">*</span></label>
                <select id="amend-payment-type"
                    class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm px-3 py-2 focus:outline-none focus:border-violet-500 transition-colors">
                    <option value="Down Payment">Down Payment (DP)</option>
                    <option value="Paid">Lunas (Full Payment)</option>
                </select>
            </div>
            <p id="amend-error" class="text-red-400 text-xs min-h-[1rem]"></p>
        </div>
        <div class="px-6 py-4 border-t border-zinc-900 flex justify-end gap-3">
            <button onclick="closeAmendModal()"
                class="border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 font-bold py-2 px-5 text-xs uppercase tracking-wider transition-all">
                Batal
            </button>
            <button id="amend-submit-btn" onclick="submitAmend()"
                class="bg-violet-700 hover:bg-violet-600 text-white font-bold py-2 px-5 text-xs uppercase tracking-wider flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-calendar-check"></i> Terapkan Perubahan
            </button>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- MODAL: PEMBATALAN OLEH ADMIN (dengan alasan wajib)          --}}
{{-- ============================================================ --}}
<div id="admin-cancel-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display:none; background: rgba(0,0,0,0.80); backdrop-filter: blur(4px);">
    <div class="bg-zinc-950 border border-red-900/50 shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-zinc-900 flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1 h-5 bg-red-600"></div>
                    <h3 class="font-black text-sm tracking-[0.15em] text-red-400 uppercase">Batalkan Reservasi</h3>
                </div>
                <p class="text-[10px] text-zinc-500 mt-0.5" id="admin-cancel-subtitle">—</p>
            </div>
            <button onclick="closeAdminCancelModal()" class="text-zinc-500 hover:text-white transition-colors mt-0.5 flex-shrink-0">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="bg-red-950/30 border border-red-900/30 p-3 text-[10px] text-red-400 leading-relaxed">
                <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                Pembatalan oleh admin bersifat <strong>permanen</strong>. Nominal yang sudah dibayarkan akan direset ke 0 dan unit kendaraan akan dikembalikan ke status <strong>Available</strong>.
            </div>
            <div>
                <label class="block text-zinc-400 font-bold uppercase tracking-widest text-[10px] mb-2">Alasan Pembatalan Admin <span class="text-red-400">*</span></label>
                <textarea id="admin-cancel-reason-input"
                          maxlength="300"
                          rows="4"
                          oninput="updateAdminCancelCharCount(this)"
                          placeholder="Jelaskan alasan pembatalan secara ringkas dan jelas..."
                          class="w-full bg-zinc-900 border border-zinc-800 focus:border-red-500/60 text-white p-3 text-xs focus:outline-none transition-colors placeholder-zinc-700 resize-none"></textarea>
                <div class="flex items-center justify-between mt-1">
                    <p id="admin-cancel-error-msg" class="text-red-500 text-[10px] hidden">Alasan wajib diisi minimal 5 karakter.</p>
                    <span id="admin-cancel-char-count" class="text-zinc-600 text-[9px] font-mono ml-auto">0 / 300</span>
                </div>
            </div>
        </div>
        <div class="px-6 pb-5 flex justify-end gap-2">
            <button onclick="closeAdminCancelModal()"
                    class="border border-zinc-800 hover:border-zinc-600 text-zinc-400 hover:text-white font-bold py-2 px-5 text-[10px] uppercase tracking-wider transition-all">
                Batal
            </button>
            <button id="admin-cancel-submit-btn" onclick="submitAdminCancelInvoice()"
                    class="bg-red-700 hover:bg-red-600 text-white font-extrabold py-2 px-5 text-[10px] uppercase tracking-wider flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-ban mr-1"></i> Konfirmasi Pembatalan
            </button>
        </div>
    </div>
</div>

@endsection
