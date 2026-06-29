<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Warehouse;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan data ringkasan analitik dan tabel relasional.
     */
    public function dashboard(Request $request)
    {
        $query = Item::with(['warehouse', 'images']);

        // Pencarian mobil di dashboard
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        $cars = $query->get();

        // Filter transaksi berdasarkan payment_status
        $statusFilter = $request->query('status_filter', 'all');
        $bookingQuery = Invoice::with(['customer', 'item', 'cashier'])->latest();

        if ($statusFilter === 'pending') {
            $bookingQuery->where('payment_status', 'Pending Validation');
        } elseif ($statusFilter === 'verified') {
            $bookingQuery->where('payment_status', 'Down Payment');
        } elseif ($statusFilter === 'paid') {
            $bookingQuery->where('payment_status', 'Paid');
        }

        $bookings = $bookingQuery->get();

        // ===== 4 Card Statistik Dashboard =====
        // 1. Total Unit — seluruh unit di inventaris
        $totalUnit = Item::count();

        // 2. Unit Sold — unit yang sudah terjual lunas
        $unitSold = Item::where('status', 'Sold')->count();

        // 3. Pending Verification — invoice yang sudah upload bukti tapi belum disahkan
        $pendingVerification = Invoice::where('payment_status', 'Pending Validation')->count();

        // 4. Total Revenue — total dana masuk dari invoice yang sudah Paid
        $totalRevenue = Invoice::where('payment_status', 'Paid')->sum('paid_amount');

        // Mengambil daftar gudang untuk form input/edit mobil
        $warehouses = Warehouse::all();

        return view('admin.dashboard', compact(
            'cars',
            'bookings',
            'totalUnit',
            'unitSold',
            'pendingVerification',
            'totalRevenue',
            'warehouses',
            'statusFilter'
        ));
    }

    /**
     * Menyimpan unit mobil baru.
     */
    public function storeCar(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'brand'        => 'required|string',
            'model'        => 'required|string',
            'vin'          => 'required|string|size:17|unique:items,vin',
            'price'        => 'required|numeric|min:0',
            'year'         => 'required|integer',
            'color'        => 'required|string',
            'engine'       => 'required|string',
            'transmission' => 'required|string',
            'images'       => 'required|array|min:1',
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status'       => 'required|in:Available,Booked,Sold'
        ]);

        $data = $validated;

        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
            if (count($uploadedFiles) > 0) {
                // Simpan foto pertama sebagai foto utama
                $primaryPath = $uploadedFiles[0]->store('cars', 'public');
                $data['image_url'] = $primaryPath;
            }
        }
        unset($data['images']);

        // Pemetaan status Booked di UI ke Invoiced di Database
        if ($data['status'] === 'Booked') {
            $data['status'] = 'Invoiced';
        }

        $item = Item::create($data);

        // Simpan semua gambar (maks 5) ke tabel item_images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public');
                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Unit mobil berhasil ditambahkan.');
    }

    /**
     * Memperbarui unit mobil.
     */
    public function updateCar(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'brand'        => 'required|string',
            'model'        => 'required|string',
            'vin'          => 'required|string|size:17|unique:items,vin,' . $id,
            'price'        => 'required|numeric|min:0',
            'year'         => 'required|integer',
            'color'        => 'required|string',
            'engine'       => 'required|string',
            'transmission' => 'required|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status'       => 'required|in:Available,Booked,Sold'
        ]);

        $data = $validated;

        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
            if (count($uploadedFiles) > 0) {
                // Simpan foto utama baru
                $primaryPath = $uploadedFiles[0]->store('cars', 'public');
                $data['image_url'] = $primaryPath;
            }
        }
        unset($data['images']);

        // Pemetaan status Booked di UI ke Invoiced di Database
        if ($data['status'] === 'Booked') {
            $data['status'] = 'Invoiced';
        }

        $item->update($data);

        // Jika ada upload gambar baru, hapus semua gambar lama dan file-file di storage
        if ($request->hasFile('images')) {
            // Hapus file fisik gambar-gambar lama
            foreach ($item->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
            }
            // Hapus record lama dari database
            $item->images()->delete();

            // Simpan gambar-gambar baru ke storage dan database
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public');
                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Unit mobil berhasil diperbarui.');
    }

    /**
     * Mengesahkan pembayaran (Validasi Finansial Kasir) — Full-page redirect (legacy).
     */
    public function verifyPayment($invoice_id)
    {
        DB::transaction(function () use ($invoice_id) {
            $invoice = Invoice::findOrFail($invoice_id);

            $newPaymentStatus = 'Paid';
            if ($invoice->payment_type === 'Down Payment') {
                $newPaymentStatus = 'Down Payment';
            }

            $invoice->update([
                'payment_status' => $newPaymentStatus,
                'cashier_id'     => Auth::id()
            ]);

            if ($newPaymentStatus === 'Paid') {
                $invoice->item->update(['status' => 'Sold']);
            }
        });

        return redirect()->back()->with('success', 'Pembayaran berhasil disahkan.');
    }

    /**
     * Mengesahkan pembayaran via AJAX — mengembalikan JSON response.
     */
    public function verifyPaymentAjax(Request $request, $invoice_id)
    {
        try {
            $result = DB::transaction(function () use ($invoice_id) {
                $invoice = Invoice::with('item')->findOrFail($invoice_id);

                $newPaymentStatus = 'Paid';
                if ($invoice->payment_type === 'Down Payment') {
                    $newPaymentStatus = 'Down Payment';
                }

                $invoice->update([
                    'payment_status' => $newPaymentStatus,
                    'cashier_id'     => Auth::id()
                ]);

                if ($newPaymentStatus === 'Paid') {
                    $invoice->item->update(['status' => 'Sold']);
                }

                return [
                    'new_payment_status' => $newPaymentStatus,
                    'invoice_id'         => $invoice->id,
                ];
            });

            return response()->json([
                'success'            => true,
                'message'            => 'Pembayaran berhasil disahkan.',
                'new_payment_status' => $result['new_payment_status'],
                'invoice_id'         => $result['invoice_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memproses persetujuan/penolakan jadwal inspeksi — Full-page redirect (legacy).
     */
    public function processInspection(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => $request->status]);

        if ($request->status === 'Rejected') {
            $invoice->item->update(['status' => 'Available']);
        }

        return redirect()->back()->with('success', 'Status inspeksi berhasil diperbarui.');
    }

    /**
     * Memproses persetujuan/penolakan jadwal inspeksi via AJAX — mengembalikan JSON response.
     */
    public function processInspectionAjax(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        try {
            $invoice = Invoice::with('item')->findOrFail($id);
            $invoice->update(['status' => $request->status]);

            if ($request->status === 'Rejected') {
                $invoice->item->update(['status' => 'Available']);
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Status inspeksi berhasil diperbarui.',
                'new_status' => $request->status,
                'invoice_id' => $id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus unit mobil (dijaga oleh batasan ON DELETE RESTRICT).
     */
    public function deleteCar($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();
            return redirect()->back()->with('success', 'Unit berhasil dihapus.');
        } catch (\Exception $e) {
            // Ini akan dieksekusi jika terjadi kegagalan akibat foreign key constraint ON DELETE RESTRICT
            return redirect()->back()->with('error', 'Gagal hapus: Unit ini memiliki riwayat transaksi/invoice aktif.');
        }
    }
}