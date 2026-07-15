<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarRequest;
use App\Http\Requests\Admin\UpdateCarRequest;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with(['warehouse:id,name', 'images:id,item_id,image_path']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('vin', 'like', '%' . $search . '%');
            });
        }

        $cars       = $query->latest()->paginate(15)->withQueryString();
        $warehouses = Warehouse::select('id', 'name')->orderBy('name')->get();

        return view('admin.items', compact('cars', 'warehouses'));
    }

    public function store(StoreCarRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $dbStatus  = $validated['status'] === 'Booked' ? 'Invoiced' : $validated['status'];

        $item = Item::create([
            'warehouse_id'  => $validated['warehouse_id'],
            'brand'         => $validated['brand'],
            'model'         => $validated['model'],
            'vin'           => strtoupper($validated['vin']),
            'price'         => $validated['price'],
            'dp_percentage' => $validated['dp_percentage'],
            'year'          => $validated['year'],
            'color'         => $validated['color'],
            'engine'        => $validated['engine'],
            'transmission'  => $validated['transmission'],
            'status'        => $dbStatus,
            'image_url'     => null,
        ]);

        $this->uploadImages($request, $item);

        return redirect()->back()->with('success', 'Unit mobil berhasil ditambahkan.');
    }

    public function update(UpdateCarRequest $request, int $id): RedirectResponse
    {
        $item      = Item::findOrFail($id);
        $validated = $request->validated();
        $dbStatus  = $validated['status'] === 'Booked' ? 'Invoiced' : $validated['status'];

        $item->update([
            'warehouse_id'  => $validated['warehouse_id'],
            'brand'         => $validated['brand'],
            'model'         => $validated['model'],
            'vin'           => strtoupper($validated['vin']),
            'price'         => $validated['price'],
            'dp_percentage' => $validated['dp_percentage'],
            'year'          => $validated['year'],
            'color'         => $validated['color'],
            'engine'        => $validated['engine'],
            'transmission'  => $validated['transmission'],
            'status'        => $dbStatus,
        ]);

        if ($request->hasFile('images')) {
            $oldPaths = $item->images()->pluck('image_path')->all();
            Storage::disk('public')->delete($oldPaths);
            $item->images()->delete();
            $this->uploadImages($request, $item);
        }

        return redirect()->back()->with('success', 'Unit mobil berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Item::findOrFail($id);

        try {
            Storage::disk('public')->delete($item->images()->pluck('image_path')->all());
            $item->delete();
            return redirect()->back()->with('success', 'Unit berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return redirect()->back()->with('error', 'Gagal hapus: Unit ini memiliki riwayat transaksi aktif.');
        }
    }

    private function uploadImages(Request $request, Item $item): void
    {
        $primarySet = false;

        foreach ($request->file('images') as $file) {
            $path = $file->store('cars', 'public');
            ItemImage::create(['item_id' => $item->id, 'image_path' => $path]);

            if (! $primarySet) {
                $item->update(['image_url' => $path]);
                $primarySet = true;
            }
        }
    }
}
