@extends('layouts.app')

@section('title', 'Cek Status & Pembayaran Pelanggan - ShowDrive')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 no-print">
    <div class="text-center mb-10">
        <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-1">PELAYANAN MANDIRI</span>
        <h2 class="text-3xl font-black tracking-wider text-white">CEK RESERVASI & PEMBAYARAN</h2>
        <div class="w-12 h-[2px] bg-luxury-gold mx-auto mt-3"></div>
    </div>

    <!-- Kotak Simulasi Notifikasi OTP (Hanya muncul jika session simulated_otp terpicu) -->
    @if(session('simulated_otp'))
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
                                <span class="font-extrabold text-zinc-200 block">{{ $car->brand }} {{ $car->model }}</span>
                                <span class="text-zinc-500 block font-mono text-[10px] mt-1">Harga: IDR {{ number_format($car->price, 0, ',', '.') }}</span>
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
                                    $requiredAmount = $booking->payment_type === 'Down Payment' ? 50000000 : $car->price;
                                @endphp
                                <div class="bg-zinc-950 border border-zinc-800 p-3 text-[11px] leading-relaxed text-zinc-400">
                                    <div class="flex justify-between border-b border-zinc-900 pb-1.5 mb-1.5">
                                        <span>Metode Pilihan:</span>
                                        <strong class="text-white uppercase">{{ $booking->payment_type }}</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Nominal Wajib Ditransfer:</span>
                                        <strong class="text-luxury-gold font-mono">IDR {{ number_format($requiredAmount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>

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
                        Tidak ada riwayat reservasi terdaftar untuk nomor ini.
                    </div>
                @endforelse

            @endif

        </div>
    </div>
</div>
@endsection
