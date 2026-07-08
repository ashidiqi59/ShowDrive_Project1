@extends('layouts.admin')

@section('title', 'Validasi Transaksi & Inspeksi')

@section('content')
<div class="space-y-8">

    {{-- ══════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">FINANCIAL GATEWAY</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">VALIDASI TRANSAKSI</h2>
            <p class="text-zinc-500 text-xs mt-1">Verifikasi pembayaran masuk dan kelola antrean jadwal inspeksi kendaraan.</p>
        </div>
        {{-- Summary badges --}}
        <div class="flex flex-wrap gap-3">
            @php
                $pendingCount  = $bookings->where('payment_status', 'Pending Validation')->count();
                $verifiedCount = $bookings->where('payment_status', 'Down Payment')->count();
                $paidCount     = $bookings->where('payment_status', 'Paid')->count();
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

    {{-- ══════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-900/50 text-emerald-400 p-4 text-xs font-bold tracking-wider uppercase flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-950/60 border border-red-900/50 text-red-400 p-4 text-xs font-bold tracking-wider uppercase flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         FILTER STATUS TABS
    ══════════════════════════════════════════ --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['all' => 'Semua Transaksi', 'pending' => 'Pending Validasi', 'verified' => 'Down Payment', 'paid' => 'Lunas'] as $key => $label)
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

    {{-- ══════════════════════════════════════════
         TABEL LAPORAN KEUANGAN & PEMBAYARAN
    ══════════════════════════════════════════ --}}
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
                        <th class="p-4">TIPE & STATUS</th>
                        <th class="p-4">NOMINAL (IDR)</th>
                        <th class="p-4">BUKTI TRANSFER</th>
                        <th class="p-4 text-right">AKSI VERIFIKASI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900" id="invoices-table-body">
                    @forelse($bookings as $bk)
                        @php
                            $payBadge = 'bg-amber-950/60 text-amber-400 border-amber-900/50';
                            if ($bk->payment_status === 'Down Payment') $payBadge = 'bg-blue-950/60 text-blue-400 border-blue-900/50';
                            if ($bk->payment_status === 'Paid') $payBadge = 'bg-emerald-950/60 text-emerald-400 border-emerald-900/50';

                            $inspBadge = 'bg-amber-950/40 text-amber-500';
                            if ($bk->status === 'Approved') $inspBadge = 'bg-emerald-950/40 text-emerald-500';
                            if ($bk->status === 'Rejected') $inspBadge = 'bg-red-950/40 text-red-400';
                        @endphp
                        <tr class="hover:bg-zinc-900/25 transition-colors" id="invoice-row-{{ $bk->id }}">
                            {{-- Pelanggan --}}
                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 flex-shrink-0 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-luxury-gold font-black text-xs">
                                        {{ strtoupper(substr($bk->customer?->name ?? $bk->customer_name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block">{{ $bk->customer?->name ?? $bk->customer_name ?? '—' }}</span>
                                        <span class="text-[9px] text-zinc-500"><i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>{{ $bk->customer?->phone ?? $bk->phone ?? '—' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Unit --}}
                            <td class="p-4">
                                <span class="font-bold text-zinc-200 block">{{ $bk->item?->brand }} {{ $bk->item?->model }}</span>
                                <span class="text-[9px] text-zinc-600 font-mono">{{ $bk->item?->vin }}</span>
                            </td>

                            {{-- Tipe & Status --}}
                            <td class="p-4">
                                <div class="flex flex-col gap-1.5">
                                    <span class="px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $payBadge }}"
                                          id="pay-badge-{{ $bk->id }}">{{ $bk->payment_status }}</span>
                                    <span class="text-[9px] text-zinc-600 uppercase font-bold">{{ $bk->payment_type ?? 'Full Payment' }}</span>
                                </div>
                            </td>

                            {{-- Nominal --}}
                            <td class="p-4 font-black font-mono text-emerald-400">
                                IDR {{ number_format($bk->paid_amount ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Bukti --}}
                            <td class="p-4">
                                @if($bk->payment_proof)
                                    <div class="flex items-center gap-2">
                                        <span class="text-emerald-400 text-[9px] flex items-center gap-1">
                                            <i class="fa-regular fa-image"></i> Ada Berkas
                                        </span>
                                        <a href="{{ asset('storage/' . $bk->payment_proof) }}" target="_blank"
                                           class="underline text-luxury-gold hover:text-white text-[9px] transition-colors">
                                            Lihat <i class="fa-solid fa-external-link-alt text-[8px]"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-zinc-600 italic text-[9px]">Belum ada bukti</span>
                                @endif
                            </td>

                             {{-- Aksi Verifikasi --}}
                             <td class="p-4 text-right whitespace-nowrap">
                                 <div id="verify-container-{{ $bk->id }}">
                                     @if($bk->payment_status === 'Pending Validation')
                                         <button
                                             onclick="ajaxVerifyPayment({{ $bk->id }}, this)"
                                             class="bg-emerald-700 hover:bg-emerald-600 text-white font-bold py-1.5 px-3 text-[9px] uppercase tracking-wider flex items-center gap-1.5 ml-auto transition-colors"
                                             id="verify-btn-{{ $bk->id }}">
                                             <i class="fa-solid fa-check"></i> Sahkan
                                         </button>
                                     @elseif($bk->payment_status === 'Down Payment')
                                         <span class="text-blue-400 text-[9px] font-bold flex items-center gap-1 justify-end">
                                             <i class="fa-solid fa-circle-check"></i> DP Terverifikasi
                                         </span>
                                     @elseif($bk->payment_status === 'Paid')
                                         <span class="text-emerald-400 text-[9px] font-bold flex items-center gap-1 justify-end">
                                             <i class="fa-solid fa-lock"></i> Selesai (Lunas)
                                         </span>
                                     @else
                                         <span class="text-zinc-600 text-[9px] italic flex items-center gap-1 justify-end">
                                             <i class="fa-solid fa-clock"></i> Menunggu Bukti
                                         </span>
                                     @endif
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
    </div>

    {{-- ══════════════════════════════════════════
         ANTREAN INSPEKSI PENDING
    ══════════════════════════════════════════ --}}
    <div class="bg-zinc-950 border border-zinc-900 shadow-2xl">
        <div class="px-6 py-4 border-b border-zinc-900 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-amber-500"></div>
                <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-300 uppercase">
                    <i class="fa-solid fa-calendar-check text-amber-500 mr-1.5"></i> ANTREAN INSPEKSI PENDING
                </h3>
            </div>
            @php $pendingInspCount = $bookings->where('status', 'Pending')->count(); @endphp
            <span class="bg-amber-950/60 border border-amber-900/50 text-amber-400 text-[9px] font-bold px-3 py-1 rounded-full">
                {{ $pendingInspCount }} Menunggu
            </span>
        </div>

        <div class="p-6">
            @if($pendingInspCount > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="inspection-queue">
                    @foreach($bookings->where('status', 'Pending') as $booking)
                        <div class="bg-zinc-900/40 border border-zinc-900 p-4 hover:border-zinc-700 transition-all" id="insp-card-{{ $booking->id }}">
                            {{-- Identitas --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-[10px] font-black text-white block tracking-wide uppercase">
                                        {{ $booking->customer?->name ?? $booking->customer_name ?? '—' }}
                                    </span>
                                    <span class="text-[9px] text-zinc-500">
                                        <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i>
                                        {{ $booking->customer?->phone ?? $booking->phone ?? '—' }}
                                    </span>
                                </div>
                                <span class="bg-amber-950/60 border border-amber-900/50 text-amber-400 text-[8px] font-bold px-2 py-0.5 rounded tracking-wider uppercase">Pending</span>
                            </div>

                            {{-- Detail Unit & Tanggal --}}
                            <div class="text-[9px] text-zinc-400 mb-3 pb-3 border-b border-zinc-900 space-y-1">
                                <p>Unit: <strong class="text-zinc-300">{{ $booking->item?->brand }} {{ $booking->item?->model }}</strong></p>
                                <p class="font-mono text-luxury-gold">
                                    <i class="fa-regular fa-calendar text-[8px] mr-1"></i>{{ $booking->date ?? '—' }}
                                </p>
                            </div>

                            {{-- Tombol Aksi Inspeksi --}}
                            <div class="flex gap-2">
                                <button onclick="ajaxProcessInspection({{ $booking->id }}, 'Approved', this)"
                                    class="flex-1 bg-emerald-800 hover:bg-emerald-600 text-white font-bold py-1.5 text-[9px] uppercase tracking-wider transition-colors flex items-center justify-center gap-1"
                                    id="insp-approve-{{ $booking->id }}">
                                    <i class="fa-solid fa-check"></i> Setujui
                                </button>
                                <button onclick="ajaxProcessInspection({{ $booking->id }}, 'Rejected', this)"
                                    class="flex-1 bg-zinc-800 hover:bg-red-900 text-zinc-400 hover:text-white font-bold py-1.5 text-[9px] uppercase tracking-wider transition-colors flex items-center justify-center gap-1"
                                    id="insp-reject-{{ $booking->id }}">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-zinc-600 text-xs italic">
                    <i class="fa-solid fa-check-double text-emerald-700 text-3xl mb-3 block"></i>
                    Tidak ada antrean inspeksi yang menunggu persetujuan. Semua sudah diproses.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
{{-- ══════════════════════════════════════════
     AJAX: Verifikasi Pembayaran
══════════════════════════════════════════ --}}
<script>
function showToast(type, message) {
    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type, message } }));
}

async function ajaxVerifyPayment(invoiceId, btn) {
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

    try {
        const res = await fetch(`/admin/booking/${invoiceId}/verify-ajax`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        if (data.success) {
            showToast('success', data.message || 'Pembayaran berhasil disahkan!');

            // Update badge di baris tabel
            const badge = document.getElementById(`pay-badge-${invoiceId}`);
            const container = document.getElementById(`verify-container-${invoiceId}`);
            const newStatus = data.new_payment_status;

            if (badge) {
                badge.textContent = newStatus;
                badge.className = badge.className
                    .replace(/bg-\w+-950\/60|text-\w+-400|border-\w+-900\/50/g, '')
                    .trim();
                if (newStatus === 'Paid') {
                    badge.classList.add('bg-emerald-950/60', 'text-emerald-400', 'border-emerald-900/50');
                } else if (newStatus === 'Down Payment') {
                    badge.classList.add('bg-blue-950/60', 'text-blue-400', 'border-blue-900/50');
                }
            }

            // Ganti tombol verifikasi dengan teks status yang sesuai agar sinkron saat diklik
            if (container) {
                if (newStatus === 'Paid') {
                    container.innerHTML = `<span class="text-emerald-400 text-[9px] font-bold flex items-center gap-1 justify-end"><i class="fa-solid fa-lock"></i> Selesai (Lunas)</span>`;
                } else if (newStatus === 'Down Payment') {
                    container.innerHTML = `<span class="text-blue-400 text-[9px] font-bold flex items-center gap-1 justify-end"><i class="fa-solid fa-circle-check"></i> DP Terverifikasi</span>`;
                }
            }
        } else {
            showToast('error', data.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

{{-- ══════════════════════════════════════════
     AJAX: Proses Inspeksi (Setujui / Tolak)
══════════════════════════════════════════ --}}
async function ajaxProcessInspection(invoiceId, status, btn) {
    const card = document.getElementById(`insp-card-${invoiceId}`);
    const originalHTML = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>';
    }

    try {
        const res = await fetch(`/admin/booking/${invoiceId}/status-ajax`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status }),
        });
        const data = await res.json();

        if (data.success) {
            const statusLabel = status === 'Approved' ? 'Disetujui ✓' : 'Ditolak ✗';
            showToast('success', `Inspeksi ${statusLabel}`);
            // Fade out dan hapus card
            if (card) {
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 420);
            }
        } else {
            showToast('error', data.message || 'Terjadi kesalahan.');
            if (btn) { btn.disabled = false; btn.innerHTML = originalHTML; }
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        if (btn) { btn.disabled = false; btn.innerHTML = originalHTML; }
    }
}
</script>
@endsection
