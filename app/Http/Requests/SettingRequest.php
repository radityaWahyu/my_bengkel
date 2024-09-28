<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        if (request()->hasFile('settings.1.value')) {
            return [
                'settings.0.value' => ['required'],
                'settings.1.value' => [File::image()
                    ->max('1mb')],
                'settings.2.value' => ['required'],
                'settings.3.value' => ['required'],
                'settings.4.value' => ['required', 'numeric'],
                'settings.5.value' => ['required', 'numeric'],
            ];
        } else {
            return [
                'settings.0.value' => ['required'],
                'settings.2.value' => ['required'],
                'settings.3.value' => ['required'],
                'settings.4.value' => ['required', 'numeric'],
                'settings.5.value' => ['required', 'numeric'],
            ];
        }
    }

    public function messages(): array
    {
        return [
            'settings.*.value.required' => ':attribute harus diisi',
            'settings.*.value.image' => ':attribute harus diinput dengan format gambar',
            'settings.*.value.file' => ':attribute harus diinput dengan format gambar',
            'settings.*.value.max' => 'Maksimal ukuran file :size ',
            'settings.*.value.numeric' => ':attribute harrus diisi dengan angka.',
        ];
    }

    public function attributes(): array
    {
        return [
            'settings.0.value' => 'Nama Bengkel',
            'settings.1.value' => 'Logo Bengkel',
            'settings.2.value' => 'Alamat',
            'settings.3.value' => 'Nama Kontak',
            'settings.4.value' => 'No Telepon',
            'settings.5.value' => 'No Whatsapp',
        ];
    }
}
