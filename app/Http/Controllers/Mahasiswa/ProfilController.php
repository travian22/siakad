<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\UpdateProfilRequest;
use App\Services\ProfilePhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data profil mahasiswa tidak ditemukan.');
        $mahasiswa->load('programStudi');

        return view('mahasiswa.profil', [
            'mahasiswa' => $mahasiswa,
            'isProfileComplete' => $mahasiswa->isProfileComplete(),
        ]);
    }

    public function update(UpdateProfilRequest $request, ProfilePhotoService $photoService): RedirectResponse
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data profil mahasiswa tidak ditemukan.');

        $validated = $request->validated();
        $photoPath = $photoService->replace(
            $request->file('foto_profil'),
            $mahasiswa->foto_profil,
            'profiles/mahasiswa'
        );

        $mahasiswa->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'foto_profil' => $photoPath,
        ]);

        return redirect()
            ->route('mahasiswa.dashboard')
            ->with('status', 'Biodata mahasiswa berhasil disimpan.');
    }
}
