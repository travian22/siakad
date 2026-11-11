<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MahasiswaRequest;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $prodiId = $request->integer('program_studi_id');

        $mahasiswas = Mahasiswa::query()
            ->with(['user', 'programStudi'])
            ->when($prodiId > 0, fn ($query) => $query->where('program_studi_id', $prodiId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('npm', 'like', '%'.$search.'%')
                        ->orWhere('nama_lengkap', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%'.$search.'%'));
                });
            })
            ->orderBy('npm')
            ->paginate(10)
            ->withQueryString();

        $programStudis = ProgramStudi::query()->orderBy('nama_prodi')->get();

        return view('admin.mahasiswa.index', [
            'mahasiswas' => $mahasiswas,
            'programStudis' => $programStudis,
            'search' => $search,
            'prodiId' => $prodiId,
        ]);
    }

    public function create(): View
    {
        return view('admin.mahasiswa.form', [
            'mahasiswa' => new Mahasiswa,
            'programStudis' => ProgramStudi::query()->orderBy('nama_prodi')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(MahasiswaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'program_studi_id' => $validated['program_studi_id'],
                'npm' => $validated['npm'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'angkatan' => $validated['angkatan'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.master-mahasiswa.index')
            ->with('status', 'Data mahasiswa dan akun login berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load('user');

        return view('admin.mahasiswa.form', [
            'mahasiswa' => $mahasiswa,
            'programStudis' => ProgramStudi::query()->orderBy('nama_prodi')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(MahasiswaRequest $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $mahasiswa): void {
            $userData = [
                'username' => $validated['username'],
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $mahasiswa->user->update($userData);

            $mahasiswa->update([
                'program_studi_id' => $validated['program_studi_id'],
                'npm' => $validated['npm'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'angkatan' => $validated['angkatan'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.master-mahasiswa.index')
            ->with('status', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        if ($mahasiswa->pesertaKelas()->exists() || $mahasiswa->biayaKuliah()->exists()) {
            return back()->with('error', 'Mahasiswa tidak dapat dihapus karena sudah memiliki data kelas atau tagihan kuliah.');
        }

        DB::transaction(function () use ($mahasiswa): void {
            $user = $mahasiswa->user;
            $mahasiswa->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.master-mahasiswa.index')
            ->with('status', 'Data mahasiswa berhasil dihapus.');
    }
}
