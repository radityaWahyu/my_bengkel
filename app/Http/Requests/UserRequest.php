<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                'username' => ['required', 'unique:users,username'],
                'password' => ['required'],
                'employee_id' => ['required'],
                'role' => ['required']
            ];
        } elseif (request()->isMethod('put')) {
            return [
                'username' => ['required', 'unique:users,username'],
                'employee_id' => ['required'],
                'role' => ['required']
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
            'username' => 'Username',
            'password' => 'Password',
            'employee_id' => 'Pegawai',
            'role' => 'Role User'
        ];
    }
}
