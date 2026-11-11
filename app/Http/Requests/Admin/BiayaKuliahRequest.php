<?php

namespace App\Http\Requests\Admin;

use App\Models\BiayaKuliah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BiayaKuliahRequest extends FormRequest
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
        /** @var BiayaKuliah|null $biaya */
        $biaya = $this->route('biaya');

        return [
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'periode_akademik_id' => [
                'required',
                'exists:periode_akademik,id',
                Rule::unique('biaya_kuliah', 'periode_akademik_id')
                    ->where(fn ($query) => $query->where('mahasiswa_id', $this->input('mahasiswa_id')))
                    ->ignore($biaya?->id),
            ],
            'nominal_tagihan' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'status_bayar' => ['required', Rule::in(['belum_bayar', 'lunas'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mahasiswa_id.required' => 'Mahasiswa wajib dipilih.',
            'periode_akademik_id.required' => 'Periode akademik wajib dipilih.',
            'periode_akademik_id.unique' => 'Tagihan untuk mahasiswa dan periode ini sudah ada.',
            'nominal_tagihan.required' => 'Nominal tagihan wajib diisi.',
            'nominal_tagihan.numeric' => 'Nominal tagihan harus berupa angka.',
            'nominal_tagihan.min' => 'Nominal tagihan tidak boleh kurang dari 0.',
            'status_bayar.required' => 'Status pembayaran wajib dipilih.',
        ];
    }
}
