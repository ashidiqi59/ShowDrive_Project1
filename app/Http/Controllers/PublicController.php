<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Car::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $cars = $query->orderBy('id', 'asc')->get();

        return view('home', compact('cars', 'search'));
    }

    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('detail', compact('car'));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'car_id' => 'required|exists:cars,id',
            'date' => 'required|date',
        ]);

        $car = Car::findOrFail($request->car_id);

        if ($car->status !== 'Available') {
            return back()->with('error', 'Unit kendaraan ini tidak tersedia untuk dipesan.');
        }

        // Create booking
        $booking = Booking::create([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'car_id' => $request->car_id,
            'date' => $request->date,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'payment_type' => 'None',
            'paid_amount' => 0,
            'payment_proof' => null,
        ]);

        // Update car status to Booked
        $car->update(['status' => 'Booked']);

        return redirect()->route('booking.track', ['phone' => $request->phone])
            ->with('success', 'SUKSES! Jadwal inspeksi berhasil disimpan. Silakan unggah bukti transfer DP.');
    }

    public function track(Request $request)
    {
        $phone = $request->input('phone');
        $bookings = [];

        if ($phone) {
            $bookings = Booking::with('car')
                ->where('phone', $phone)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('track', compact('bookings', 'phone'));
    }

    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'required|in:Down Payment,Paid',
            'paid_amount' => 'required|numeric|min:0',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $booking = Booking::findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            // Delete old file if exists
            if ($booking->payment_proof) {
                Storage::disk('public')->delete($booking->payment_proof);
            }

            // Save new proof file
            $path = $request->file('payment_proof')->store('receipts', 'public');
            
            $booking->update([
                'payment_type' => $request->payment_type,
                'paid_amount' => $request->paid_amount,
                'payment_proof' => $path,
                'payment_status' => $request->payment_type, // Under admin review
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.');
    }

    public function invoice($id)
    {
        $booking = Booking::with('car')->findOrFail($id);
        return view('invoice', compact('booking'));
    }
}
