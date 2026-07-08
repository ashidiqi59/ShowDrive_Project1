<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Katalog utama: Menampilkan seluruh mobil dengan pencarian
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Item::with('warehouse');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%');
            });
        }

        $cars = $query->get();
        return view('home', compact('cars', 'search'));
    }

    /**
     * Detail mobil & Form Booking
     */
    public function show($id)
    {
        $car = Item::findOrFail($id);
        return view('detail', compact('car'));
    }

    /**
     * Proses Booking dari sisi pelanggan
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'customer_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\pL\s\.\'\-]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(08|628|\+628)[0-9]{7,11}$/',
            ],
            'date' => [
                'required',
                'date',
                'after_or_equal:tomorrow',
                'before_or_equal:+7 days',
            ],
            'payment_type' => [
                'required',
                'in:Down Payment,Paid',
            ],
            'car_id' => [
                'required',
                'exists:items,id',
            ],
        ], [
            'customer_name.required'  => 'Nama lengkap wajib diisi.',
            'customer_name.min'       => 'Nama minimal 3 karakter.',
            'customer_name.regex'     => 'Nama hanya boleh mengandung huruf dan spasi.',
            'phone.required'          => 'Nomor HP wajib diisi.',
            'phone.regex'             => 'Format nomor HP tidak valid. Gunakan format: 08xx, 628xx, atau +628xx.',
            'date.required'           => 'Tanggal inspeksi wajib diisi.',
            'date.after_or_equal'     => 'Tanggal inspeksi paling cepat adalah besok.',
            'date.before_or_equal'    => 'Tanggal inspeksi paling lambat adalah 7 hari ke depan.',
            'payment_type.required'   => 'Pilihan metode pembayaran komitmen wajib diisi.',
            'payment_type.in'         => 'Metode pembayaran komitmen tidak valid.',
            'car_id.required'         => 'Unit kendaraan tidak ditemukan.',
            'car_id.exists'           => 'Unit kendaraan tidak valid.',
        ]);

        // Validasi tambahan: cek apakah mobil masih Available
        $item = Item::findOrFail($request->car_id);
        if ($item->getRawOriginal('status') !== 'Available') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['car_id' => 'Maaf, unit ini sudah tidak tersedia untuk dibooking.']);
        }

        $invoiceId = null;
        DB::transaction(function () use ($request, $item, &$invoiceId) {
            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone],
                ['name'  => $request->customer_name]
            );

            // Generate unique invoice code
            $invoiceCode = 'SD-INV-' . strtoupper(Str::random(8));

            $invoice = Invoice::create([
                'invoice_code'      => $invoiceCode,
                'customer_id'       => $customer->id,
                'item_id'           => $item->id,
                'cashier_id'        => null,
                'date'              => $request->date,
                'total_amount'      => $item->price,
                'paid_amount'       => 0,
                'payment_type'      => $request->payment_type, // Menyimpan pilihan metode pembayaran
                'payment_status'    => 'Unpaid',
                'status'            => 'Pending',
                'authentic_receipt' => null,
            ]);

            $invoiceId = $invoice->id;

            // Update item status to Invoiced (ditampilkan sebagai 'Booked' di UI)
            $item->update(['status' => 'Invoiced']);
        });

        // Simpan nomor HP ke session agar link kwitansi bisa langsung diakses tanpa re-input HP
        if ($invoiceId) {
            session(['booking_phone_' . $invoiceId => $request->phone]);
        }

        return redirect()->route('booking.track', ['phone' => $request->phone])
            ->with('success', 'Booking berhasil dibuat! Silakan lakukan transfer dan unggah bukti transfer pembayaran di bawah.');
    }


    /**
     * Pelacakan status pembayaran & jadwal inspeksi (Halaman utama Lacak)
     */
    public function track(Request $request)
    {
        $phone = session('track_phone_verified');
        $bookings = collect();

        // Jika user sudah terverifikasi OTP untuk sesi ini
        if ($phone) {
            $customer = Customer::where('phone', $phone)->first();
            if ($customer) {
                $bookings = Invoice::with(['customer', 'item'])
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->get();
            }
        }

        return view('track', compact('bookings', 'phone'));
    }

    /**
     * Menerima input HP dan mengirimkan kode OTP (Simulasi)
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^(08|628|\+628)[0-9]{7,11}$/',
            ],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex'    => 'Format nomor HP tidak valid.',
        ]);

        $phone = $request->phone;

        // Cek apakah nomor HP tersebut memiliki riwayat booking/invoice
        $customer = Customer::where('phone', $phone)->first();
        if (!$customer || Invoice::where('customer_id', $customer->id)->count() === 0) {
            return redirect()->back()->withErrors(['phone' => 'Nomor HP tidak ditemukan atau tidak memiliki riwayat reservasi.']);
        }

        // Generate OTP 4 Digit secara acak
        $otp = rand(1000, 9999);

        // Simpan data OTP dan nomor HP sementara ke session (expired dalam 5 menit)
        session([
            'track_otp' => $otp,
            'track_phone_request' => $phone,
            'track_otp_expired_at' => now()->addMinutes(5),
        ]);

        // Kirimkan flash notification sebagai simulasi pengantaran pesan OTP WhatsApp/SMS
        return redirect()->back()->with('simulated_otp', [
            'otp'   => $otp,
            'phone' => $phone
        ])->with('success', 'Kode OTP keamanan telah di-generate.');
    }

    /**
     * Memverifikasi kode OTP yang dimasukkan pelanggan
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4',
        ]);

        $sessionOtp = session('track_otp');
        $sessionPhone = session('track_phone_request');
        $expiredAt = session('track_otp_expired_at');

        if (!$sessionOtp || !$sessionPhone || now()->gt($expiredAt)) {
            return redirect()->route('booking.track')->withErrors(['otp' => 'Sesi OTP telah kedaluwarsa. Silakan minta kode OTP baru.']);
        }

        if ($request->otp == $sessionOtp) {
            // Set session verifikasi sukses
            session(['track_phone_verified' => $sessionPhone]);

            // Hapus temporary session OTP
            session()->forget(['track_otp', 'track_phone_request', 'track_otp_expired_at']);

            return redirect()->route('booking.track')->with('success', 'Verifikasi berhasil! Selamat datang di dashboard pelacakan Anda.');
        }

        return redirect()->back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
    }

    /**
     * Keluar dari sesi pelacakan saat ini
     */
    public function resetTrackSession()
    {
        session()->forget(['track_phone_verified']);
        return redirect()->route('booking.track')->with('info', 'Anda telah keluar dari dashboard pelacakan.');
    }

    /**
     * Mengunggah bukti bayar (manual receipt)
     */
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            // Eksplisit gunakan mimes (bukan rule 'image') untuk mencegah upload SVG yang bisa mengandung XSS
            'payment_proof' => 'required|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'payment_proof.required' => 'File bukti transfer wajib diunggah.',
            'payment_proof.mimes'    => 'Format file tidak didukung. Gunakan JPEG, JPG, PNG, atau WebP.',
            'payment_proof.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $invoice = Invoice::findOrFail($id);

        // Guard: Cegah re-upload jika invoice sudah dalam status yang tidak memungkinkan
        if (in_array($invoice->payment_status, ['Paid', 'Down Payment', 'Pending Validation'])) {
            return redirect()->back()->with('error',
                'Tidak dapat mengunggah bukti pembayaran. Status invoice saat ini adalah: "' . $invoice->payment_status . '". Hubungi admin jika ada kendala.'
            );
        }

        // Tentukan nominal yang harus dibayar berdasarkan pilihan tipe pembayaran saat booking
        $paidAmount = 0;
        if ($invoice->payment_type === 'Down Payment') {
            $paidAmount = 50000000; // DP paten 50 Juta Rupiah
        } else {
            $paidAmount = $invoice->total_amount; // Pelunasan Penuh
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('receipts', 'public');

            $invoice->update([
                'paid_amount'       => $paidAmount,
                'authentic_receipt' => $path,
                'payment_status'    => 'Pending Validation',
            ]);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Tampilan Kwitansi Cetak
     * Hanya pemilik invoice (diverifikasi via nomor HP) yang bisa mengakses.
     */
    public function invoice(Request $request, $id)
    {
        $booking = Invoice::with(['customer', 'item'])->findOrFail($id);

        // Cari nomor HP terverifikasi dari session tracking
        $phone = session('track_phone_verified') ?? session('booking_phone_' . $id);

        if (!$phone || $booking->customer->phone !== $phone) {
            abort(403, 'AKSES DITOLAK. Kwitansi ini hanya dapat diakses oleh pemilik transaksi. Silakan lakukan proses verifikasi OTP di menu lacak reservasi.');
        }

        return view('invoice', compact('booking'));
    }
}