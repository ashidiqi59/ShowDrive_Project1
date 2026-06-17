<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran Resmi - ShowDrive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        luxury: {
                            gold: '#D4AF37'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-100 text-black min-h-screen py-12 px-4">

    <div class="max-w-3xl mx-auto p-8 bg-white text-black shadow-2xl relative border-t-8 border-luxury-gold">
        <!-- No-Print Back Button -->
        <a href="{{ route('booking.track', ['phone' => $booking->phone]) }}" class="no-print absolute -top-12 left-0 flex items-center gap-2 text-zinc-600 hover:text-black font-bold tracking-widest text-xs transition-colors">
            <i class="fa-solid fa-arrow-left"></i> KEMBALI KE RIWAYAT STATUS
        </a>
        <button onclick="window.print()" class="no-print absolute -top-12 right-0 bg-luxury-gold text-black hover:bg-yellow-600 font-bold px-4 py-2 text-xs tracking-wider transition-all">
            <i class="fa-solid fa-print mr-1"></i> CETAK DOKUMEN (PDF)
        </button>

        <!-- Brand Header Invoice -->
        <div class="flex justify-between items-start border-b-2 border-zinc-100 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-black tracking-widest text-zinc-900">SHOW<span class="text-luxury-gold">DRIVE</span></h1>
                <p class="text-xs text-zinc-500 font-semibold tracking-wider uppercase mt-1">Exclusive Luxury Showroom</p>
                <p class="text-[10px] text-zinc-400 mt-2">D4 Teknik Informatika - Universitas Logistik & Bisnis Internasional (ULBI)</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-zinc-800 tracking-wider">KWITANSI RESMI</h2>
                <p class="text-[11px] font-mono text-zinc-500 mt-1">SD-INV-00{{ $booking->id }}</p>
                <p class="text-[10px] text-zinc-400 mt-1">Tanggal: {{ $booking->date }}</p>
            </div>
        </div>

        <!-- Customer & Vehicle Info -->
        <div class="grid grid-cols-2 gap-8 mb-8 text-xs leading-relaxed">
            <div>
                <h3 class="font-bold text-zinc-500 uppercase tracking-wider mb-2">DETAIL PELANGGAN:</h3>
                <p class="font-extrabold text-sm text-zinc-800">{{ strtoupper($booking->customer_name) }}</p>
                <p class="text-zinc-600 font-medium">Nomor WA: {{ $booking->phone }}</p>
            </div>
            <div>
                <h3 class="font-bold text-zinc-500 uppercase tracking-wider mb-2">DETAIL UNIT KENDARAAN:</h3>
                <p class="font-extrabold text-sm text-zinc-800">{{ $booking->car->brand }} {{ $booking->car->model }}</p>
                <p class="text-zinc-600 font-mono tracking-wider">VIN: {{ $booking->car->vin }}</p>
                <p class="text-zinc-600 font-medium">Mesin: {{ $booking->car->engine }} | Transmisi: {{ $booking->car->transmission }}</p>
            </div>
        </div>

        <!-- Invoice Item Table -->
        <table class="w-full text-left text-xs mb-8">
            <thead>
                <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 uppercase font-bold tracking-wider">
                    <th class="p-3">Uraian Transaksi</th>
                    <th class="p-3 text-right">Harga Unit</th>
                    <th class="p-3 text-right">Total Bayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-zinc-700">
                <tr>
                    <td class="p-3">
                        <span class="font-bold text-zinc-900 block">
                            {{ $booking->payment_status === 'Paid' ? 'PELUNASAN PENUH KENDARAAN (LUNAS)' : 'UANG MUKA PEMESANAN UNIT (DP)' }}
                        </span>
                        <span class="text-[10px] text-zinc-400 block mt-1">Uang Jaminan Booking / Pelunasan Terhadap Unit Mobil</span>
                    </td>
                    <td class="p-3 text-right font-mono">IDR {{ number_format($booking->car->price, 0, ',', '.') }}</td>
                    <td class="p-3 text-right font-bold text-zinc-900 font-mono">IDR {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Box & Payment Status Badge -->
        <div class="flex justify-between items-end border-t border-zinc-200 pt-6">
            <div>
                <span class="text-xs text-zinc-500 font-bold block mb-1">STATUS PEMBAYARAN:</span>
                @php
                    $statusClass = $booking->payment_status === 'Paid' ? 'bg-emerald-600' : 'bg-amber-600';
                @endphp
                <span class="px-4 py-1.5 font-black text-[10px] tracking-widest uppercase text-white {{ $statusClass }}">{{ $booking->payment_status }}</span>
            </div>
            <div class="text-right">
                <span class="text-zinc-400 text-xs font-bold uppercase tracking-wider">Jumlah Total Bayar:</span>
                <h3 class="text-2xl font-black text-zinc-900 font-mono mt-1">IDR {{ number_format($booking->paid_amount, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Footer Invoice & Signatures -->
        <div class="grid grid-cols-2 gap-12 mt-16 text-xs text-zinc-500 pt-8 border-t border-zinc-100">
            <div>
                <h4 class="font-bold text-zinc-700 uppercase mb-2">SYARAT & KETENTUAN:</h4>
                <p class="leading-relaxed text-[10px]">1. Kuitansi ini sah jika pembayaran telah divalidasi dan dikonfirmasi lunas oleh sistem admin ShowDrive.<br>2. Jadwal serah terima diatur dalam kontrak perjanjian terpisah.</p>
            </div>
            <div class="text-center flex flex-col items-center">
                <p class="mb-12">Disahkan oleh,<br><strong>Manajemen Operasional ShowDrive</strong></p>
                <div class="w-24 h-[1px] bg-zinc-300"></div>
                <p class="text-[10px] mt-1 text-zinc-400">Aris & Fathoni - Authorized Partner</p>
            </div>
        </div>
    </div>

</body>
</html>
