<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.payment_settings', ['company' => Company::first()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name'           => 'nullable|string|max:100',
            'bank_account'        => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:255',
            'qris_image'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'qris_image.image' => 'File QRIS harus berupa gambar.',
            'qris_image.mimes' => 'Format gambar QRIS tidak didukung. Gunakan JPEG, PNG, atau WebP.',
            'qris_image.max'   => 'Ukuran gambar QRIS maksimal 2MB.',
        ]);

        $company = Company::first();

        if (! $company) {
            return redirect()->back()->with('error', 'Data perusahaan tidak ditemukan.');
        }

        $data = $request->only('bank_name', 'bank_account', 'bank_account_holder');

        if ($request->hasFile('qris_image')) {
            // Hapus gambar QRIS lama jika ada
            if ($company->qris_image) {
                Storage::disk('public')->delete($company->qris_image);
            }
            $path = $request->file('qris_image')->store('company', 'public');
            $data['qris_image'] = $path;
        }

        $company->update($data);

        return redirect()->back()->with('success', 'Rekening & QRIS showroom berhasil diperbarui.');
    }
}
