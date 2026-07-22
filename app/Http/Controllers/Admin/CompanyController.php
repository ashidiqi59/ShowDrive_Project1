<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'name'        => 'required|string|max:255',
            'tax_id'      => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:500',
            'phone'       => 'nullable|string|max:50',
            'logo_url'    => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'favicon_url' => 'nullable|mimes:ico,png,svg,jpeg,jpg,webp|max:512',
        ], [
            'logo_url.image'    => 'File logo harus berupa gambar.',
            'logo_url.mimes'    => 'Format logo tidak didukung. Gunakan JPEG, PNG, WebP, atau SVG.',
            'logo_url.max'      => 'Ukuran file logo maksimal 2MB.',
            'favicon_url.mimes' => 'Format favicon tidak didukung. Gunakan ICO, PNG, atau SVG.',
            'favicon_url.max'   => 'Ukuran favicon maksimal 512KB.',
        ]);

        $company = Company::firstOrCreate([], ['name' => 'ShowDrive']);

        $data = $request->only('name', 'tax_id', 'address', 'phone');

        // Handle logo upload
        if ($request->hasFile('logo_url')) {
            // Hapus logo lama jika ada
            if ($company->logo_url) {
                Storage::disk('public')->delete($company->logo_url);
            }
            $data['logo_url'] = $request->file('logo_url')->store('branding', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon_url')) {
            // Hapus favicon lama jika ada
            if ($company->favicon_url) {
                Storage::disk('public')->delete($company->favicon_url);
            }
            $data['favicon_url'] = $request->file('favicon_url')->store('branding', 'public');
        }

        $company->update($data);

        return redirect()->back()->with('success', 'Profil showroom berhasil diperbarui.');
    }
}
