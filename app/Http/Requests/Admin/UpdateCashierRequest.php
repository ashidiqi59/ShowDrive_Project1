<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:100|unique:cashiers,username,' . $id . '|alpha_dash',
            'password'   => 'nullable|string|min:8|max:100',
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
