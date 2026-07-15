<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupStorage extends Command
{
    protected $signature   = 'backup:storage';
    protected $description = 'Backup semua file upload (storage/app/public) ke direktori backup terpisah dengan timestamp';

    public function handle(): int
    {
        $source      = storage_path('app/public');
        $timestamp   = now()->format('Y-m-d_H-i-s');
        $destination = storage_path('app/backups/storage_' . $timestamp);

        // Pastikan direktori backup ada
        if (! File::isDirectory(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        // Pastikan source directory ada
        if (! File::isDirectory($source)) {
            $this->warn('Direktori storage/app/public tidak ditemukan. Tidak ada yang di-backup.');
            return self::SUCCESS;
        }

        // Hitung total file sebelum backup
        $files = File::allFiles($source);
        $count = count($files);

        if ($count === 0) {
            $this->info('Tidak ada file di storage/app/public. Backup dilewati.');
            return self::SUCCESS;
        }

        // Salin seluruh direktori ke destination
        File::copyDirectory($source, $destination);

        // Rotasi backup: hapus backup lama jika melebihi 5 backup terakhir
        $backups = collect(File::directories(storage_path('app/backups')))
            ->sort()
            ->values();

        $maxBackups = 5;
        if ($backups->count() > $maxBackups) {
            $toDelete = $backups->slice(0, $backups->count() - $maxBackups);
            foreach ($toDelete as $oldBackup) {
                File::deleteDirectory($oldBackup);
                $this->line("Backup lama dihapus: {$oldBackup}");
            }
        }

        $this->info("Backup berhasil: {$count} file disalin ke storage/app/backups/storage_{$timestamp}");

        return self::SUCCESS;
    }
}
