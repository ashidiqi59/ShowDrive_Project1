<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:100|unique:cashiers,username|alpha_dash',
            'password'   => 'required|string|min:8|max:100',
            'role'       => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'    => 'Username sudah digunakan oleh akun lain.',
            'username.alpha_dash'=> 'Username hanya boleh mengandung huruf, angka, - dan _.',
            'password.min'       => 'Password minimal 8 karakter.',
        ];
    }
}
