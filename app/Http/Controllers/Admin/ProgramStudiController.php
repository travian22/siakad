<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramStudiController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $programStudis = ProgramStudi::query()
            ->withCount('mahasiswa')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('kode_prodi', 'like', '%'.$search.'%')
                        ->orWhere('nama_prodi', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('kode_prodi')
            ->paginate(10)
            ->withQueryString();

        return view('admin.prodi.index', [
            'programStudis' => $programStudis,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.prodi.form', [
            'prodi' => new ProgramStudi,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ProgramStudi::create($this->validatedPayload($request));

        return redirect()
            ->route('admin.master-prodi.index')
            ->with('status', 'Program studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $prodi): View
    {
        return view('admin.prodi.form', [
            'prodi' => $prodi,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, ProgramStudi $prodi): RedirectResponse
    {
        $prodi->update($this->validatedPayload($request, $prodi));

        return redirect()
            ->route('admin.master-prodi.index')
            ->with('status', 'Program studi berhasil diperbarui.');
    }

    /**
     * @return array{kode_prodi: string, nama_prodi: string}
     */
    private function validatedPayload(Request $request, ?ProgramStudi $prodi = null): array
    {
        return $request->validate([
            'kode_prodi' => ['required', 'string', 'max:20', Rule::unique('program_studi', 'kode_prodi')->ignore($prodi?->id)],
            'nama_prodi' => ['required', 'string', 'max:255'],
        ], [
            'kode_prodi.required' => 'Kode program studi wajib diisi.',
            'kode_prodi.unique' => 'Kode program studi sudah digunakan.',
            'nama_prodi.required' => 'Nama program studi wajib diisi.',
        ]);
    }

    public function destroy(ProgramStudi $prodi): RedirectResponse
    {
        if ($prodi->mahasiswa()->exists()) {
            return back()->with('error', 'Program studi tidak dapat dihapus karena masih memiliki mahasiswa terdaftar.');
        }

        $prodi->delete();

        return redirect()
            ->route('admin.master-prodi.index')
            ->with('status', 'Program studi berhasil dihapus.');
    }
}
