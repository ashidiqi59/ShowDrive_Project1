<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard analytics — 4 KPI cards resolved in 2 aggregated DB queries.
     */
    public function index(): View
    {
        $stats = DB::table('items')->selectRaw("
            COUNT(*) AS total_unit,
            SUM(CASE WHEN status = 'Sold' THEN 1 ELSE 0 END) AS unit_sold
        ")->first();

        $invoiceStats = DB::table('invoices')->selectRaw("
            SUM(CASE WHEN payment_status = 'Pending Validation' THEN 1 ELSE 0 END) AS pending_verification,
            SUM(CASE WHEN payment_status IN ('Paid','Down Payment') THEN paid_amount ELSE 0 END) AS total_revenue
        ")->first();

        return view('admin.dashboard', [
            'totalUnit'           => $stats->total_unit ?? 0,
            'unitSold'            => $stats->unit_sold ?? 0,
            'pendingVerification' => $invoiceStats->pending_verification ?? 0,
            'totalRevenue'        => $invoiceStats->total_revenue ?? 0,
        ]);
    }
}
