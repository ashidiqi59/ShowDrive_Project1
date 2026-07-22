<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature   = 'admin:reset-password
                                {--email= : Email akun admin yang ingin direset (opsional, jika tidak diisi akan menampilkan daftar)}';

    protected $description = 'Reset password akun admin/kasir secara aman via terminal (tanpa email)';

    public function handle(): int
    {
        $this->info('');
        $this->line('  <fg=yellow;options=bold>ShowDrive — Admin Password Reset</>');
        $this->line('  ─────────────────────────────────');
        $this->info('');

        // ── 1. Pilih akun ───────────────────────────────────────────────
        $email = $this->option('email');

        if (! $email) {
            // Tampilkan semua akun user yang ada
            $users = User::select('id', 'name', 'email', 'role')->orderBy('name')->get();

            if ($users->isEmpty()) {
                $this->error('Tidak ada akun user yang terdaftar di database.');
                return self::FAILURE;
            }

            $this->table(
                ['ID', 'Nama', 'Email', 'Role'],
                $users->map(fn ($u) => [$u->id, $u->name, $u->email, $u->role ?? '—'])->toArray()
            );

            $email = $this->ask('Masukkan email akun yang ingin direset passwordnya');
        }

        // ── 2. Cari user ────────────────────────────────────────────────
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Akun dengan email [{$email}] tidak ditemukan.");
            return self::FAILURE;
        }

        $this->info("Akun ditemukan: <fg=green>{$user->name}</> ({$user->email}) — Role: " . ($user->role ?? '—'));
        $this->info('');

        // ── 3. Konfirmasi ───────────────────────────────────────────────
        if (! $this->confirm("Lanjutkan reset password untuk akun ini?", false)) {
            $this->line('  Operasi dibatalkan.');
            return self::SUCCESS;
        }

        // ── 4. Input password baru ──────────────────────────────────────
        $password = $this->secret('Masukkan password baru (minimal 8 karakter, tidak akan ditampilkan)');

        if (strlen($password) < 8) {
            $this->error('Password terlalu pendek. Minimal 8 karakter.');
            return self::FAILURE;
        }

        $confirm = $this->secret('Konfirmasi password baru');

        if ($password !== $confirm) {
            $this->error('Password dan konfirmasi tidak cocok. Operasi dibatalkan.');
            return self::FAILURE;
        }

        // ── 5. Simpan ───────────────────────────────────────────────────
        $user->update([
            'password' => Hash::make($password),
        ]);

        $this->info('');
        $this->line('  <fg=green;options=bold>✓ Password berhasil direset!</>');
        $this->line("  Akun  : {$user->name} ({$user->email})");
        $this->line('  Waktu : ' . now()->format('d/m/Y H:i:s') . ' WIB');
        $this->info('');
        $this->warn('  Pastikan password baru sudah dicatat di tempat yang aman.');
        $this->info('');

        return self::SUCCESS;
    }
}
