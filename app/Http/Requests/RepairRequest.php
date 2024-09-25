<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairRequest extends FormRequest
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
                'name' => ['required', 'unique:repairs,name'],
                'price' => ['required', 'numeric']
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'name' => ['required'],
                'price' => ['required', 'numeric']
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
            'name' => 'Nama Merk',
            'price' => 'Harga'
        ];
    }
}
