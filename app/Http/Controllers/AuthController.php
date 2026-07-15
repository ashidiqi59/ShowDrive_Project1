<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        if ($request->query('gateway_token') !== config('app.gateway_token')) {
            abort(404);
        }
        return view('login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            cookie()->queue('sd_logged_in', '1', 43200); // 30 days
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        cookie()->queue(cookie()->forget('sd_logged_in'));
        return redirect()->route('home')
            ->with('info', 'Anda telah berhasil logout dari sistem ShowDrive.');
    }

    public function shortcutRedirect(): RedirectResponse
    {
        return redirect('/pintu-akses-masuk-showdrive?gateway_token=' . config('app.gateway_token'));
    }
}
