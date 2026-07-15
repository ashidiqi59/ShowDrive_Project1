@extends('layouts.app')

@section('title', 'Cek Status & Pembayaran Pelanggan - ShowDrive')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 no-print">
    <div class="text-center mb-10">
        <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-1">PELAYANAN MANDIRI</span>
        <h2 class="text-3xl font-black tracking-wider text-white">CEK RESERVASI & PEMBAYARAN</h2>
        <div class="w-12 h-[2px] bg-luxury-gold mx-auto mt-3"></div>
    </div>

    <!-- Kotak Simulasi Notifikasi OTP (Hanya muncul di environment local jika session simulated_otp terpicu) -->
    @if(app()->isLocal() && session('simulated_otp'))
        <div class="max-w-md mx-auto mb-8 bg-zinc-950 border-2 border-luxury-gold p-4 relative rounded shadow-2xl animate-bounce">
            <div class="absolute -top-3 left-4 bg-luxury-gold text-black text-[9px] font-mono px-2 py-0.5 rounded font-black tracking-wider uppercase">
                💬 WhatsApp Simulation Gateway
            </div>
            <div class="flex items-start space-x-3 mt-1.5">
                <div class="text-2xl text-emerald-400">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div class="text-xs text-zinc-300 leading-relaxed">
                    <p class="font-bold text-white text-[11px] mb-1">ShowDrive Verification Center:</p>
                    <p>Halo, ini adalah pesan simulasi OTP untuk nomor <span class="font-mono text-white font-bold">{{ session('simulated_otp')['phone'] }}</span>.</p>
                    <p class="mt-2 text-zinc-400">Kode OTP keamanan Anda adalah:</p>
                    <p class="text-xl font-mono text-luxury-gold font-black tracking-[0.2em] mt-1">{{ session('simulated_otp')['otp'] }}</p>
                    <p class="text-[10px] text-zinc-500 mt-2 italic font-mono">*Berlaku selama 5 menit. Jangan bagikan kode ini kepada siapapun.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- ================= PANEL AUTH / TRACK CONTROL (KIRI) ================= -->
        <div class="bg-zinc-950 border border-zinc-900 p-6 h-fit">

            <!-- Kasus 1: Belum Request OTP & Belum Terverifikasi -->
            @if(!session('track_phone_verified') && !session('track_otp'))
                <h3 class="font-bold text-xs tracking-wider uppercase text-zinc-300 mb-4">
                    <i class="fa-solid fa-phone text-luxury-gold mr-1.5"></i> Masukkan No. HP
                </h3>
                <form action="{{ route('booking.track.otp') }}" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-[11px] text-zinc-500 leading-relaxed">
                        Masukkan Nomor WhatsApp yang Anda gunakan saat memesan kendaraan untuk menerima kode akses keamanan.
                    </p>
                    <div>
                        <input type="tel" name="phone" placeholder="Contoh: 08112233445"
                            class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold text-white p-3 text-xs focus:outline-none placeholder-zinc-600 @error('phone') border-red-500 @enderror" required>
                        @error('phone')
                            <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-bold py-3 text-xs tracking-wider transition-all uppercase">
                        MINTA KODE OTP
                    </button>
                </form>

            <!-- Kasus 2: OTP Sudah Dikirim, Menunggu Verifikasi Input -->
            @elseif(!session('track_phone_verified') && session('track_otp'))
                <h3 class="font-bold text-xs tracking-wider uppercase text-zinc-300 mb-4 text-luxury-gold animate-pulse">
                    <i class="fa-solid fa-shield-halved mr-1.5"></i> Verifikasi OTP
                </h3>
                <form action="{{ route('booking.track.verify') }}" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-[11px] text-zinc-400">
                        Kami telah mengirimkan 4-digit kode akses keamanan ke nomor Anda. Silakan masukkan kode tersebut di bawah ini:
                    </p>
                    <div>
                        <input type="text" name="otp" maxlength="4" placeholder="4 Digit Kode"
                            class="w-full bg-zinc-900 border border-zinc-800 focus:border-luxury-gold text-center text-lg tracking-[0.5em] font-mono text-white p-3 focus:outline-none @error('otp') border-red-500 @enderror" required>
                        @error('otp')
                            <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 text-xs tracking-wider transition-all uppercase">
                        VERIFIKASI KODE
                    </button>
                </form>

                <!-- Tombol Kirim Ulang / Reset Form -->
                <form action="{{ route('booking.track.reset') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full border border-zinc-800 hover:border-zinc-700 text-zinc-400 py-2 text-[10px] tracking-wider transition-all uppercase">
                        BATALKAN / GANTI NO. HP
                    </button>
                </form>

            <!-- Kasus 3: Sudah Terverifikasi, Tampilkan Info Sesi Aktif -->
            @else
                <h3 class="font-bold text-xs tracking-wider uppercase text-zinc-300 mb-4">
                    <i class="fa-solid fa-circle-check text-emerald-400 mr-1.5"></i> Sesi Aktif
                </h3>
                <div class="space-y-4">
                    <div class="bg-zinc-900/55 p-3.5 border border-zinc-900 text-xs">
                        <span class="text-zinc-500 block uppercase text-[9px] mb-1">Terverifikasi Sebagai:</span>
                        <span class="font-bold font-mono text-luxury-gold">{{ session('track_phone_verified') }}</span>
                    </div>

                    <p class="text-[10px] text-zinc-500 leading-relaxed">
                        Anda berada di ruang pelacakan aman. Anda dapat mengunggah dokumen bukti pembayaran dan mencetak kwitansi resmi.
                    </p>

                    <form action="{{ route('booking.track.reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold py-2.5 text-xs tracking-wider transition-all uppercase">
                            KELUAR DASHBOARD
                        </button>
                    </form>
                </div>
            @endif

        </div>

        <!-- ================= HASIL PENELUSURAN DATA (KANAN) ================= -->
        <div class="md:col-span-2 space-y-6">

            <!-- Tampilan Placeholder jika belum terverifikasi login -->
            @if(!session('track_phone_verified'))
                <div id="tracking-result-placeholder" class="bg-zinc-950 border border-zinc-900 p-12 text-center text-zinc-500 italic text-xs flex flex-col items-center justify-center space-y-3">
                    <i class="fa-solid fa-lock text-3xl text-zinc-700 mb-2"></i>
                    <span>Sesi pelacakan terkunci. Silakan verifikasi nomor HP Anda terlebih dahulu menggunakan kode OTP untuk memuat reservasi dan mengunduh berkas kwitansi.</span>
                </div>
            @else

                <!-- Jika lolos verifikasi OTP -->
                @forelse($bookings as $booking)
                    @php
                        $trackItem = $booking->item;

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
                                <h4 class="text-sm font-black text-white uppercase">{{ $booking->customer->name ?? '—' }}</h4>
                                <p class="text-[10px] text-zinc-500 font-mono mt-0.5">{{ $booking->invoice_code }}</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 border text-[9px] font-bold uppercase {{ $badgeColor }}">Status: {{ $booking->status }}</span>
                                <span class="px-2 py-0.5 border text-[9px] font-bold uppercase {{ $payStatusColor }}">Bayar: {{ $booking->payment_status }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs leading-relaxed">
                            <div>
                                <span class="text-zinc-500 block mb-0.5 uppercase text-[9px]">Model Mobil:</span>
                                <span class="font-extrabold text-zinc-200 block">{{ $trackItem->brand }} {{ $trackItem->model }}</span>
                                <span class="text-zinc-500 block font-mono text-[10px] mt-1">Harga: IDR {{ number_format($trackItem->price, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 block mb-0.5 uppercase text-[9px]">Jadwal Temu/Inspeksi:</span>
                                <span class="font-bold text-zinc-200 block"><i class="fa-regular fa-calendar mr-1 text-luxury-gold"></i>{{ $booking->date }}</span>
                                <span class="text-zinc-500 block text-[10px] mt-1">Telah Ditransfer: <strong class="text-emerald-400 font-mono">IDR {{ number_format($booking->paid_amount, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>

                        @if($booking->payment_status !== 'Paid')
                            <div class="border-t border-zinc-900 pt-4 mt-4 bg-zinc-900/10 p-4 border border-zinc-900 space-y-4">
                                <h4 class="font-extrabold text-[10px] tracking-wider text-luxury-gold uppercase">KIRIM DOKUMEN BUKTI PEMBAYARAN MANUAL</h4>

                                {{-- Ringkasan Komitmen Pembayaran --}}
                                @php
                                    $isPelunasan = $booking->payment_status === 'Down Payment' && $booking->status === 'Approved';
                                    if ($isPelunasan) {
                                        $requiredAmount = $booking->total_amount - $booking->paid_amount;
                                        $dpLabel = 'Pelunasan Sisa Pembayaran (80%)';
                                        $paymentMethodLabel = 'Pelunasan Sisa Tagihan';
                                    } else {
                                        $requiredAmount = $booking->payment_type === 'Down Payment'
                                            ? (int) round($trackItem->price * ($trackItem->dp_percentage / 100))
                                            : $booking->total_amount;
                                        $dpLabel = $booking->payment_type === 'Down Payment'
                                            ? 'DP ' . $trackItem->dp_percentage . '%'
                                            : 'Pelunasan Penuh';
                                        $paymentMethodLabel = $booking->payment_type;
                                    }
                                @endphp
                                <div class="bg-zinc-950 border border-zinc-800 p-3 text-[11px] leading-relaxed text-zinc-400">
                                    <div class="flex justify-between border-b border-zinc-900 pb-1.5 mb-1.5">
                                        <span>Metode Pilihan:</span>
                                        <strong class="text-white uppercase">{{ $paymentMethodLabel }}</strong>
                                    </div>
                                    <div class="flex justify-between border-b border-zinc-900 pb-1.5 mb-1.5">
                                        <span>Keterangan:</span>
                                        <strong class="text-zinc-300">{{ $dpLabel }}</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Nominal Wajib Ditransfer:</span>
                                        <strong class="text-luxury-gold font-mono">IDR {{ number_format($requiredAmount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>

                                @if($company && $company->bank_name && $company->bank_account)
                                    <div class="bg-zinc-950 border border-zinc-900 p-4 rounded space-y-2.5 mt-2">
                                        <div class="flex items-center gap-1.5 text-zinc-400 font-bold text-[9px] uppercase tracking-wider">
                                            <i class="fa-solid fa-building-columns text-luxury-gold"></i> Rekening Tujuan Transfer
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs leading-normal">
                                            <div>
                                                <span class="text-zinc-500 block text-[9px] uppercase">Bank</span>
                                                <span class="font-extrabold text-zinc-200">{{ $company->bank_name }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-500 block text-[9px] uppercase">Nama Penerima</span>
                                                <span class="font-bold text-zinc-200">{{ $company->bank_account_holder ?? $company->name }}</span>
                                            </div>
                                            <div class="sm:col-span-2 bg-zinc-900/60 p-2 border border-zinc-800 flex justify-between items-center rounded-sm">
                                                <div>
                                                    <span class="text-zinc-500 block text-[9px] uppercase">Nomor Rekening</span>
                                                    <span class="font-black text-white font-mono tracking-widest text-sm">{{ $company->bank_account }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" onclick="copyToClipboard('{{ $company->bank_account }}')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white px-2 py-1 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 transition-colors">
                                                        <i class="fa-regular fa-copy"></i> Salin
                                                    </button>
                                                    @if($company->qris_image)
                                                        <button type="button" onclick="openQrisModal('{{ asset('storage/' . $company->qris_image) }}')" class="bg-luxury-gold hover:bg-luxury-goldHover text-black px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 transition-colors">
                                                            <i class="fa-solid fa-qrcode"></i> QRIS
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(($booking->payment_status === 'Unpaid' && in_array($booking->status, ['Pending', 'Rejected'], true)) || ($booking->payment_status === 'Down Payment' && $booking->status === 'Approved'))
                                    @if($booking->status === 'Rejected' && $booking->rejection_note)
                                        <div class="bg-red-950/30 border border-red-900/40 p-3 text-[11px] text-red-400 space-y-1.5 rounded">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                                <span class="font-bold">Bukti pembayaran Anda ditolak oleh admin.</span>
                                            </div>
                                            <div class="bg-red-950/40 border border-red-900/30 p-2 rounded text-[10px] text-red-300">
                                                <span class="font-bold block mb-0.5 uppercase tracking-wider text-[9px] text-red-500">Alasan Penolakan Admin:</span>
                                                <span class="italic">{{ $booking->rejection_note }}</span>
                                            </div>
                                            <p class="text-[10px] text-zinc-400">Silakan koreksi transfer Anda dan unggah ulang bukti transfer yang valid di bawah.</p>
                                        </div>
                                    @endif

                                    @if($isPelunasan)
                                        <div class="bg-emerald-950/30 border border-emerald-900/40 p-3 text-[11px] text-emerald-400 space-y-1.5 rounded mb-3">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span class="font-bold">DP Terverifikasi & Jadwal Inspeksi Selesai!</span>
                                            </div>
                                            <p class="text-[10px] text-zinc-400 leading-relaxed">Silakan transfer pelunasan sisa 80% tagihan ke rekening showroom di atas, kemudian unggah bukti transfer pelunasan di bawah.</p>
                                        </div>
                                    @endif

                                    <form action="{{ route('booking.upload_proof', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-zinc-500 font-bold mb-1.5 uppercase text-[10px]">Pilih File Bukti Transfer (Format JPG, PNG, WebP) <span class="text-red-400">*</span></label>
                                            <input type="file" name="payment_proof" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:border file:border-zinc-800 file:bg-zinc-900 file:text-zinc-300 file:text-xs focus:outline-none" required>
                                        </div>
                                        <button type="submit" class="w-full bg-luxury-gold text-black font-bold py-2.5 text-xs uppercase tracking-wider transition-colors mt-2">
                                            UNGGAH BUKTI & KIRIM
                                        </button>
                                    </form>

                                    {{-- Tombol Batalkan Booking — buka modal alasan --}}
                                    <button type="button"
                                        onclick="openCancelModal('{{ $booking->id }}', '{{ addslashes($trackItem->brand . ' ' . $trackItem->model) }}', '{{ $booking->invoice_code }}')"
                                        class="w-full border border-red-900/50 hover:border-red-500 text-red-500 hover:text-red-300 hover:bg-red-950/30 font-bold py-2 text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-ban"></i> BATALKAN RESERVASI INI
                                    </button>
                                @elseif($booking->payment_status === 'Pending Validation')
                                    <div class="bg-amber-950/30 border border-amber-900/40 p-3 text-[11px] text-amber-400 flex items-center gap-2">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                        <span>Bukti pembayaran Anda sedang dalam proses verifikasi oleh admin. Harap tunggu konfirmasi.</span>
                                    </div>
                                @elseif($booking->payment_status === 'Down Payment' && $booking->status === 'Pending')
                                    <div class="bg-blue-950/30 border border-blue-900/40 p-3 text-[11px] text-blue-400 flex items-center gap-2 rounded">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <span>Pembayaran DP Berhasil Diverifikasi. Silakan lakukan inspeksi fisik kendaraan sesuai jadwal yang disetujui sebelum melakukan pelunasan sisa 80%.</span>
                                    </div>
                                @elseif($booking->payment_status === 'Cancelled')
                                    <div class="bg-red-950/30 border border-red-900/40 p-3 text-[11px] text-red-400 space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-ban"></i>
                                            <span class="font-bold">Reservasi ini telah dibatalkan.</span>
                                        </div>
                                        @if($booking->cancellation_note)
                                            <div class="bg-red-950/40 border border-red-900/30 p-2 rounded text-[10px] text-red-300">
                                                <span class="font-bold block mb-0.5 uppercase tracking-wider text-[9px] text-red-500">Alasan Pembatalan Anda:</span>
                                                <span class="italic">{{ $booking->cancellation_note }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- TAMPILAN JIKA SUDAH LUNAS --}}
                            <div class="border-t border-zinc-900 pt-4 mt-4 bg-zinc-900/10 p-4 border border-zinc-900 space-y-4">
                                <h4 class="font-extrabold text-[10px] tracking-wider text-emerald-400 uppercase flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-xs"></i> Reservasi Selesai (Lunas)
                                </h4>

                                <div class="bg-emerald-950/20 border border-emerald-900/30 p-4 rounded text-xs leading-relaxed space-y-3 text-zinc-300">
                                    <div>
                                        <i class="fa-solid fa-lock text-luxury-gold mr-1.5"></i>
                                        Unit kendaraan <strong>{{ $trackItem->brand }} {{ $trackItem->model }}</strong> telah dibayar lunas sepenuhnya. Terima kasih telah mempercayakan transaksi Anda bersama kami.
                                    </div>
                                    
                                    {{-- Status Serah Terima Fisik Mobil --}}
                                    @if($booking->handed_over_at)
                                        <div class="bg-zinc-900 border border-zinc-800 p-3 rounded flex items-start gap-3">
                                            <div class="bg-emerald-950 text-emerald-400 p-2 rounded">
                                                <i class="fa-solid fa-truck-ramp-box text-sm"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-[10px] uppercase text-emerald-400 tracking-wider block">Status Penyerahan Unit:</span>
                                                <span class="text-zinc-200 block text-xs font-semibold mt-0.5">Unit Kendaraan Telah Diserahkan Secara Fisik</span>
                                                <span class="text-zinc-500 block text-[9px] font-mono mt-1">Pada: {{ $booking->handed_over_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-zinc-900 border border-zinc-850 p-3 rounded flex items-start gap-3">
                                            <div class="bg-amber-950 text-amber-400 p-2 rounded animate-pulse">
                                                <i class="fa-solid fa-clock text-sm"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-[10px] uppercase text-amber-500 tracking-wider block">Status Penyerahan Unit:</span>
                                                <span class="text-zinc-300 block text-xs font-semibold mt-0.5">Menunggu Proses Serah Terima Fisik</span>
                                                <p class="text-zinc-500 text-[10px] leading-relaxed mt-1">Silakan hubungi pihak manajemen operasional showroom ShowDrive untuk menjadwalkan pengambilan atau pengiriman unit kendaraan Anda.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Cetak Kwitansi Resmi Lunas --}}
                                <a href="{{ route('booking.invoice', $booking->id) }}" target="_blank"
                                   class="w-full bg-luxury-gold text-black font-extrabold py-2.5 text-xs uppercase tracking-widest transition-colors flex items-center justify-center gap-2 hover:bg-yellow-600 block text-center">
                                    <i class="fa-solid fa-print"></i> Cetak Kwitansi Lunas Resmi (PDF)
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
                        Tidak ada riwayat reservasi terdaftar untuk nomor ini.
                    </div>
                @endforelse

            @endif

        </div>
    </div>
</div>
@endsection

{{-- ══════════════════════════════════════════
     MODAL: ALASAN PEMBATALAN RESERVASI
     (Dari sisi pelanggan di halaman track)
══════════════════════════════════════════ --}}
<div id="cancel-modal"
     class="fixed inset-0 bg-black/80 z-[200] items-center justify-center p-4"
     style="display:none;">
    <div class="bg-zinc-950 border border-red-900/50 w-full max-w-md shadow-2xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-zinc-900">
            <div class="w-8 h-8 rounded-full bg-red-950 border border-red-900/50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-ban text-red-400 text-sm"></i>
            </div>
            <div>
                <h3 class="font-black text-sm text-white tracking-wider uppercase">Batalkan Reservasi</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5" id="cancel-modal-subtitle">—</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            {{-- Peringatan --}}
            <div class="bg-red-950/30 border border-red-900/40 p-3 text-[10px] text-red-400 flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
                <span>Pembatalan bersifat <strong>permanen</strong>. Unit akan dikembalikan ke daftar tersedia dan tidak dapat diurungkan.</span>
            </div>

            {{-- Input alasan --}}
            <div>
                <label class="block text-zinc-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                    Alasan Pembatalan <span class="text-red-400">*</span>
                    <span class="text-zinc-600 normal-case font-normal ml-1">(min. 10 karakter)</span>
                </label>
                <textarea id="cancel-reason-input"
                    class="w-full bg-zinc-900 border border-zinc-800 text-white text-xs p-3 focus:outline-none focus:border-red-500 resize-none transition-colors"
                    rows="3"
                    placeholder="Contoh: Berubah pikiran, ingin memilih unit lain..."
                    maxlength="300"
                    oninput="updateCancelCharCount(this)"></textarea>
                <div class="flex justify-between items-center mt-1.5">
                    <p id="cancel-error-msg" class="text-red-400 text-[10px] hidden">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>Alasan wajib diisi minimal 10 karakter.
                    </p>
                    <span id="cancel-char-count" class="text-zinc-600 text-[10px] ml-auto font-mono">0 / 300</span>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-3 pt-1">
                <button onclick="closeCancelModal()"
                    class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-2.5 text-[10px] uppercase tracking-wider transition-colors">
                    Kembali
                </button>
                <button onclick="submitCancelBooking()"
                    id="cancel-submit-btn"
                    class="flex-[2] bg-red-800 hover:bg-red-700 text-white font-bold py-2.5 text-[10px] uppercase tracking-wider transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-ban"></i> Konfirmasi Pembatalan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Form tersembunyi — di-submit oleh JavaScript setelah validasi alasan --}}
<form id="cancel-booking-form" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="cancellation_note" id="cancel-note-hidden">
</form>

@section('scripts')
<script>
let cancelBookingId = null;

function openCancelModal(bookingId, unitName, invoiceCode) {
    cancelBookingId = bookingId;
    document.getElementById('cancel-modal-subtitle').textContent = `${unitName} — ${invoiceCode}`;
    document.getElementById('cancel-reason-input').value = '';
    document.getElementById('cancel-char-count').textContent = '0 / 300';
    document.getElementById('cancel-error-msg').classList.add('hidden');
    const btn = document.getElementById('cancel-submit-btn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Konfirmasi Pembatalan';
    document.getElementById('cancel-modal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancel-modal').style.display = 'none';
    cancelBookingId = null;
}

function updateCancelCharCount(el) {
    document.getElementById('cancel-char-count').textContent = `${el.value.length} / 300`;
    if (el.value.length >= 10) {
        document.getElementById('cancel-error-msg').classList.add('hidden');
    }
}

function submitCancelBooking() {
    const reason = document.getElementById('cancel-reason-input').value.trim();

    if (reason.length < 10) {
        document.getElementById('cancel-error-msg').classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('cancel-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Membatalkan...';

    // Set action URL dan isi hidden input, lalu submit
    const form = document.getElementById('cancel-booking-form');
    form.action = "{{ url('booking') }}/" + cancelBookingId + "/cancel";
    document.getElementById('cancel-note-hidden').value = reason;
    form.submit();
}

// Tutup modal saat klik backdrop
document.getElementById('cancel-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

// Copy to Clipboard with fallback
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Nomor rekening berhasil disalin!');
        }, function(err) {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            alert('Nomor rekening berhasil disalin!');
        } else {
            alert('Gagal menyalin nomor rekening.');
        }
    } catch (err) {
        alert('Gagal menyalin nomor rekening.');
    }
    document.body.removeChild(textArea);
}

// QRIS Modal Handlers
function openQrisModal(imageSrc) {
    document.getElementById('qris-modal-image').src = imageSrc;
    document.getElementById('qris-modal').classList.remove('hidden');
}

function closeQrisModal() {
    document.getElementById('qris-modal').classList.add('hidden');
}
</script>

{{-- ============================================================ --}}
{{-- MODAL: QRIS SHOWROOM                                        --}}
{{-- ============================================================ --}}
<div id="qris-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeQrisModal()">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
    <div class="relative bg-zinc-950 border border-zinc-800 shadow-2xl w-full max-w-sm">
        <div class="px-6 py-4 border-b border-zinc-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-luxury-gold"></div>
                <h3 class="font-bold text-sm tracking-[0.15em] text-zinc-200 uppercase">Pindai QRIS</h3>
            </div>
            <button onclick="closeQrisModal()" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="p-6 flex flex-col items-center justify-center bg-white m-4 rounded-sm">
            <img id="qris-modal-image" src="" alt="QRIS Code" class="max-w-full h-auto object-contain">
        </div>
        <div class="px-6 py-4 border-t border-zinc-900 text-center">
            <p class="text-[10px] text-zinc-500">Gunakan aplikasi mobile banking atau e-wallet pilihan Anda untuk melakukan pemindaian.</p>
        </div>
    </div>
</div>
@endsection
