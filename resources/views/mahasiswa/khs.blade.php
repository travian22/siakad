<x-layouts.app title="Kartu Hasil Studi (KHS)">
    <x-page-header
        title="Kartu Hasil Studi (KHS)"
        :subtitle="'IPK Kumulatif: '.number_format($summary['ipk'], 2).' | Total SKS Lulus: '.$summary['sks_kumulatif'].' SKS'"
    />

    <div class="rounded-lg border border-slate-300 bg-white p-4 mb-6">
        <form method="GET" action="{{ route('mahasiswa.khs') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1 max-w-md">
                <label for="periode_akademik_id" class="block text-sm font-medium text-slate-700">Filter Periode Akademik</label>
                <select
                    id="periode_akademik_id"
                    name="periode_akademik_id"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Semua Periode Akademik</option>
                    @foreach ($availablePeriodes as $periode)
                        <option value="{{ $periode->id }}" @selected($selectedPeriodeId === $periode->id)>
                            {{ $periode->nama_periode }}{{ $periode->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Tampilkan
                </button>
                @if ($selectedPeriodeId > 0)
                    <a
                        href="{{ route('mahasiswa.khs') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Semua Periode
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if ($semesterGroups->isEmpty())
        <div class="rounded-lg border border-slate-300 bg-white p-8 text-center">
            <p class="text-sm text-slate-700">Belum ada riwayat mata kuliah dan nilai untuk ditampilkan.</p>
        </div>
    @else
        <div class="flex flex-col gap-6">
            @foreach ($semesterGroups as $group)
                <section class="rounded-lg border border-slate-300 bg-white">
                    <div class="border-b border-slate-200 p-4 sm:px-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-gray-50">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Periode {{ $group['periode']->nama_periode }}
                            </h2>
                            <p class="text-xs text-slate-600">
                                Total SKS Diambil: {{ $group['metrics']['sks_diambil'] }} SKS | SKS Dinilai: {{ $group['metrics']['sks_dinilai'] }} SKS
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-md border border-slate-400 bg-white px-3 py-1 text-sm font-semibold text-slate-900">
                            IPS: {{ number_format($group['metrics']['ips'], 2) }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Kode MK</th>
                                    <th scope="col" class="px-4 py-3">Mata Kuliah</th>
                                    <th scope="col" class="px-4 py-3">SKS</th>
                                    <th scope="col" class="px-4 py-3">Tugas</th>
                                    <th scope="col" class="px-4 py-3">UTS</th>
                                    <th scope="col" class="px-4 py-3">UAS</th>
                                    <th scope="col" class="px-4 py-3">Nilai Akhir</th>
                                    <th scope="col" class="px-4 py-3">Nilai Huruf</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach ($group['items'] as $peserta)
                                    @php
                                        $kelas = $peserta->kelasPerkuliahan;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $kelas->mataKuliah->kode_mk }}</td>
                                        <td class="px-4 py-3 text-slate-800">
                                            {{ $kelas->mataKuliah->nama_mk }}
                                            <span class="block text-xs text-slate-600">{{ $kelas->nama_kelas }} | {{ $kelas->dosen->nama_lengkap }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">{{ $kelas->mataKuliah->sks }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $peserta->nilai_tugas ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $peserta->nilai_uts ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $peserta->nilai_uas ?? '-' }}</td>
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $peserta->nilai_akhir ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($peserta->nilai_huruf)
                                                <span class="inline-flex items-center rounded-md border border-slate-400 bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-900">
                                                    {{ $peserta->nilai_huruf }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-600">Belum dinilai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</x-layouts.app>

