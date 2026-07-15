<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tamu tidak sah yang mencoba mengakses rute terproteksi (/admin/*)
        // akan langsung mendapatkan respon 404 (seolah-olah rute tersebut tidak ada)
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null; // Membiarkan Laravel mengembalikan respon JSON 401 Unauthenticated
            }
            if ($request->cookies->has('sd_logged_in')) {
                return route('login') . '?gateway_token=SD_STEALTH_AUTH_2026';
            }
            abort(404);
        });

        // Authenticated users who hit the login page are redirected to their dashboard.
        $middleware->redirectUsersTo('/admin/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
