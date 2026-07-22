<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Customer;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Public catalog — with optional full-text search across brand/model/VIN.
     * select() projection avoids pulling engine/transmission/image_url into memory
     * for the card-grid view that only needs summary fields.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Item::with('warehouse:id,name')
            ->select('id', 'warehouse_id', 'brand', 'model', 'vin', 'year', 'price', 'status', 'color', 'image_url');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%');
            });
        }

        $cars = $query->latest()->paginate(12)->withQueryString();

        return view('home', compact('cars', 'search'));
    }

    /**
     * Vehicle detail page & booking form.
     */
    public function show(int $id): View
    {
        $car = Item::with(['warehouse:id,name,location', 'images:id,item_id,image_path'])
            ->findOrFail($id);

        return view('detail', compact('car'));
    }

    /**
     * Process a new booking from a customer.
     */
    public function storeBooking(StoreBookingRequest $request): RedirectResponse
    {
        // Validasi sudah ditangani sepenuhnya oleh StoreBookingRequest
        $invoiceId = null;

        try {
            DB::transaction(function () use ($request, &$invoiceId) {
                // Lock item di dalam transaksi untuk mencegah race condition:
                // dua request concurrent tidak bisa sama-sama melewati pengecekan status
                // karena yang kedua akan menunggu lock dilepas oleh yang pertama.
                $item = Item::select('id', 'status', 'price', 'dp_percentage')
                    ->lockForUpdate()
                    ->findOrFail($request->car_id);

                if ($item->getRawOriginal('status') !== 'Available') {
                    throw new \RuntimeException('Unit sudah tidak tersedia.');
                }

                $customer = Customer::firstOrCreate(
                    ['phone' => $request->phone],
                    ['name'  => $request->customer_name]
                );

                // Update NIK jika diisi dan customer ini belum punya NIK
                // Catatan: UniqueConstraintViolationException akan di-catch di luar jika NIK sudah dimiliki orang lain
                if ($request->filled('nik') && ! $customer->nik) {
                    $customer->update(['nik' => $request->nik]);
                }

                // Hitung pajak PPN 11% sesuai regulasi perpajakan Indonesia
                $subtotal  = $item->price;
                $taxRate   = 11.00;
                $taxAmount = round($subtotal * ($taxRate / 100));
                $total     = $subtotal + $taxAmount;

                $invoice = Invoice::create([
                    'invoice_code'   => Invoice::generateCode(),
                    'customer_id'    => $customer->id,
                    'item_id'        => $item->id,
                    'cashier_id'     => null,
                    'date'           => $request->date . ' ' . $request->time . ':00',
                    'subtotal'       => $subtotal,
                    'tax_rate'       => $taxRate,
                    'tax_amount'     => $taxAmount,
                    'total_amount'   => $total,
                    'paid_amount'    => 0,
                    'payment_type'   => $request->payment_type,
                    'payment_status' => 'Unpaid',
                    'status'         => 'Pending',
                ]);

                $invoiceId = $invoice->id;

                $item->update(['status' => 'Invoiced']);
            });
        } catch (\RuntimeException $e) {
            // Unit sudah tidak tersedia (terdeteksi setelah lock di dalam transaksi)
            return redirect()->back()->withInput()
                ->withErrors(['car_id' => 'Maaf, unit ini sudah tidak tersedia untuk dibooking.']);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // NIK yang dimasukkan sudah terdaftar pada akun pelanggan lain
            return redirect()->back()->withInput()
                ->withErrors(['nik' => 'NIK ' . $request->nik . ' sudah terdaftar pada akun pelanggan lain. Pastikan NIK yang Anda masukkan benar, atau kosongkan kolom NIK jika Anda tidak ingin mengisinya.']);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->withErrors(['car_id' => 'Terjadi kesalahan saat memproses booking. Silakan coba kembali.']);
        }

        if ($invoiceId) {
            session([
                'booking_phone_' . $invoiceId  => $request->phone,
                'last_booked_invoice_id'        => $invoiceId,
            ]);
        }

        return redirect()->route('booking.success', $invoiceId);
    }

    /**
     * Halaman konfirmasi setelah booking berhasil dibuat.
     * Dilindungi oleh session 'last_booked_invoice_id' — hanya bisa diakses
     * sekali langsung setelah storeBooking() redirect ke sini.
     */
    public function bookingSuccess(int $id): View
    {
        // Validasi: session harus ada dan harus cocok dengan {id}
        if ((int) session('last_booked_invoice_id') !== $id) {
            abort(403, 'Akses tidak diizinkan. Halaman ini hanya tersedia segera setelah proses booking.');
        }

        $invoice = Invoice::with([
            'customer:id,name,phone,nik',
            'item:id,brand,model,year,vin,price,dp_percentage,image_url',
        ])->findOrFail($id);

        // Forget session setelah data diambil — mencegah akses ulang / bookmark
        session()->forget('last_booked_invoice_id');

        $company = \App\Models\Company::first();

        return view('booking-success', compact('invoice', 'company'));
    }

    /**
     * Tracking dashboard — visible after OTP verification.
     */
    public function track(Request $request): View
    {
        $phone    = session('track_phone_verified');
        $bookings = collect();

        if ($phone) {
            $customer = Customer::select('id', 'name', 'phone')
                ->where('phone', $phone)
                ->first();

            if ($customer) {
                $bookings = Invoice::with([
                    'customer:id,name,phone',
                    'item:id,brand,model,year,price,dp_percentage,image_url',
                ])
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->get();
            }
        }

        return view('track', compact('bookings', 'phone') + ['company' => \App\Models\Company::first()]);
    }

    /**
     * Generate a simulated OTP and store it in the session.
     */
    public function requestOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{7,11}$/'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex'    => 'Format nomor HP tidak valid.',
        ]);

        $phone    = $request->phone;
        $customer = Customer::select('id')->where('phone', $phone)->first();

        if (! $customer || ! Invoice::where('customer_id', $customer->id)->exists()) {
            return redirect()->back()
                ->withErrors(['phone' => 'Nomor HP tidak ditemukan atau tidak memiliki riwayat reservasi.']);
        }

        $otp = random_int(1000, 9999);

        session([
            'track_otp'            => bcrypt((string) $otp), // hash sebelum disimpan ke session
            'track_phone_request'  => $phone,
            'track_otp_expired_at' => now()->addMinutes(5),
        ]);

        return redirect()->back()
            ->with('simulated_otp', ['otp' => $otp, 'phone' => $phone])
            ->with('success', 'Kode OTP keamanan telah di-generate.');
    }

    /**
     * Verify the OTP entered by the customer.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => 'required|numeric|digits:4']);

        $sessionOtp   = session('track_otp');
        $sessionPhone = session('track_phone_request');
        $expiredAt    = session('track_otp_expired_at');

        if (! $sessionOtp || ! $sessionPhone || now()->gt($expiredAt)) {
            return redirect()->route('booking.track')
                ->withErrors(['otp' => 'Sesi OTP telah kedaluwarsa. Silakan minta kode OTP baru.']);
        }

        // Hash::check() mencegah perbandingan plaintext — lebih aman dari hash_equals
        if (\Illuminate\Support\Facades\Hash::check((string) $request->otp, $sessionOtp)) {
            session(['track_phone_verified' => $sessionPhone]);
            session()->forget(['track_otp', 'track_phone_request', 'track_otp_expired_at', 'track_otp_failed_attempts']);

            return redirect()->route('booking.track')
                ->with('success', 'Verifikasi berhasil! Selamat datang di dashboard pelacakan Anda.');
        }

        // Hitung dan batasi kegagalan OTP
        $failedAttempts = session('track_otp_failed_attempts', 0) + 1;
        session(['track_otp_failed_attempts' => $failedAttempts]);

        if ($failedAttempts >= 3) {
            session()->forget(['track_otp', 'track_phone_request', 'track_otp_expired_at', 'track_otp_failed_attempts']);
            return redirect()->route('booking.track')
                ->withErrors(['phone' => 'Terlalu banyak kegagalan verifikasi. Sesi OTP dibatalkan. Silakan minta kode OTP baru.']);
        }

        return redirect()->back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali. Sisa percobaan: ' . (3 - $failedAttempts)]);
    }

    /**
     * Destroy the tracking session.
     */
    public function resetTrackSession(): RedirectResponse
    {
        session()->forget('track_phone_verified');

        return redirect()->route('booking.track')
            ->with('info', 'Anda telah keluar dari dashboard pelacakan.');
    }

    /**
     * Upload payment proof (bukti transfer).
     * SVG is explicitly excluded via mimes to prevent XSS via uploaded SVG.
     */
    public function uploadProof(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'payment_proof' => 'required|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'payment_proof.required' => 'File bukti transfer wajib diunggah.',
            'payment_proof.mimes'    => 'Format tidak didukung. Gunakan JPEG, PNG, atau WebP.',
            'payment_proof.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // Eager loading lockForUpdate() item
                $invoice = Invoice::with(['item' => function ($query) {
                    $query->lockForUpdate();
                }])
                ->select('id', 'payment_status', 'payment_type', 'total_amount', 'authentic_receipt', 'item_id', 'status')
                ->lockForUpdate()
                ->findOrFail($id);

                // Jika status saat ini adalah Paid atau Pending Validation, batalkan upload.
                // Down Payment diperbolehkan untuk proses pelunasan.
                if (in_array($invoice->payment_status, ['Paid', 'Pending Validation'], true)) {
                    throw new \RuntimeException('Tidak dapat mengunggah bukti. Status invoice saat ini: "' . $invoice->payment_status . '".');
                }

                // Hitung nominal target pembayaran secara dinamis
                // Jika invoice sudah dalam status Down Payment, maka ini adalah pembayaran pelunasan (Paid)
                $paidAmount = $invoice->payment_status === 'Down Payment'
                    ? $invoice->total_amount
                    : ($invoice->payment_type === 'Down Payment'
                        ? (int) round($invoice->item->price * ($invoice->item->dp_percentage / 100))
                        : $invoice->total_amount);

                $oldReceipt = $invoice->authentic_receipt;

                $path = $request->file('payment_proof')->store('receipts', 'public');

                $invoice->update([
                    'paid_amount'       => $paidAmount,
                    'authentic_receipt' => $path,
                    'payment_status'    => 'Pending Validation',
                    'status'            => 'Pending', // kembalikan ke Pending (jika sebelumnya Rejected)
                ]);

                // Hapus file bukti lama secara async jika ada (mencegah storage penuh dari file orphan)
                if ($oldReceipt && $oldReceipt !== $path) {
                    \App\Jobs\DeleteOldImageJob::dispatch($oldReceipt);
                }

                // Jika unit mobil saat ini Available (karena ditolak/alasan lain), ubah kembali menjadi Invoiced (Booked)
                if ($invoice->item) {
                    $rawStatus = $invoice->item->getRawOriginal('status');
                    if ($rawStatus === 'Available') {
                        $invoice->item->update(['status' => 'Invoiced']);
                    } elseif ($rawStatus === 'Sold') {
                        throw new \RuntimeException('Unit kendaraan ini sudah terjual ke pelanggan lain.');
                    }
                }
            });

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable) {
            return redirect()->back()->with('error', 'Gagal memproses unggah bukti transfer.');
        }
    }

    /**
     * Print-ready invoice view.
     * Access is restricted to the invoice owner verified via session.
     */
    public function invoice(Request $request, int $id): View
    {
        $booking = Invoice::with([
            'customer:id,name,phone',
            'item:id,brand,model,vin,year,price,engine,transmission',
        ])->findOrFail($id);

        // Jika user adalah admin/staf yang login, bypass verifikasi nomor HP pelanggan
        if (! auth()->check()) {
            $phone = session('track_phone_verified') ?? session('booking_phone_' . $id);

            if (! $phone || $booking->customer->phone !== $phone) {
                abort(403, 'AKSES DITOLAK. Kwitansi ini hanya dapat diakses oleh pemilik transaksi.');
            }
        }

        return view('invoice', compact('booking'));
    }

    /**
     * Halaman Tentang ShowDrive — informasi platform, tech stack, dan tim pengembang.
     */
    public function about(): View
    {
        return view('about');
    }

    /**
     * Customer membatalkan booking mereka sendiri.
     * Hanya bisa dilakukan jika invoice masih Unpaid + Pending
     * dan pelanggan terverifikasi via session OTP.
     */
    public function cancelBooking(Request $request, int $id): RedirectResponse
    {
        $phone = session('track_phone_verified');
        if (! $phone) {
            return redirect()->route('booking.track')
                ->withErrors(['error' => 'Sesi tidak valid. Silakan verifikasi OTP terlebih dahulu.']);
        }

        // Validasi cancellation_note
        $request->validate([
            'cancellation_note' => ['required', 'string', 'min:10', 'max:300'],
        ], [
            'cancellation_note.required' => 'Alasan pembatalan wajib diisi.',
            'cancellation_note.min'      => 'Alasan pembatalan minimal 10 karakter.',
            'cancellation_note.max'      => 'Alasan pembatalan maksimal 300 karakter.',
        ]);

        $invoice = Invoice::with('item:id,status', 'customer:id,phone')
            ->findOrFail($id);

        if ($invoice->customer->phone !== $phone) {
            abort(403, 'Anda tidak memiliki izin untuk membatalkan reservasi ini.');
        }

        if ($invoice->payment_status !== 'Unpaid' || $invoice->status !== 'Pending') {
            return redirect()->back()->with(
                'error',
                'Pembatalan tidak dapat dilakukan. Booking ini sudah dalam proses pembayaran atau telah diverifikasi. Hubungi admin untuk bantuan.'
            );
        }

        DB::transaction(function () use ($invoice, $request) {
            $invoice->update([
                'status'            => 'Cancelled',
                'payment_status'    => 'Cancelled',
                'cancellation_note' => $request->cancellation_note,
            ]);

            if ($invoice->item) {
                $invoice->item->update(['status' => 'Available']);
            }
        });

        return redirect()->route('booking.track')
            ->with('success', 'Reservasi ' . $invoice->invoice_code . ' berhasil dibatalkan.');
    }
}
