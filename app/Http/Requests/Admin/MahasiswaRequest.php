<?php

namespace App\Http\Requests\Admin;

use App\Models\Mahasiswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = $this->route('mahasiswa');

        return [
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($mahasiswa?->user_id),
            ],
            'password' => $mahasiswa ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
            'npm' => [
                'required',
                'string',
                'max:30',
                Rule::unique('mahasiswa', 'npm')->ignore($mahasiswa?->id),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'program_studi_id' => ['required', 'exists:program_studi,id'],
            'angkatan' => ['required', 'digits:4'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi untuk akun mahasiswa baru.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'npm.required' => 'NPM wajib diisi.',
            'npm.unique' => 'NPM sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'angkatan.digits' => 'Angkatan harus berupa 4 digit tahun (contoh: 2024).',
        ];
    }
}
