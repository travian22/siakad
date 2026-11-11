<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\BiayaKuliah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BiayaController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if($mahasiswa === null, 404, 'Data mahasiswa tidak ditemukan.');
        $mahasiswa->load('programStudi');

        $tagihans = BiayaKuliah::query()
            ->with('periodeAkademik')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('periode_akademik_id')
            ->get();

        $totalBelumBayar = $tagihans
            ->where('status_bayar', 'belum_bayar')
            ->sum(fn (BiayaKuliah $biaya): float => (float) $biaya->nominal_tagihan);

        $totalLunas = $tagihans
            ->where('status_bayar', 'lunas')
            ->sum(fn (BiayaKuliah $biaya): float => (float) $biaya->nominal_tagihan);

        return view('mahasiswa.biaya', [
            'mahasiswa' => $mahasiswa,
            'tagihans' => $tagihans,
            'totalBelumBayar' => $totalBelumBayar,
            'totalLunas' => $totalLunas,
        ]);
    }
}
