<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status_filter', 'all');

        $query = Invoice::with([
            'customer:id,name,phone',
            'item:id,brand,model,vin,price,image_url',
            'cashier:id,name',
        ])->latest();

        // WARN-03 fix: if-elseif lebih semantik untuk side-effect pada query builder
        if ($statusFilter === 'pending') {
            $query->where('payment_status', 'Pending Validation');
        } elseif ($statusFilter === 'verified') {
            $query->where('payment_status', 'Down Payment');
        } elseif ($statusFilter === 'paid') {
            $query->where('payment_status', 'Paid');
        } elseif ($statusFilter === 'Cancelled') {
            $query->where('payment_status', 'Cancelled');
        }
        // 'all' — no filter applied

        // WARN-04 fix: hitung summary badge dari seluruh DB tanpa filter
        $summaryStats = Invoice::selectRaw("
            SUM(CASE WHEN payment_status = 'Pending Validation' THEN 1 ELSE 0 END) AS pending_count,
            SUM(CASE WHEN payment_status = 'Down Payment'       THEN 1 ELSE 0 END) AS verified_count,
            SUM(CASE WHEN payment_status = 'Paid'               THEN 1 ELSE 0 END) AS paid_count
        ")->first();

        $bookings = $query->paginate(20)->withQueryString();

        return view('admin.invoices', compact('bookings', 'statusFilter'))
            ->with('summaryPending',  $summaryStats->pending_count  ?? 0)
            ->with('summaryVerified', $summaryStats->verified_count ?? 0)
            ->with('summaryPaid',     $summaryStats->paid_count     ?? 0);
    }

    public function verifyAjax(Request $request, int $id): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($id) {
                $invoice = Invoice::with('item:id,status')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($invoice->payment_status !== 'Pending Validation') {
                    throw new \RuntimeException('Invoice ini sudah diproses sebelumnya.');
                }

                $newStatus = ($invoice->paid_amount == $invoice->total_amount)
                    ? 'Paid'
                    : ($invoice->payment_type === 'Down Payment'
                        ? 'Down Payment'
                        : 'Paid');

                $invoice->update([
                    'payment_status' => $newStatus,
                    'cashier_id'     => Auth::id(),
                ]);

                if ($newStatus === 'Paid') {
                    $invoice->item->update(['status' => 'Sold']);
                }

                return ['new_payment_status' => $newStatus, 'invoice_id' => $invoice->id];
            });

            return response()->json([
                'success'            => true,
                'message'            => 'Pembayaran berhasil disahkan.',
                'new_payment_status' => $result['new_payment_status'],
                'invoice_id'         => $result['invoice_id'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal memverifikasi pembayaran.'], 500);
        }
    }

    public function inspectionAjax(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:Approved,Rejected']);

        try {
            DB::transaction(function () use ($request, $id) {
                $invoice = Invoice::with('item:id,status')->lockForUpdate()->findOrFail($id);
                $invoice->update(['status' => $request->status]);

                if ($request->status === 'Rejected') {
                    $invoice->item->update(['status' => 'Available']);
                }
            });

            return response()->json([
                'success'    => true,
                'message'    => 'Status inspeksi berhasil diperbarui.',
                'new_status' => $request->status,
                'invoice_id' => $id,
            ]);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status.'], 500);
        }
    }

    public function cancelAjax(Request $request, int $id): JsonResponse
    {
        try {
            $note = trim($request->input('cancellation_note', ''));

            if (strlen($note) < 5) {
                return response()->json(['success' => false, 'message' => 'Alasan pembatalan wajib diisi (minimal 5 karakter).'], 422);
            }

            DB::transaction(function () use ($id, $note) {
                $invoice = Invoice::with('item:id,status')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($invoice->payment_status === 'Cancelled') {
                    throw new \RuntimeException('Invoice sudah dalam status Cancelled.');
                }

                if ($invoice->handed_over_at !== null) {
                    throw new \RuntimeException('Invoice tidak dapat dibatalkan setelah unit diserahterimakan kepada pelanggan.');
                }

                $invoice->update([
                    'status'            => 'Cancelled',
                    'payment_status'    => 'Cancelled',
                    'paid_amount'       => 0,
                    'cancellation_note' => 'Admin: ' . $note,
                ]);

                // Kembalikan unit ke Available dari status apapun (Invoiced/Sold)
                if ($invoice->item) {
                    $itemStatus = $invoice->item->getRawOriginal('status');
                    if (in_array($itemStatus, ['Invoiced', 'Sold'], true)) {
                        $invoice->item->update(['status' => 'Available']);
                    }
                }
            });

            return response()->json([
                'success'    => true,
                'message'    => 'Invoice berhasil dibatalkan oleh admin. Unit dikembalikan ke status Available.',
                'invoice_id' => $id,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan invoice.'], 500);
        }
    }

    // =====================================================================
    // APPROVE: Setujui bukti bayar + inspeksi dalam satu aksi atomik
    // =====================================================================

    public function approveAjax(int $id): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($id) {
                $invoice = Invoice::with([
                    'item:id,status,brand,model',
                    'customer:id,name,phone',
                ])->lockForUpdate()->findOrFail($id);

                if ($invoice->payment_status !== 'Pending Validation') {
                    throw new \RuntimeException('Invoice ini tidak dalam status Pending Validation.');
                }

                $newPaymentStatus = ($invoice->paid_amount == $invoice->total_amount)
                    ? 'Paid'
                    : ($invoice->payment_type === 'Down Payment'
                        ? 'Down Payment'
                        : 'Paid');

                $invoice->update([
                    'payment_status' => $newPaymentStatus,
                    'status'         => 'Approved',
                    'cashier_id'     => Auth::id(),
                    'rejection_note' => null,
                ]);

                if ($newPaymentStatus === 'Paid') {
                    $invoice->item->update(['status' => 'Sold']);
                }

                return [
                    'invoice_id'         => $invoice->id,
                    'new_payment_status' => $newPaymentStatus,
                    'customer_name'      => $invoice->customer?->name,
                    'customer_phone'     => $invoice->customer?->phone,
                    'invoice_code'       => $invoice->invoice_code,
                    'unit_name'          => trim(($invoice->item?->brand ?? '') . ' ' . ($invoice->item?->model ?? '')),
                ];
            });

            return response()->json([
                'success'            => true,
                'message'            => 'Pembayaran & jadwal inspeksi berhasil disetujui.',
                'invoice_id'         => $result['invoice_id'],
                'new_payment_status' => $result['new_payment_status'],
                'customer_name'      => $result['customer_name'],
                'customer_phone'     => $result['customer_phone'],
                'invoice_code'       => $result['invoice_code'],
                'unit_name'          => $result['unit_name'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui invoice.'], 500);
        }
    }

    // =====================================================================
    // REJECT: Tolak bukti bayar dengan alasan wajib + audit trail
    // =====================================================================

    public function rejectAjax(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rejection_note' => 'required|string|min:10|max:500',
        ], [
            'rejection_note.required' => 'Alasan penolakan wajib diisi.',
            'rejection_note.min'      => 'Alasan minimal 10 karakter.',
            'rejection_note.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        try {
            $result = DB::transaction(function () use ($request, $id) {
                $invoice = Invoice::with([
                    'item:id,status,brand,model',
                    'customer:id,name,phone',
                ])->lockForUpdate()->findOrFail($id);

                if ($invoice->payment_status !== 'Pending Validation') {
                    throw new \RuntimeException('Invoice ini tidak dalam status Pending Validation.');
                }

                $invoice->update([
                    'payment_status' => 'Unpaid',
                    'status'         => 'Rejected',
                    'rejection_note' => $request->rejection_note,
                    'paid_amount'    => 0,
                    'cashier_id'     => Auth::id(),
                ]);

                // Kembalikan unit ke Available agar bisa dibooking ulang
                if ($invoice->item) {
                    $invoice->item->update(['status' => 'Available']);
                }

                return [
                    'invoice_id'     => $invoice->id,
                    'customer_name'  => $invoice->customer?->name,
                    'customer_phone' => $invoice->customer?->phone,
                    'invoice_code'   => $invoice->invoice_code,
                    'unit_name'      => trim(($invoice->item?->brand ?? '') . ' ' . ($invoice->item?->model ?? '')),
                    'rejection_note' => $request->rejection_note,
                ];
            });

            return response()->json([
                'success'        => true,
                'message'        => 'Bukti pembayaran ditolak. Pelanggan perlu upload ulang.',
                'invoice_id'     => $result['invoice_id'],
                'customer_name'  => $result['customer_name'],
                'customer_phone' => $result['customer_phone'],
                'invoice_code'   => $result['invoice_code'],
                'unit_name'      => $result['unit_name'],
                'rejection_note' => $result['rejection_note'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal menolak invoice.'], 500);
        }
    }

    // =====================================================================
    // EDIT PELANGGAN: Koreksi nama & NIK customer (admin-only, semua status kecuali Cancelled)
    // =====================================================================

    public function updateCustomerAjax(Request $request, int $id): JsonResponse
    {
        try {
            $invoice = Invoice::select('id', 'customer_id', 'payment_status', 'handed_over_at')
                ->findOrFail($id);

            if ($invoice->payment_status === 'Cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak dapat diubah pada invoice yang sudah dibatalkan.',
                ], 422);
            }

            if ($invoice->handed_over_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak dapat diubah setelah unit diserahterimakan kepada pelanggan.',
                ], 422);
            }

            $customer = Customer::findOrFail($invoice->customer_id);

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\.\x27\-]+$/u'],
                'nik'  => ['nullable', 'digits:16', 'unique:customers,nik,' . $customer->id],
            ], [
                'name.required' => 'Nama pelanggan wajib diisi.',
                'name.min'      => 'Nama minimal 3 karakter.',
                'name.regex'    => 'Nama hanya boleh mengandung huruf dan spasi.',
                'nik.digits'    => 'NIK harus tepat 16 digit angka.',
                'nik.unique'    => 'NIK ini sudah terdaftar pada akun pelanggan lain.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            DB::transaction(function () use ($request, $customer) {
                $customer->update([
                    'name' => trim($request->name),
                    'nik'  => $request->filled('nik') ? $request->nik : $customer->nik,
                ]);
            });

            return response()->json([
                'success'  => true,
                'message'  => 'Data pelanggan berhasil diperbarui.',
                'new_name' => $customer->fresh()->name,
                'new_nik'  => $customer->fresh()->nik,
            ]);
        } catch (UniqueConstraintViolationException) {
            return response()->json(['success' => false, 'message' => 'NIK sudah terdaftar pada akun pelanggan lain.'], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data pelanggan.'], 500);
        }
    }

    // =====================================================================
    // AMEND RESERVASI: Ubah tanggal inspeksi & tipe pembayaran (semua status kecuali Cancelled)
    // =====================================================================

    public function amendAjax(Request $request, int $id): JsonResponse
    {
        try {
            $invoice = Invoice::with('item:id,price,dp_percentage')
                ->select('id', 'payment_status', 'status', 'item_id', 'subtotal', 'tax_rate', 'payment_type', 'date', 'handed_over_at')
                ->findOrFail($id);

            if ($invoice->payment_status === 'Cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi yang sudah dibatalkan tidak dapat diubah.',
                ], 422);
            }

            if ($invoice->payment_status === 'Paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi yang sudah lunas (Paid) tidak dapat diubah.',
                ], 422);
            }

            if ($invoice->handed_over_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak dapat diubah setelah unit diserahterimakan kepada pelanggan.',
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'date'         => ['required', 'date', 'after_or_equal:tomorrow', 'before_or_equal:+7 days'],
                'payment_type' => ['required', 'in:Down Payment,Paid'],
            ], [
                'date.required'          => 'Tanggal inspeksi wajib diisi.',
                'date.after_or_equal'    => 'Tanggal inspeksi paling cepat adalah besok.',
                'date.before_or_equal'   => 'Tanggal inspeksi paling lambat adalah 7 hari ke depan.',
                'payment_type.required'  => 'Tipe pembayaran wajib dipilih.',
                'payment_type.in'        => 'Tipe pembayaran tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $result = DB::transaction(function () use ($request, $invoice) {
                // Hitung ulang total jika payment_type berubah
                $subtotal  = (float) $invoice->subtotal;
                $taxRate   = (float) $invoice->tax_rate;
                $taxAmount = round($subtotal * ($taxRate / 100));
                $total     = $subtotal + $taxAmount;

                $invoice->update([
                    'date'         => $request->date,
                    'payment_type' => $request->payment_type,
                    'tax_amount'   => $taxAmount,
                    'total_amount' => $total,
                ]);

                return [
                    'new_date'         => $invoice->fresh()->date->format('d M Y'),
                    'new_payment_type' => $request->payment_type,
                    'new_total_amount' => number_format($total, 0, ',', '.'),
                ];
            });

            return response()->json([
                'success'          => true,
                'message'          => 'Reservasi berhasil diperbarui.',
                'new_date'         => $result['new_date'],
                'new_payment_type' => $result['new_payment_type'],
                'new_total_amount' => $result['new_total_amount'],
            ]);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui reservasi.'], 500);
        }
    }

    // =====================================================================
    // CONFIRM HANDOVER: Catat serah terima fisik unit (wajib Paid)
    // =====================================================================
    public function confirmHandover(int $id): JsonResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $invoice = Invoice::lockForUpdate()->findOrFail($id);

                if ($invoice->payment_status !== 'Paid') {
                    throw new \RuntimeException('Unit kendaraan tidak dapat diserahterimakan karena belum lunas.');
                }

                if ($invoice->handed_over_at !== null) {
                    throw new \RuntimeException('Unit kendaraan sudah diserahterimakan sebelumnya.');
                }

                $invoice->update([
                    'handed_over_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Serah terima unit berhasil dikonfirmasi.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Gagal melakukan serah terima unit.'], 500);
        }
    }
}
