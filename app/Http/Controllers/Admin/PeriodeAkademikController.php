<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodeAkademikController extends Controller
{
    public function index(): View
    {
        $periodes = PeriodeAkademik::query()
            ->withCount(['kelasPerkuliahan', 'biayaKuliah'])
            ->ordered()
            ->paginate(10);

        return view('admin.periode.index', [
            'periodes' => $periodes,
        ]);
    }

    public function create(): View
    {
        return view('admin.periode.form', [
            'periode' => new PeriodeAkademik,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_periode' => ['required', 'string', 'max:100', 'unique:periode_akademik,nama_periode'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'nama_periode.required' => 'Nama periode akademik wajib diisi.',
            'nama_periode.unique' => 'Nama periode akademik sudah ada.',
        ]);

        $shouldActivate = $request->boolean('is_active');

        DB::transaction(function () use ($validated, $shouldActivate): void {
            if ($shouldActivate) {
                PeriodeAkademik::query()->update(['is_active' => false]);
            }

            PeriodeAkademik::create([
                'nama_periode' => $validated['nama_periode'],
                'is_active' => $shouldActivate,
            ]);
        });

        return redirect()
            ->route('admin.periode-akademik.index')
            ->with('status', 'Periode akademik berhasil ditambahkan.');
    }

    public function edit(PeriodeAkademik $periode): View
    {
        return view('admin.periode.form', [
            'periode' => $periode,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, PeriodeAkademik $periode): RedirectResponse
    {
        $validated = $request->validate([
            'nama_periode' => ['required', 'string', 'max:100', Rule::unique('periode_akademik', 'nama_periode')->ignore($periode->id)],
        ], [
            'nama_periode.required' => 'Nama periode akademik wajib diisi.',
            'nama_periode.unique' => 'Nama periode akademik sudah ada.',
        ]);

        $periode->update([
            'nama_periode' => $validated['nama_periode'],
        ]);

        return redirect()
            ->route('admin.periode-akademik.index')
            ->with('status', 'Periode akademik berhasil diperbarui.');
    }

    public function activate(PeriodeAkademik $periode): RedirectResponse
    {
        DB::transaction(function () use ($periode): void {
            PeriodeAkademik::query()->update(['is_active' => false]);
            $periode->update(['is_active' => true]);
        });

        return redirect()
            ->route('admin.periode-akademik.index')
            ->with('status', "Periode {$periode->nama_periode} berhasil diaktifkan.");
    }

    public function destroy(PeriodeAkademik $periode): RedirectResponse
    {
        if ($periode->is_active) {
            return back()->with('error', 'Periode akademik yang sedang aktif tidak dapat dihapus.');
        }

        if ($periode->kelasPerkuliahan()->exists() || $periode->biayaKuliah()->exists()) {
            return back()->with('error', 'Periode akademik tidak dapat dihapus karena masih memiliki data kelas atau tagihan.');
        }

        $periode->delete();

        return redirect()
            ->route('admin.periode-akademik.index')
            ->with('status', 'Periode akademik berhasil dihapus.');
    }
}
