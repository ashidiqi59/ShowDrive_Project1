<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route sudah dilindungi middleware auth
    }

    public function rules(): array
    {
        return [
            'warehouse_id'  => 'required|exists:warehouses,id',
            'brand'         => 'required|string|max:100',
            'model'         => 'required|string|max:100',
            'vin'           => 'required|string|size:17|unique:items,vin|alpha_num',
            'price'         => 'required|numeric|min:1|max:99999999999999.99',
            'year'          => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color'         => 'required|string|max:100',
            'engine'        => 'required|string|max:150',
            'transmission'  => 'required|string|max:100',
            'dp_percentage' => 'required|integer|min:1|max:100',
            'images'        => 'required|array|min:1|max:10',
            'images.*'      => 'required|mimes:jpeg,jpg,png,webp|max:2048',
            'status'        => 'required|in:Available,Booked,Sold',
        ];
    }

    public function messages(): array
    {
        return [
            'vin.size'           => 'Nomor VIN harus tepat 17 karakter.',
            'vin.unique'         => 'Nomor VIN sudah terdaftar di sistem.',
            'vin.alpha_num'      => 'VIN hanya boleh berisi huruf dan angka.',
            'dp_percentage.min'  => 'Persentase DP minimal 1%.',
            'dp_percentage.max'  => 'Persentase DP maksimal 100%.',
            'images.required'    => 'Minimal 1 foto unit wajib diunggah.',
            'images.*.mimes'     => 'Format foto tidak didukung. Gunakan JPEG, PNG, atau WebP.',
            'images.*.max'       => 'Ukuran setiap foto maksimal 2MB.',
        ];
    }
}
