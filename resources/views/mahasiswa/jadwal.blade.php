<x-layouts.app title="Jadwal Kuliah Mahasiswa">
    <x-page-header
        title="Jadwal Kuliah"
        :subtitle="$activePeriode ? 'Daftar mata kuliah yang Anda ikuti pada periode '.$activePeriode->nama_periode.' (Total '.$totalSks.' SKS).' : 'Belum ada periode akademik yang aktif saat ini.'"
    />

    <div class="rounded-lg border border-slate-300 bg-white">
        @if (! $activePeriode)
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Belum ada periode akademik aktif. Silakan hubungi bagian akademik.</p>
            </div>
        @elseif ($jadwalList->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Anda belum terdaftar pada kelas perkuliahan apa pun di periode {{ $activePeriode->nama_periode }}.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Hari &amp; Jam</th>
                            <th scope="col" class="px-4 py-3">Kode MK</th>
                            <th scope="col" class="px-4 py-3">Mata Kuliah</th>
                            <th scope="col" class="px-4 py-3">Kelas</th>
                            <th scope="col" class="px-4 py-3">SKS</th>
                            <th scope="col" class="px-4 py-3">Ruangan</th>
                            <th scope="col" class="px-4 py-3">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($jadwalList as $peserta)
                            @php
                                $kelas = $peserta->kelasPerkuliahan;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $kelas->hari }}, {{ substr($kelas->jam_mulai, 0, 5) }}-{{ substr($kelas->jam_selesai, 0, 5) }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $kelas->mataKuliah->kode_mk }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $kelas->mataKuliah->nama_mk }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->nama_kelas }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->mataKuliah->sks }} SKS</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->ruangan }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->dosen->nama_lengkap }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>

