<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Warehouse;
use App\Models\Cashier;
use App\Models\Company;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan data ringkasan analitik dan tabel relasional.
     */
    public function dashboard(Request $request)
    {
        // ===== 4 Card Statistik Dashboard =====
        $totalUnit           = Item::count();
        $unitSold            = Item::where('status', 'Sold')->count();
        $pendingVerification = Invoice::where('payment_status', 'Pending Validation')->count();
        
        // Revenue dihitung dari semua pembayaran riil yang sudah disahkan (Lunas maupun Uang Muka / DP)
        $totalRevenue        = Invoice::whereIn('payment_status', ['Paid', 'Down Payment'])->sum('paid_amount');

        return view('admin.dashboard', compact(
            'totalUnit',
            'unitSold',
            'pendingVerification',
            'totalRevenue'
        ));
    }

    public function manageItems(Request $request)
    {
        $query = Item::with(['warehouse', 'images']);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }
        $cars = $query->get();
        $warehouses = Warehouse::all();
        return view('admin.items', compact('cars', 'warehouses'));
    }

    public function manageInvoices(Request $request)
    {
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
        return view('admin.invoices', compact('bookings', 'statusFilter'));
    }

    public function manageWarehouses()
    {
        $warehouses = Warehouse::with('company')->withCount('items')->get();
        $company    = Company::first();
        return view('admin.warehouses', compact('warehouses', 'company'));
    }

    public function manageCashiers()
    {
        $cashiers = Cashier::with('company')->get();
        $company  = Company::first();
        return view('admin.cashiers', compact('cashiers', 'company'));
    }

    public function manageProfile()
    {
        $company = Company::first();
        return view('admin.profile', compact('company'));
    }

    // =====================================================================
    // CRUD CARS (KENDARAAN)
    // =====================================================================

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
        unset($data['images']);
        $data['image_url'] = null; // Placeholder, akan diisi oleh gambar pertama

        if ($data['status'] === 'Booked') {
            $data['status'] = 'Invoiced';
        }

        $item = Item::create($data);

        // Upload tiap file SEKALI dan simpan sebagai ItemImage.
        // Gambar pertama juga menjadi image_url utama item.
        if ($request->hasFile('images')) {
            $primaryPathSet = false;
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public'); // Setiap file diupload tepat 1x
                ItemImage::create(['item_id' => $item->id, 'image_path' => $path]);

                if (!$primaryPathSet) {
                    $item->update(['image_url' => $path]); // Set gambar utama dari gambar pertama
                    $primaryPathSet = true;
                }
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
        unset($data['images']);

        if ($data['status'] === 'Booked') {
            $data['status'] = 'Invoiced';
        }

        $item->update($data);

        // Jika ada gambar baru: hapus lama, lalu upload baru (masing-masing 1x saja)
        if ($request->hasFile('images')) {
            foreach ($item->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
            }
            $item->images()->delete();

            $primaryPathSet = false;
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public'); // Setiap file diupload tepat 1x
                ItemImage::create(['item_id' => $item->id, 'image_path' => $path]);

                if (!$primaryPathSet) {
                    $item->update(['image_url' => $path]); // Update gambar utama
                    $primaryPathSet = true;
                }
            }
        }

        return redirect()->back()->with('success', 'Unit mobil berhasil diperbarui.');
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
            return redirect()->back()->with('error', 'Gagal hapus: Unit ini memiliki riwayat transaksi/invoice aktif.');
        }
    }

    // =====================================================================
    // VERIFIKASI PEMBAYARAN & INSPEKSI
    // =====================================================================

    /**
     * Mengesahkan pembayaran (Full-page redirect — legacy).
     */
    public function verifyPayment($invoice_id)
    {
        DB::transaction(function () use ($invoice_id) {
            $invoice = Invoice::findOrFail($invoice_id);
            $newPaymentStatus = $invoice->payment_type === 'Down Payment' ? 'Down Payment' : 'Paid';
            $invoice->update(['payment_status' => $newPaymentStatus, 'cashier_id' => Auth::id()]);
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
                $newPaymentStatus = $invoice->payment_type === 'Down Payment' ? 'Down Payment' : 'Paid';
                $invoice->update(['payment_status' => $newPaymentStatus, 'cashier_id' => Auth::id()]);
                if ($newPaymentStatus === 'Paid') {
                    $invoice->item->update(['status' => 'Sold']);
                }
                return ['new_payment_status' => $newPaymentStatus, 'invoice_id' => $invoice->id];
            });

            return response()->json([
                'success'            => true,
                'message'            => 'Pembayaran berhasil disahkan.',
                'new_payment_status' => $result['new_payment_status'],
                'invoice_id'         => $result['invoice_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memverifikasi pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memproses persetujuan/penolakan jadwal inspeksi (Full-page redirect — legacy).
     */
    public function processInspection(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Approved,Rejected']);
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
        $request->validate(['status' => 'required|in:Approved,Rejected']);

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
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status: ' . $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // CRUD WAREHOUSE (GUDANG)
    // =====================================================================

    /**
     * Menyimpan gudang baru.
     */
    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'location'   => 'required|string|max:255',
        ]);

        Warehouse::create($request->only('company_id', 'name', 'location'));
        return redirect()->back()->with('success', 'Lokasi gudang berhasil ditambahkan.');
    }

    /**
     * Memperbarui data gudang.
     */
    public function updateWarehouse(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'location'   => 'required|string|max:255',
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($request->only('company_id', 'name', 'location'));
        return redirect()->back()->with('success', 'Data gudang berhasil diperbarui.');
    }

    /**
     * Menghapus gudang (dijaga oleh relasi ke items via ON DELETE CASCADE dari warehouse->items).
     */
    public function deleteWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            if ($warehouse->items()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal hapus: Gudang ini masih memiliki ' . $warehouse->items()->count() . ' unit kendaraan yang terdaftar.');
            }
            $warehouse->delete();
            return redirect()->back()->with('success', 'Gudang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal hapus gudang: ' . $e->getMessage());
        }
    }

    // =====================================================================
    // CRUD CASHIER (KASIR / STAF)
    // =====================================================================

    /**
     * Menyimpan akun kasir baru.
     */
    public function storeCashier(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:cashiers,username',
            'password'   => 'required|string|min:8',
            'role'       => 'required|string|max:100',
        ]);

        Cashier::create([
            'company_id' => $request->company_id,
            'name'       => $request->name,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    /**
     * Memperbarui data kasir (password opsional — kosong = tidak diubah).
     */
    public function updateCashier(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:cashiers,username,' . $id,
            'password'   => 'nullable|string|min:8',
            'role'       => 'required|string|max:100',
        ]);

        $cashier = Cashier::findOrFail($id);
        $data = $request->only('company_id', 'name', 'username', 'role');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cashier->update($data);
        return redirect()->back()->with('success', 'Data kasir berhasil diperbarui.');
    }

    /**
     * Menghapus akun kasir (tidak bisa hapus diri sendiri).
     */
    public function deleteCashier($id)
    {
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun yang sedang aktif digunakan.');
        }

        try {
            $cashier = Cashier::findOrFail($id);
            $cashier->delete();
            return redirect()->back()->with('success', 'Akun kasir berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus akun kasir: ' . $e->getMessage());
        }
    }

    // =====================================================================
    // PROFIL PERUSAHAAN (COMPANY)
    // =====================================================================

    /**
     * Memperbarui profil perusahaan showroom.
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'tax_id'  => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:50',
        ]);

        $company = Company::first();

        if (!$company) {
            return redirect()->back()->with('error', 'Data perusahaan tidak ditemukan.');
        }

        $company->update($request->only('name', 'tax_id', 'address', 'phone'));
        return redirect()->back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    // =====================================================================
    // LAPORAN KEUANGAN
    // =====================================================================

    /**
     * Menampilkan halaman laporan keuangan komprehensif dengan filter periode dan status.
     */
    public function laporan(Request $request)
    {
        $bulan  = $request->query('bulan');
        $tahun  = $request->query('tahun', now()->year);
        $status = $request->query('status', 'all');

        $query = Invoice::with(['customer', 'item.warehouse', 'cashier'])->latest();

        if ($bulan) {
            $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        } elseif ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        $invoices = $query->get();

        // Statistik rangkuman untuk kartu laporan
        $totalTransaksi  = $invoices->count();
        $totalPendapatan = $invoices->where('payment_status', 'Paid')->sum('paid_amount');
        $totalDP         = $invoices->where('payment_status', 'Down Payment')->sum('paid_amount');
        $totalPending    = $invoices->where('payment_status', 'Pending Validation')->count();
        $unitTerjual     = $invoices->where('payment_status', 'Paid')->count();

        $company = Company::first();

        return view('admin.laporan', compact(
            'invoices',
            'totalTransaksi',
            'totalPendapatan',
            'totalDP',
            'totalPending',
            'unitTerjual',
            'company',
            'bulan',
            'tahun',
            'status'
        ));
    }
}