<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ----------------------------------------------------------------
        // TIMEZONE & LOCALE
        // ----------------------------------------------------------------

        // Set locale Carbon ke Bahasa Indonesia agar translatedFormat()
        // menghasilkan nama bulan/hari dalam Bahasa Indonesia
        // Contoh: now()->translatedFormat('d F Y') → "11 Juli 2026"
        Carbon::setLocale(config('app.locale', 'id'));

        // ----------------------------------------------------------------
        // ELOQUENT STRICT MODE
        // ----------------------------------------------------------------

        // Di non-production: aktifkan semua guard Eloquent untuk deteksi
        // bug sedini mungkin saat development:
        //   - preventLazyLoading()      : lempar exception jika ada N+1 query
        //   - preventSilentlyDiscardingAttributes() : exception jika assign
        //     atribut yang tidak ada di $fillable
        //   - preventAccessingMissingAttributes()   : exception jika akses
        //     property yang tidak di-select atau tidak ada di model
        Model::shouldBeStrict(! app()->isProduction());

        // ----------------------------------------------------------------
        // PRODUCTION HTTPS
        // ----------------------------------------------------------------

        // Paksa semua generated URL menggunakan HTTPS di production.
        // Penting jika app berjalan di balik reverse proxy/load balancer
        // yang meng-terminate SSL (APP_URL menggunakan https://)
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // ----------------------------------------------------------------
        // SHARE $company KE SEMUA VIEW
        // ----------------------------------------------------------------
        // Logo, favicon, dan info showroom tersedia di semua template
        // tanpa perlu inject manual dari setiap controller.
        View::share('company', \App\Models\Company::first());
    }
}
