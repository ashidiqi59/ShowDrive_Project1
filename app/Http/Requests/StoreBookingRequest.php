<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route publik — semua pengunjung boleh booking
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\.\'\-]+$/u'],
            'phone'         => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{7,11}$/'],
            'nik'           => ['nullable', 'digits:16'],
            'date'          => ['required', 'date', 'after_or_equal:tomorrow', 'before_or_equal:+7 days'],
            'time'          => ['required', 'date_format:H:i'],
            'payment_type'  => ['required', 'in:Down Payment,Paid'],
            'car_id'        => ['required', 'exists:items,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_name.min'      => 'Nama minimal 3 karakter.',
            'customer_name.regex'    => 'Nama hanya boleh mengandung huruf dan spasi.',
            'phone.required'         => 'Nomor HP wajib diisi.',
            'phone.regex'            => 'Format HP tidak valid. Gunakan: 08xx, 628xx, atau +628xx.',
            'nik.digits'             => 'NIK harus tepat 16 digit angka.',
            'date.required'          => 'Tanggal inspeksi wajib diisi.',
            'date.after_or_equal'    => 'Tanggal inspeksi paling cepat adalah besok.',
            'date.before_or_equal'   => 'Tanggal inspeksi paling lambat adalah 7 hari ke depan.',
            'time.required'          => 'Jam inspeksi wajib diisi.',
            'time.date_format'       => 'Format jam tidak valid.',
            'payment_type.required'  => 'Pilihan metode pembayaran wajib diisi.',
            'payment_type.in'        => 'Metode pembayaran tidak valid.',
            'car_id.required'        => 'Unit kendaraan tidak ditemukan.',
            'car_id.exists'          => 'Unit kendaraan tidak valid.',
        ];
    }
}
