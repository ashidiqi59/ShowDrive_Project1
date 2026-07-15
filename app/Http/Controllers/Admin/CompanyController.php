<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile', ['company' => Company::first()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'tax_id'  => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:50',
        ]);

        $company = Company::first();

        if (! $company) {
            return redirect()->back()->with('error', 'Data perusahaan tidak ditemukan.');
        }

        $company->update($request->only('name', 'tax_id', 'address', 'phone'));

        return redirect()->back()->with('success', 'Profil showroom berhasil diperbarui.');
    }
}
