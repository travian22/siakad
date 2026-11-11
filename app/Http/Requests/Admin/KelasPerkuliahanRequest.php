<?php

namespace App\Http\Requests\Admin;

use App\Models\KelasPerkuliahan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelasPerkuliahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'jam_mulai' => is_string($this->jam_mulai) ? substr($this->jam_mulai, 0, 5) : $this->jam_mulai,
            'jam_selesai' => is_string($this->jam_selesai) ? substr($this->jam_selesai, 0, 5) : $this->jam_selesai,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periode_akademik_id' => ['required', 'exists:periode_akademik,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'nama_kelas' => ['required', 'string', 'max:50'],
            'hari' => ['required', Rule::in(KelasPerkuliahan::DAYS)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'periode_akademik_id.required' => 'Periode akademik wajib dipilih.',
            'mata_kuliah_id.required' => 'Mata kuliah wajib dipilih.',
            'dosen_id.required' => 'Dosen pengampu wajib dipilih.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'hari.required' => 'Hari perkuliahan wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM.',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
            'ruangan.required' => 'Ruangan wajib diisi.',
        ];
    }
}
