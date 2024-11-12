<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockCorrectionRequest extends FormRequest
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
            'product_id' => ['required'],
            'old_stock' => ['required'],
            'new_stock' => ['required'],
            'description' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.'
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'Nama barang',
            'old_stock' => 'Stok lama',
            'new_stock' => 'Stok baru',
            'description' => 'Keterangan',
        ];
    }
}
