<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'vehicle_id' => ['required'],
            'description' => ['required'],
        ];
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
            'vehicle_id' => 'Data kendaraan',
            'description' => 'Deskripsi Keluhan',
        ];
    }
}
