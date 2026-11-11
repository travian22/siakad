<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dosen\UpdateProfilRequest;
use App\Services\ProfilePhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        $dosen = $request->user()->dosen;
        abort_if($dosen === null, 404, 'Data profil dosen tidak ditemukan.');

        return view('dosen.profil', [
            'dosen' => $dosen,
            'isProfileComplete' => $dosen->isProfileComplete(),
        ]);
    }

    public function update(UpdateProfilRequest $request, ProfilePhotoService $photoService): RedirectResponse
    {
        $dosen = $request->user()->dosen;
        abort_if($dosen === null, 404, 'Data profil dosen tidak ditemukan.');

        $validated = $request->validated();
        $photoPath = $photoService->replace(
            $request->file('foto_profil'),
            $dosen->foto_profil,
            'profiles/dosen'
        );

        $dosen->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'telepon' => $validated['telepon'],
            'alamat' => $validated['alamat'],
            'foto_profil' => $photoPath,
        ]);

        return redirect()
            ->route('dosen.dashboard')
            ->with('status', 'Profil dosen berhasil disimpan.');
    }
}
