<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JurnalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_date' => ['required'],
            'payment' => ['required'],
            'jurnal_type' => ['required'],
            'total' => ['required', 'numeric'],
            'description' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.',
            'numeric' => ':attribute harus diisi dengan angka'
        ];
    }

    public function attributes(): array
    {
        return [
            'transaction_date' => 'Tanggal transaksi',
            'payment' => 'Jenis pembayaran',
            'jurnal_type' => 'Jenis jurnal',
            'total' => 'Total',
            'description' => 'Deskripsi jurnal',
        ];
    }
}
