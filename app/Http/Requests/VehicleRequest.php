<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
                'name' => ['required'],
                'plate_number' => ['required', 'unique:vehicles,plate_number'],
                'machine_frame' => ['required'],
                'engine_volume' => ['required'],
                'engine_type' => ['required'],
                'type' => ['required'],
                'production_year' => ['required', 'numeric'],
                'brand_id' => ['required'],
                'customer_id' => ['required'],
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'name' => ['required'],
                'plate_number' => ['required'],
                'machine_frame' => ['required'],
                'engine_volume' => ['required'],
                'engine_type' => ['required'],
                'type' => ['required'],
                'production_year' => ['required', 'numeric', 'max:4'],
                'brand_id' => ['required'],
                'customer_id' => ['required'],
            ];
        }
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah terdapat di sistem.',
            'integer' => ':attribute harus diisi angka',
            'max' => ':attribute masksimal pengisian :size digit'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama merk kendaraan',
            'plate_number' => 'No Plat',
            'machine_frame' => 'No Rangka',
            'engine_volume' => 'CC Mesin',
            'engine_type' => 'Jenis Mesin',
            'type' => 'Jenis Kendaraan',
            'production_year' => 'Tahun Pembuatan',
            'brand_id' => 'Nama Merk',
            'customer_id' => 'Nama Pelanggan',
        ];
    }
}
