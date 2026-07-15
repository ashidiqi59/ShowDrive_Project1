<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::with('company:id,name')
            ->withCount('items')
            ->orderBy('name')
            ->get();

        return view('admin.warehouses', [
            'warehouses' => $warehouses,
            'company'    => Company::first(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'location'   => 'required|string|max:255',
        ]);

        Warehouse::create($request->only('company_id', 'name', 'location'));

        return redirect()->back()->with('success', 'Lokasi gudang berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'location'   => 'required|string|max:255',
        ]);

        Warehouse::findOrFail($id)->update($request->only('company_id', 'name', 'location'));

        return redirect()->back()->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $warehouse = Warehouse::withCount('items')->findOrFail($id);

        if ($warehouse->items_count > 0) {
            return redirect()->back()->with(
                'error',
                "Gagal hapus: Gudang ini masih memiliki {$warehouse->items_count} unit kendaraan terdaftar."
            );
        }

        $warehouse->delete();

        return redirect()->back()->with('success', 'Gudang berhasil dihapus.');
    }
}
