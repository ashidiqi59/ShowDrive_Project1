<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login admin.
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Memproses autentikasi (Login).
     */
    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Attempt login (menggunakan model Cashier yang sudah extend Authenticatable)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Arahkan ke dashboard admin setelah login sukses
            return redirect()->intended('/admin/dashboard');
        }

        // Jika login gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Mengelola logout admin.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Langsung redirect ke homepage — menghindari double redirect
        // (sebelumnya redirect('/login') yang langsung di-redirect ke '/' oleh Route::redirect)
        return redirect()->route('home')->with('info', 'Anda telah berhasil logout dari sistem ShowDrive.');
    }
}