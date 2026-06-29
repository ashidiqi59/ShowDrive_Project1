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
                'after_or_equal:today',
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
            'date.after_or_equal'     => 'Tanggal inspeksi tidak boleh sebelum hari ini.',
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

        DB::transaction(function () use ($request, $item) {
            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone],
                ['name' => $request->customer_name]
            );

            // Generate unique invoice code
            $invoiceCode = 'SD-INV-' . strtoupper(Str::random(8));

            Invoice::create([
                'invoice_code'     => $invoiceCode,
                'customer_id'      => $customer->id,
                'item_id'          => $item->id,
                'cashier_id'       => null,
                'date'             => $request->date,
                'total_amount'     => $item->price,
                'paid_amount'      => 0,
                'payment_type'     => 'None',
                'payment_status'   => 'Unpaid',
                'status'           => 'Pending',
                'authentic_receipt' => null,
            ]);

            // Update item status to Invoiced (ditampilkan sebagai 'Booked' di UI)
            $item->update(['status' => 'Invoiced']);
        });

        return redirect()->route('booking.track', ['phone' => $request->phone])
            ->with('success', 'Booking berhasil dibuat! Silakan lakukan transfer dan unggah bukti transfer pembayaran di bawah.');
    }


    /**
     * Pelacakan status pembayaran & jadwal inspeksi
     */
    public function track(Request $request)
    {
        $phone = $request->query('phone');
        $bookings = collect();

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
     * Mengunggah bukti bayar (manual receipt)
     */
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'required|in:Down Payment,Paid',
            'paid_amount' => 'required|numeric|min:0',
            'payment_proof' => 'required|image|max:2048',
        ]);

        $invoice = Invoice::findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('receipts', 'public');
            
            $invoice->update([
                'payment_type' => $request->payment_type,
                'paid_amount' => $request->paid_amount,
                'authentic_receipt' => $path,
                'payment_status' => 'Pending Validation',
            ]);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Tampilan Kwitansi Cetak
     */
    public function invoice($id)
    {
        $booking = Invoice::with(['customer', 'item'])->findOrFail($id);
        return view('invoice', compact('booking'));
    }
}