<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteOldImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     * Membatasi retry agar tidak loop tak terbatas pada file yang memang sudah terhapus.
     */
    public int $tries = 3;

    /**
     * Timeout maksimum per percobaan (detik).
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param  string  $filePath  Path relatif terhadap disk 'public' (contoh: 'cars/abc123.jpg')
     */
    public function __construct(
        public readonly string $filePath,
    ) {}

    /**
     * Execute the job.
     * Penghapusan file dilakukan secara asinkron di background worker,
     * sehingga HTTP thread tidak diblokir oleh operasi I/O disk.
     */
    public function handle(): void
    {
        // Guard: abaikan path kosong untuk mencegah Storage::delete('') yang merusak
        if (empty(trim($this->filePath))) {
            return;
        }

        try {
            if (Storage::disk('public')->exists($this->filePath)) {
                Storage::disk('public')->delete($this->filePath);

                Log::info('[DeleteOldImageJob] File berhasil dihapus.', [
                    'path' => $this->filePath,
                ]);
            } else {
                // File tidak ditemukan — anggap sudah bersih, tidak perlu retry
                Log::debug('[DeleteOldImageJob] File tidak ditemukan, dilewati.', [
                    'path' => $this->filePath,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[DeleteOldImageJob] Gagal menghapus file.', [
                'path'  => $this->filePath,
                'error' => $e->getMessage(),
            ]);

            // Lempar ulang agar queue worker tahu job ini gagal dan perlu di-retry
            throw $e;
        }
    }

    /**
     * Handle job yang sudah habis percobaan retry-nya.
     * Dicatat ke log agar bisa ditindaklanjuti secara manual (storage cleanup).
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('[DeleteOldImageJob] Job gagal permanen setelah semua retry habis.', [
            'path'  => $this->filePath,
            'error' => $exception->getMessage(),
        ]);
    }
}
