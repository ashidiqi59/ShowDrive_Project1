<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCars = Car::count();
        $availableCars = Car::where('status', 'Available')->count();
        $bookedCars = Car::where('status', 'Booked')->count();
        $soldCars = Car::where('status', 'Sold')->count();
        $incomingCash = Booking::whereIn('payment_status', ['Paid', 'Down Payment'])->sum('paid_amount');

        $cars = Car::orderBy('id', 'desc')->get();
        $bookings = Booking::with('car')->orderBy('id', 'desc')->get();

        return view('admin.dashboard', compact(
            'totalCars',
            'availableCars',
            'bookedCars',
            'soldCars',
            'incomingCash',
            'cars',
            'bookings'
        ));
    }

    public function storeCar(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vin' => 'required|string|unique:cars,vin|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Booked,Sold',
            'engine' => 'required|string|max:255',
            'transmission' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'image' => 'required|string|max:2000', // Image URL
        ]);

        Car::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Unit baru berhasil disimpan.');
    }

    public function updateCar(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vin' => 'required|string|max:255|unique:cars,vin,' . $car->id,
            'year' => 'required|integer|min:1900|max:2100',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Booked,Sold',
            'engine' => 'required|string|max:255',
            'transmission' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'image' => 'required|string|max:2000', // Image URL
        ]);

        $car->update($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Data unit berhasil diperbarui.');
    }

    public function deleteCar($id)
    {
        $car = Car::findOrFail($id);

        // Referential Integrity check: restrict deletion if active pending bookings exist
        $hasActiveBooking = Booking::where('car_id', $id)->where('status', 'Pending')->exists();
        
        if ($hasActiveBooking) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'GAGAL MENGHAPUS! Unit kendaraan memiliki relasi aktif di tabel transaksi "bookings" (Referential Integrity Constraint: ON DELETE RESTRICT). Selesaikan transaksi terlebih dahulu.');
        }

        // Otherwise delete
        $car->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Unit kendaraan berhasil dihapus.');
    }

    public function verifyPayment($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'payment_status' => 'Paid',
            'status' => 'Approved', // Auto approves inspection
        ]);

        $booking->car->update(['status' => 'Sold']);

        return redirect()->route('admin.dashboard')->with('success', 'Pembayaran berhasil diverifikasi. Status unit otomatis berubah menjadi "Sold".');
    }

    public function processInspection(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        if ($request->status === 'Rejected') {
            $booking->car->update(['status' => 'Available']);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Jadwal inspeksi berhasil diproses.');
    }
}
