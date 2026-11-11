<x-layouts.app title="Jadwal Mengajar Dosen">
    <x-page-header
        title="Jadwal Mengajar"
        :subtitle="$activePeriode ? 'Daftar kelas yang Anda ampu pada periode '.$activePeriode->nama_periode.'.' : 'Belum ada periode akademik yang diaktifkan oleh Admin.'"
    />

    <section aria-label="Ringkasan Mengajar" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Periode Aktif</p>
            <p class="mt-2 text-lg font-semibold text-slate-900">
                {{ $activePeriode?->nama_periode ?? 'Belum Aktif' }}
            </p>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Kelas Diampu</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">
                {{ number_format($jadwalMengajar->count()) }} kelas
            </p>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Total Mahasiswa Diampu</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">
                {{ number_format($jadwalMengajar->sum('peserta_kelas_count')) }} mahasiswa
            </p>
        </div>
    </section>

    <section class="mt-6 rounded-lg border border-slate-300 bg-white">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-base font-semibold text-slate-900">Daftar Kelas Perkuliahan</h2>
            <p class="text-sm text-slate-600">Klik tombol Kelola &amp; Input Nilai pada kelas untuk melihat daftar mahasiswa dan mengisi nilai.</p>
        </div>

        @if (! $activePeriode)
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Saat ini belum ada periode akademik yang aktif. Hubungi Admin akademik untuk mengaktifkan periode.</p>
            </div>
        @elseif ($jadwalMengajar->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Anda belum memiliki jadwal mengajar pada periode {{ $activePeriode->nama_periode }}.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Kode &amp; Mata Kuliah</th>
                            <th scope="col" class="px-4 py-3">Kelas</th>
                            <th scope="col" class="px-4 py-3">SKS</th>
                            <th scope="col" class="px-4 py-3">Hari &amp; Jam</th>
                            <th scope="col" class="px-4 py-3">Ruangan</th>
                            <th scope="col" class="px-4 py-3">Progres Nilai</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($jadwalMengajar as $kelas)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $kelas->mataKuliah->kode_mk }} - {{ $kelas->mataKuliah->nama_mk }}
                                </td>
                                <td class="px-4 py-3 text-slate-800">{{ $kelas->nama_kelas }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->mataKuliah->sks }} SKS</td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $kelas->hari }}, {{ substr($kelas->jam_mulai, 0, 5) }}-{{ substr($kelas->jam_selesai, 0, 5) }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->ruangan }}</td>
                                <td class="px-4 py-3">
                                    @if ($kelas->peserta_kelas_count > 0 && $kelas->dinilai_count === $kelas->peserta_kelas_count)
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Lengkap ({{ $kelas->dinilai_count }}/{{ $kelas->peserta_kelas_count }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                            {{ $kelas->dinilai_count }}/{{ $kelas->peserta_kelas_count }} dinilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a
                                        href="{{ route('dosen.kelas.nilai', $kelas->id) }}"
                                        class="min-h-[44px] inline-flex items-center rounded-md bg-academic-navy px-3.5 py-1.5 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                                    >
                                        Input Nilai
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>

