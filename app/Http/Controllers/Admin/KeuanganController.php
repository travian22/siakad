<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BiayaKuliahRequest;
use App\Models\BiayaKuliah;
use App\Models\Mahasiswa;
use App\Models\PeriodeAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $periodeId = $request->integer('periode_akademik_id');
        $statusBayar = $request->string('status_bayar')->toString();

        $tagihans = BiayaKuliah::query()
            ->with(['mahasiswa.programStudi', 'periodeAkademik'])
            ->when($periodeId > 0, fn ($query) => $query->where('periode_akademik_id', $periodeId))
            ->when(in_array($statusBayar, ['belum_bayar', 'lunas'], true), fn ($query) => $query->where('status_bayar', $statusBayar))
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('mahasiswa', function ($mhs) use ($search): void {
                    $mhs->where('npm', 'like', '%'.$search.'%')
                        ->orWhere('nama_lengkap', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $periodes = PeriodeAkademik::query()->ordered()->get();

        return view('admin.keuangan.index', [
            'tagihans' => $tagihans,
            'periodes' => $periodes,
            'search' => $search,
            'periodeId' => $periodeId,
            'statusBayar' => $statusBayar,
        ]);
    }

    public function create(): View
    {
        $activePeriode = PeriodeAkademik::currentActive();

        return view('admin.keuangan.form', [
            'biaya' => new BiayaKuliah([
                'periode_akademik_id' => $activePeriode?->id,
                'status_bayar' => 'belum_bayar',
            ]),
            'mahasiswas' => Mahasiswa::query()->with('programStudi')->orderBy('npm')->get(),
            'periodes' => PeriodeAkademik::query()->ordered()->get(),
            'isEdit' => false,
        ]);
    }

    public function store(BiayaKuliahRequest $request): RedirectResponse
    {
        BiayaKuliah::create($request->validated());

        return redirect()
            ->route('admin.keuangan.index')
            ->with('status', 'Tagihan biaya kuliah berhasil ditambahkan.');
    }

    public function edit(BiayaKuliah $biaya): View
    {
        return view('admin.keuangan.form', [
            'biaya' => $biaya,
            'mahasiswas' => Mahasiswa::query()->with('programStudi')->orderBy('npm')->get(),
            'periodes' => PeriodeAkademik::query()->ordered()->get(),
            'isEdit' => true,
        ]);
    }

    public function update(BiayaKuliahRequest $request, BiayaKuliah $biaya): RedirectResponse
    {
        $biaya->update($request->validated());

        return redirect()
            ->route('admin.keuangan.index')
            ->with('status', 'Tagihan biaya kuliah berhasil diperbarui.');
    }

    public function toggleStatus(BiayaKuliah $biaya): RedirectResponse
    {
        $nextStatus = $biaya->status_bayar === 'lunas' ? 'belum_bayar' : 'lunas';
        $biaya->update(['status_bayar' => $nextStatus]);

        return back()->with('status', 'Status pembayaran berhasil diperbarui.');
    }

    public function destroy(BiayaKuliah $biaya): RedirectResponse
    {
        $biaya->delete();

        return redirect()
            ->route('admin.keuangan.index')
            ->with('status', 'Tagihan biaya kuliah berhasil dihapus.');
    }
}
