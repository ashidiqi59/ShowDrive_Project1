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
        if (Auth::id() === $id) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun yang sedang aktif digunakan.');
        }

        Cashier::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Akun kasir berhasil dihapus.');
    }
}
