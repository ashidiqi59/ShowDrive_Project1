<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
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
}
