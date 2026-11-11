<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $mataKuliahs = MataKuliah::query()
            ->withCount('kelasPerkuliahan')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('kode_mk', 'like', '%'.$search.'%')
                        ->orWhere('nama_mk', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('kode_mk')
            ->paginate(10)
            ->withQueryString();

        return view('admin.matkul.index', [
            'mataKuliahs' => $mataKuliahs,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.matkul.form', [
            'matkul' => new MataKuliah,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        MataKuliah::create($this->validatedPayload($request));

        return redirect()
            ->route('admin.master-matkul.index')
            ->with('status', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $matkul): View
    {
        return view('admin.matkul.form', [
            'matkul' => $matkul,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, MataKuliah $matkul): RedirectResponse
    {
        $matkul->update($this->validatedPayload($request, $matkul));

        return redirect()
            ->route('admin.master-matkul.index')
            ->with('status', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * @return array{kode_mk: string, nama_mk: string, sks: int|string}
     */
    private function validatedPayload(Request $request, ?MataKuliah $matkul = null): array
    {
        return $request->validate([
            'kode_mk' => ['required', 'string', 'max:20', Rule::unique('mata_kuliah', 'kode_mk')->ignore($matkul?->id)],
            'nama_mk' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
        ], [
            'kode_mk.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mk.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mk.required' => 'Nama mata kuliah wajib diisi.',
            'sks.required' => 'Jumlah SKS wajib diisi.',
            'sks.min' => 'Minimal SKS adalah 1.',
            'sks.max' => 'Maksimal SKS adalah 6.',
        ]);
    }

    public function destroy(MataKuliah $matkul): RedirectResponse
    {
        if ($matkul->kelasPerkuliahan()->exists()) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena masih digunakan pada kelas perkuliahan.');
        }

        $matkul->delete();

        return redirect()
            ->route('admin.master-matkul.index')
            ->with('status', 'Mata kuliah berhasil dihapus.');
    }
}
