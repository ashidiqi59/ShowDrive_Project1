<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    protected $signature   = 'bookings:cancel-expired';
    protected $description = 'Auto-cancel booking Unpaid yang tidak ada bukti bayar dalam 24 jam';

    public function handle(): int
    {
        // Ambil semua ID booking yang kadaluarsa — satu query ringan
        $expiredIds = Invoice::where('payment_status', 'Unpaid')
            ->where('status', 'Pending')
            ->where('created_at', '<', now()->subHours(24))
            ->pluck('id');

        if ($expiredIds->isEmpty()) {
            $this->info('Tidak ada booking kadaluarsa yang perlu dibatalkan.');
            return self::SUCCESS;
        }

        // Satu batch transaction untuk semua — jauh lebih efisien dari N transaksi terpisah
        // Sebelumnya: membuka dan menutup N transaksi DB terpisah (1 per invoice)
        // Sekarang: satu transaksi tunggal dengan 2 batch UPDATE query
        DB::transaction(function () use ($expiredIds) {
            // Batch UPDATE 1: batalkan semua invoice sekaligus
            Invoice::whereIn('id', $expiredIds)->update([
                'status'         => 'Cancelled',
                'payment_status' => 'Cancelled',
            ]);

            // Batch UPDATE 2: kembalikan unit ke Available
            // Hanya unit yang masih berstatus Invoiced yang perlu di-reset
            $itemIds = Invoice::whereIn('id', $expiredIds)->pluck('item_id');

            Item::whereIn('id', $itemIds)
                ->where('status', 'Invoiced')
                ->update(['status' => 'Available']);
        });

        $this->info("Berhasil membatalkan {$expiredIds->count()} booking kadaluarsa.");
        return self::SUCCESS;
    }
}
