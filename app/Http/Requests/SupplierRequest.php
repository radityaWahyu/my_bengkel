<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
                'name' => ['required', 'unique:suppliers,name'],
                'contact_name' => ['required'],
                'address' => ['required'],
                'phone' => ['required', 'numeric'],
                'whatsapp' => ['required', 'numeric'],
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'name' => ['required'],
                'contact_name' => ['required'],
                'address' => ['required'],
                'phone' => ['required', 'numeric'],
                'whatsapp' => ['required', 'numeric'],
            ];
        }
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.',
            'integer' => ':attribute harus diisi angka',
            'numeric' => ':attribute harus diisi angka'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Pegawai',
            'contact_name' => 'Nama Kontak',
            'address' => 'Alamat',
            'phone' => 'No Telepon',
            'whatsapp' => 'No Whatsapp',
        ];
    }
}
