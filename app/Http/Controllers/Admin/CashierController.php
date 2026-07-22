<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCashierRequest;
use App\Http\Requests\Admin\UpdateCashierRequest;
use App\Models\Cashier;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        return view('admin.cashiers', [
            'cashiers' => Cashier::with('company:id,name')->orderBy('name')->get(),
            'company'  => Company::first(),
        ]);
    }

    public function store(StoreCashierRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Cashier::create([
            'company_id' => $validated['company_id'],
            'name'       => $validated['name'],
            'username'   => $validated['username'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
        ]);

        return redirect()->back()->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    public function update(UpdateCashierRequest $request, int $id): RedirectResponse
    {
        $cashier   = Cashier::findOrFail($id);
        $validated = $request->validated();
        $data      = collect($validated)->except('password')->toArray();

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $cashier->update($data);

        return redirect()->back()->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $target = Cashier::findOrFail($id);

        // Proteksi 1: tidak bisa hapus akun sendiri
        if (Auth::id() === $id) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun yang sedang aktif digunakan.');
        }

        // Proteksi 2: Super Admin tidak bisa dihapus dari UI
        // Nilai 'Super Admin' harus di-set langsung di DB, tidak tersedia di form dropdown
        if ($target->role === 'Super Admin') {
            return redirect()->back()->with('error', 'Akun Super Admin tidak dapat dihapus. Gunakan CLI jika benar-benar diperlukan.');
        }

        // Proteksi 3: hanya Super Admin yang bisa menghapus akun Admin
        // Kasir biasa tidak punya hak menghapus siapapun selain dirinya sendiri (sudah di-block di atas)
        /** @var \App\Models\Cashier $actor */
        $actor = Auth::user();
        if ($target->role === 'Admin' && $actor->role !== 'Super Admin') {
            return redirect()->back()->with('error', 'Hanya Super Admin yang dapat menghapus akun Admin.');
        }

        $target->delete();

        return redirect()->back()->with('success', 'Akun staf berhasil dihapus.');
    }
}
