<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            return [
                'name' => ['required', 'unique:products,name'],
                'stock' => ['required', 'integer'],
                'category_id' => ['required'],
                'rack_id' => ['required'],
                'buy_price' => ['required', 'integer'],
                'sale_price' => ['required', 'integer'],
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'name' => ['required'],
                'category_id' => ['required'],
                'rack_id' => ['required'],
                'buy_price' => ['required', 'integer'],
                'sale_price' => ['required', 'integer'],
            ];
        }
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.',
            'integer' => ':attribute harus diisi angka'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Merk',
            'category_id' => 'Kategori',
            'rack_id' => 'Rak',
            'buy_price' => 'Harga Beli',
            'sale_price' => 'Harga Jual',
            'stock' => 'Stok Awal',
        ];
    }
}
