<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $bulan  = $request->query('bulan');
        $tahun  = $request->query('tahun', now()->year);
        $status = $request->query('status', 'all');

        $query = Invoice::with([
            'customer:id,name,phone',
            'item:id,brand,model,vin',
            'cashier:id,name',
        ])->latest();

        if ($bulan) {
            $query->whereMonth('created_at', (int) $bulan)
                  ->whereYear('created_at', (int) $tahun);
        } else {
            $query->whereYear('created_at', (int) $tahun);
        }

        $allowedStatuses = ['Unpaid', 'Pending Validation', 'Down Payment', 'Paid', 'Cancelled'];
        if ($status !== 'all' && in_array($status, $allowedStatuses, true)) {
            $query->where('payment_status', $status);
        }

        // Pagination 50 per halaman — mencegah semua baris dimuat ke memori sekaligus
        $invoices = $query->paginate(50)->withQueryString();

        // Stats dihitung dari DB secara terpisah agar akurat meski data dipaginasi
        $statsQuery = Invoice::query();
        if ($bulan) {
            $statsQuery->whereMonth('created_at', (int) $bulan)->whereYear('created_at', (int) $tahun);
        } else {
            $statsQuery->whereYear('created_at', (int) $tahun);
        }
        if ($status !== 'all' && in_array($status, $allowedStatuses, true)) {
            $statsQuery->where('payment_status', $status);
        }

        $stats = $statsQuery->selectRaw("
            COUNT(*) AS total_transaksi,
            SUM(CASE WHEN payment_status = 'Paid' THEN paid_amount ELSE 0 END) AS total_pendapatan,
            SUM(CASE WHEN payment_status = 'Down Payment' THEN paid_amount ELSE 0 END) AS total_dp,
            SUM(CASE WHEN payment_status = 'Pending Validation' THEN 1 ELSE 0 END) AS total_pending,
            SUM(CASE WHEN payment_status = 'Paid' THEN 1 ELSE 0 END) AS unit_terjual
        ")->first();

        $totalTransaksi  = $stats->total_transaksi ?? 0;
        $totalPendapatan = $stats->total_pendapatan ?? 0;
        $totalDP         = $stats->total_dp ?? 0;
        $totalPending    = $stats->total_pending ?? 0;
        $unitTerjual     = $stats->unit_terjual ?? 0;

        return view('admin.laporan', compact(
            'invoices',
            'totalTransaksi',
            'totalPendapatan',
            'totalDP',
            'totalPending',
            'unitTerjual',
            'bulan',
            'tahun',
            'status',
        ) + ['company' => Company::first()]);
    }

    /**
     * Export laporan keuangan ke format CSV.
     * Menggunakan generator / streaming agar tidak memuat semua baris ke memori sekaligus.
     */
    public function exportCsv(Request $request): Response
    {
        $bulan  = $request->query('bulan');
        $tahun  = $request->query('tahun', now()->year);
        $status = $request->query('status', 'all');

        $query = Invoice::with([
            'customer:id,name,phone',
            'item:id,brand,model,vin',
            'cashier:id,name',
        ])->latest();

        if ($bulan) {
            $query->whereMonth('created_at', (int) $bulan)
                  ->whereYear('created_at', (int) $tahun);
        } else {
            $query->whereYear('created_at', (int) $tahun);
        }

        $allowedStatuses = ['Unpaid', 'Pending Validation', 'Down Payment', 'Paid', 'Cancelled'];
        if ($status !== 'all' && in_array($status, $allowedStatuses, true)) {
            $query->where('payment_status', $status);
        }

        // Nama file mencerminkan filter yang aktif
        $periodLabel = $bulan
            ? \Carbon\Carbon::create()->month((int) $bulan)->format('m') . '-' . $tahun
            : $tahun;
        $statusLabel = $status !== 'all' ? '_' . str_replace(' ', '-', strtolower($status)) : '';
        $filename = "showdrive_laporan_{$periodLabel}{$statusLabel}_" . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        // Stream CSV langsung ke response tanpa buffer memori besar
        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel membaca karakter Indonesia dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, [
                'No.',
                'Kode Invoice',
                'Tanggal Dibuat',
                'Tanggal Inspeksi',
                'Nama Pelanggan',
                'No. HP Pelanggan',
                'Brand Kendaraan',
                'Model Kendaraan',
                'VIN',
                'Tipe Pembayaran',
                'Subtotal (IDR)',
                'PPN (%)',
                'Pajak (IDR)',
                'Total (IDR)',
                'Nominal Dibayar (IDR)',
                'Status Pembayaran',
                'Status Inspeksi',
                'Disahkan Oleh',
                'Serah Terima',
            ], ';');

            $no = 1;
            // chunk(200) — ambil 200 baris sekaligus, bukan semua sekaligus
            $query->chunk(200, function ($invoices) use ($handle, &$no) {
                foreach ($invoices as $inv) {
                    fputcsv($handle, [
                        $no++,
                        $inv->invoice_code,
                        $inv->created_at?->format('d/m/Y H:i'),
                        $inv->date?->format('d/m/Y'),
                        $inv->customer?->name ?? '—',
                        $inv->customer?->phone ?? '—',
                        $inv->item?->brand ?? '—',
                        $inv->item?->model ?? '—',
                        $inv->item?->vin ?? '—',
                        $inv->payment_type,
                        number_format((float) $inv->subtotal, 0, ',', ''),
                        number_format((float) $inv->tax_rate, 2, ',', ''),
                        number_format((float) $inv->tax_amount, 0, ',', ''),
                        number_format((float) $inv->total_amount, 0, ',', ''),
                        number_format((float) $inv->paid_amount, 0, ',', ''),
                        $inv->payment_status,
                        $inv->status,
                        $inv->cashier?->name ?? '—',
                        $inv->handed_over_at?->format('d/m/Y H:i') ?? '—',
                    ], ';');
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
