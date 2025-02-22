<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
                'name' => ['required', 'unique:payments,name'],
                'bank_name' => ['required'],
                'account_name' => ['required'],
                'account_number' => ['required'],
                'tax' => ['nullable', 'numeric']
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'name' => ['required'],
                'bank_name' => ['required'],
                'account_name' => ['required'],
                'account_number' => ['required'],
                'tax' => ['nullable', 'numeric']
            ];
        }
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'numeric' => ':attribute harus diisi angkat.',
            'unique' => ':attribute sudah terdapat di sistem.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Pembayaran',
            'bank_name' => 'Nama Bank',
            'account_name' => 'Nama Akun',
            'account_number' => 'No rekening',
            'tax' => 'Pajak'
        ];
    }
}
