<?php

namespace App\Http\Requests\Dosen;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreNilaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'dosen';
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nilai' => ['required', 'array', 'min:1'],
            'nilai.*' => ['required', 'array'],
            'nilai.*.tugas' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.uts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.uas' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * @return array<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $rows = $this->input('nilai', []);

                if (! is_array($rows)) {
                    return;
                }

                $completeCount = 0;

                foreach ($rows as $pesertaId => $scores) {
                    if (! is_array($scores)) {
                        continue;
                    }

                    $hasTugas = filled($scores['tugas'] ?? null);
                    $hasUts = filled($scores['uts'] ?? null);
                    $hasUas = filled($scores['uas'] ?? null);

                    if ($hasTugas && $hasUts && $hasUas) {
                        $completeCount++;

                        continue;
                    }

                    if ($hasTugas || $hasUts || $hasUas) {
                        $validator->errors()->add(
                            "nilai.{$pesertaId}",
                            'Nilai Tugas, UTS, dan UAS untuk setiap mahasiswa yang dinilai harus diisi lengkap.'
                        );
                    }
                }

                if ($completeCount === 0 && $validator->errors()->isEmpty()) {
                    $validator->errors()->add(
                        'nilai',
                        'Isi minimal satu nilai lengkap mahasiswa (Tugas, UTS, dan UAS) sebelum menyimpan.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nilai.required' => 'Data nilai tidak ditemukan.',
            'nilai.*.tugas.numeric' => 'Nilai Tugas harus berupa angka.',
            'nilai.*.tugas.min' => 'Nilai Tugas minimal 0.',
            'nilai.*.tugas.max' => 'Nilai Tugas maksimal 100.',
            'nilai.*.uts.numeric' => 'Nilai UTS harus berupa angka.',
            'nilai.*.uts.min' => 'Nilai UTS minimal 0.',
            'nilai.*.uts.max' => 'Nilai UTS maksimal 100.',
            'nilai.*.uas.numeric' => 'Nilai UAS harus berupa angka.',
            'nilai.*.uas.min' => 'Nilai UAS minimal 0.',
            'nilai.*.uas.max' => 'Nilai UAS maksimal 100.',
        ];
    }
}
