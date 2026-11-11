<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KelasPerkuliahanRequest;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeAkademik;
use App\Models\PesertaKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KelasPerkuliahanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $periodeId = $request->integer('periode_akademik_id');

        $kelasList = KelasPerkuliahan::query()
            ->with(['periodeAkademik', 'mataKuliah', 'dosen'])
            ->withCount('pesertaKelas')
            ->when($periodeId > 0, fn ($query) => $query->where('periode_akademik_id', $periodeId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('nama_kelas', 'like', '%'.$search.'%')
                        ->orWhere('ruangan', 'like', '%'.$search.'%')
                        ->orWhereHas('mataKuliah', fn ($mk) => $mk->where('nama_mk', 'like', '%'.$search.'%')->orWhere('kode_mk', 'like', '%'.$search.'%'))
                        ->orWhereHas('dosen', fn ($d) => $d->where('nama_lengkap', 'like', '%'.$search.'%'));
                });
            })
            ->orderByDesc('periode_akademik_id')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        $periodes = PeriodeAkademik::query()->ordered()->get();

        return view('admin.kelas.index', [
            'kelasList' => $kelasList,
            'periodes' => $periodes,
            'search' => $search,
            'periodeId' => $periodeId,
        ]);
    }

    public function create(): View
    {
        $activePeriode = PeriodeAkademik::currentActive();

        return view('admin.kelas.form', [
            'kelas' => new KelasPerkuliahan(['periode_akademik_id' => $activePeriode?->id]),
            'periodes' => PeriodeAkademik::query()->ordered()->get(),
            'mataKuliahs' => MataKuliah::query()->orderBy('kode_mk')->get(),
            'dosens' => Dosen::query()->orderBy('nama_lengkap')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(KelasPerkuliahanRequest $request): RedirectResponse
    {
        KelasPerkuliahan::create($request->validated());

        return redirect()
            ->route('admin.kelas.index')
            ->with('status', 'Kelas perkuliahan berhasil dijadwalkan.');
    }

    public function edit(KelasPerkuliahan $kelas): View
    {
        return view('admin.kelas.form', [
            'kelas' => $kelas,
            'periodes' => PeriodeAkademik::query()->ordered()->get(),
            'mataKuliahs' => MataKuliah::query()->orderBy('kode_mk')->get(),
            'dosens' => Dosen::query()->orderBy('nama_lengkap')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(KelasPerkuliahanRequest $request, KelasPerkuliahan $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()
            ->route('admin.kelas.index')
            ->with('status', 'Jadwal kelas perkuliahan berhasil diperbarui.');
    }

    public function destroy(KelasPerkuliahan $kelas): RedirectResponse
    {
        if ($kelas->pesertaKelas()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki peserta terdaftar.');
        }

        $kelas->delete();

        return redirect()
            ->route('admin.kelas.index')
            ->with('status', 'Kelas perkuliahan berhasil dihapus.');
    }

    public function peserta(KelasPerkuliahan $kelas): View
    {
        $kelas->load(['periodeAkademik', 'mataKuliah', 'dosen']);

        $enrolledPeserta = PesertaKelas::query()
            ->with(['mahasiswa.programStudi'])
            ->where('kelas_perkuliahan_id', $kelas->id)
            ->orderBy('id')
            ->get();

        $enrolledIds = $enrolledPeserta->pluck('mahasiswa_id');

        $availableMahasiswas = Mahasiswa::query()
            ->with('programStudi')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('npm')
            ->get();

        return view('admin.kelas.peserta', [
            'kelas' => $kelas,
            'enrolledPeserta' => $enrolledPeserta,
            'availableMahasiswas' => $availableMahasiswas,
        ]);
    }

    public function storePeserta(Request $request, KelasPerkuliahan $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_ids' => ['required', 'array', 'min:1'],
            'mahasiswa_ids.*' => ['required', 'integer', 'exists:mahasiswa,id'],
        ], [
            'mahasiswa_ids.required' => 'Pilih minimal satu mahasiswa untuk didaftarkan ke kelas.',
            'mahasiswa_ids.min' => 'Pilih minimal satu mahasiswa untuk didaftarkan ke kelas.',
        ]);

        DB::transaction(function () use ($kelas, $validated): void {
            foreach ($validated['mahasiswa_ids'] as $mahasiswaId) {
                PesertaKelas::firstOrCreate([
                    'kelas_perkuliahan_id' => $kelas->id,
                    'mahasiswa_id' => $mahasiswaId,
                ]);
            }
        });

        return redirect()
            ->route('admin.kelas.peserta', $kelas)
            ->with('status', 'Peserta kelas berhasil ditambahkan.');
    }

    public function destroyPeserta(KelasPerkuliahan $kelas, PesertaKelas $peserta): RedirectResponse
    {
        abort_if($peserta->kelas_perkuliahan_id !== $kelas->id, 404);

        if ($peserta->nilai_akhir !== null) {
            return back()->with('error', 'Peserta yang sudah memiliki nilai tidak dapat dihapus dari kelas.');
        }

        $peserta->delete();

        return redirect()
            ->route('admin.kelas.peserta', $kelas)
            ->with('status', 'Mahasiswa berhasil dihapus dari kelas.');
    }
}
