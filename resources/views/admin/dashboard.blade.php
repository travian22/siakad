<x-layouts.app title="Dashboard Admin">
    <x-page-header
        title="Dashboard Akademik"
        :subtitle="$activePeriode ? 'Periode aktif saat ini: '.$activePeriode->nama_periode : 'Belum ada periode akademik yang diaktifkan.'"
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.kelas.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Buat Kelas Perkuliahan
            </a>
        </x-slot:actions>
    </x-page-header>

    <section aria-label="Ringkasan Data Akademik" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Mahasiswa Terdaftar</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($totalMahasiswa) }}</p>
            <a href="{{ route('admin.master-mahasiswa.index') }}" class="mt-3 inline-flex min-h-[44px] items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900">
                Kelola mahasiswa
            </a>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Dosen Terdaftar</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($totalDosen) }}</p>
            <a href="{{ route('admin.master-dosen.index') }}" class="mt-3 inline-flex min-h-[44px] items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900">
                Kelola dosen
            </a>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Program Studi</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($totalProdi) }}</p>
            <a href="{{ route('admin.master-prodi.index') }}" class="mt-3 inline-flex min-h-[44px] items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900">
                Kelola program studi
            </a>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">
                {{ $activePeriode ? 'Kelas Periode Aktif' : 'Total Kelas' }}
            </p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($totalKelasAktif) }}</p>
            <a href="{{ route('admin.kelas.index') }}" class="mt-3 inline-flex min-h-[44px] items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900">
                Kelola jadwal kelas
            </a>
        </div>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
        <section class="rounded-lg border border-slate-300 bg-white p-5 sm:p-6 lg:col-span-7">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Jumlah Mahasiswa per Program Studi</h2>
                    <p class="text-sm text-slate-600">Distribusi mahasiswa terdaftar di setiap program studi aktif.</p>
                </div>
                <span class="text-xs font-semibold text-slate-700">Total: {{ number_format($totalMahasiswa) }} mahasiswa</span>
            </div>

            @if ($prodiStats->isEmpty())
                <div class="mt-6 rounded-md border border-dashed border-slate-400 bg-gray-50 p-6 text-center">
                    <p class="text-sm text-slate-700">Belum ada data program studi.</p>
                    <a
                        href="{{ route('admin.master-prodi.create') }}"
                        class="mt-3 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                    >
                        Tambah Program Studi
                    </a>
                </div>
            @else
                <div class="mt-5 flex flex-col gap-4" role="img" aria-label="Grafik jumlah mahasiswa per program studi">
                    @foreach ($prodiStats as $prodi)
                        @php
                            $percentage = (int) round(($prodi->mahasiswa_count / $maxMahasiswaPerProdi) * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-800 truncate">
                                    {{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}
                                </span>
                                <span class="font-semibold text-slate-900 shrink-0">
                                    {{ number_format($prodi->mahasiswa_count) }} mhs
                                </span>
                            </div>
                            <div class="mt-1.5 h-3 w-full overflow-hidden rounded-sm bg-slate-200 border border-slate-300">
                                <div
                                    class="h-full bg-academic-navy"
                                    style="width: {{ $prodi->mahasiswa_count > 0 ? max(4, $percentage) : 0 }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-slate-300 bg-white p-5 sm:p-6 lg:col-span-5 flex flex-col justify-between gap-5">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ringkasan Akademik &amp; Tagihan</h2>
                <p class="text-sm text-slate-600">Status administrasi pada periode berjalan.</p>

                <dl class="mt-4 flex flex-col gap-3 text-sm">
                    <div class="flex items-center justify-between rounded-md border border-slate-200 bg-gray-50 px-4 py-3">
                        <dt class="text-slate-700">Mata Kuliah Tersedia</dt>
                        <dd class="font-semibold text-slate-900">{{ number_format($totalMatkul) }}</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-md border border-slate-200 bg-gray-50 px-4 py-3">
                        <dt class="text-slate-700">Tagihan Lunas</dt>
                        <dd class="font-semibold text-emerald-900">{{ number_format($tagihanSummary['lunas']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-md border border-slate-200 bg-gray-50 px-4 py-3">
                        <dt class="text-slate-700">Tagihan Belum Bayar</dt>
                        <dd class="font-semibold text-amber-900">{{ number_format($tagihanSummary['belum_bayar']) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-slate-200 pt-4">
                <a
                    href="{{ route('admin.periode-akademik.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Atur Periode Aktif
                </a>
                <a
                    href="{{ route('admin.keuangan.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Kelola Tagihan
                </a>
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-lg border border-slate-300 bg-white">
        <div class="flex flex-col gap-2 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Jadwal Kelas Terbaru</h2>
                <p class="text-sm text-slate-600">Daftar kelas perkuliahan terbaru pada periode aktif.</p>
            </div>
            <a
                href="{{ route('admin.kelas.index') }}"
                class="min-h-[44px] inline-flex items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900"
            >
                Lihat semua kelas
            </a>
        </div>

        @if ($recentKelas->isEmpty())
            <div class="p-6 text-center">
                <p class="text-sm text-slate-700">Belum ada kelas perkuliahan yang dijadwalkan.</p>
                <a
                    href="{{ route('admin.kelas.create') }}"
                    class="mt-3 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Jadwalkan Kelas Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Mata Kuliah</th>
                            <th scope="col" class="px-4 py-3">Kelas</th>
                            <th scope="col" class="px-4 py-3">Dosen</th>
                            <th scope="col" class="px-4 py-3">Jadwal &amp; Ruang</th>
                            <th scope="col" class="px-4 py-3">Peserta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($recentKelas as $kelas)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $kelas->mataKuliah->kode_mk }} - {{ $kelas->mataKuliah->nama_mk }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->nama_kelas }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->dosen->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $kelas->hari }}, {{ substr($kelas->jam_mulai, 0, 5) }}-{{ substr($kelas->jam_selesai, 0, 5) }} ({{ $kelas->ruangan }})
                                </td>
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('admin.kelas.peserta', $kelas) }}"
                                        class="inline-flex min-h-[44px] items-center font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900"
                                    >
                                        {{ $kelas->peserta_kelas_count }} mahasiswa
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

