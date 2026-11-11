<?php

namespace App\Http\Requests\Admin;

use App\Models\Dosen;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DosenRequest extends FormRequest
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
        /** @var Dosen|null $dosen */
        $dosen = $this->route('dosen');

        return [
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($dosen?->user_id),
            ],
            'password' => $dosen ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('dosen', 'nip')->ignore($dosen?->id),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'telepon' => ['nullable', 'string', 'max:25'],
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
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'password.required' => 'Kata sandi wajib diisi untuk akun dosen baru.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
        ];
    }
}
