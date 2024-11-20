<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
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
            'name' => ['required'],
            'gender' => ['required'],
            'whatsapp' => ['required',  'max:11'],
            'phone' => ['max:11'],
            'username' => ['required'],
            'password' => ['sometimes', 'nullable', 'min:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.',
            'min' => ':attribute harus diisi minimal :min digit',
            'max' => ':attribute harus diisi maksimal :min digit',
            'number' => ':attribute harus diisi dengan angka',
            'exists' => ':attribute tidak terdapat dalam sistem.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'gender' => 'Jenis Kelamin',
            'whatsapp' => 'No Whatsapp',
            'phone' => 'No Telepon',
            'username' => 'Username',
            'password' => 'Password',

        ];
    }
}
