<x-layouts.app title="Dashboard Akademik Mahasiswa">
    <x-page-header
        title="Dashboard Akademik"
        :subtitle="$mahasiswa->npm.' | '.$mahasiswa->programStudi->nama_prodi.' (Angkatan '.$mahasiswa->angkatan.')'"
    >
        <x-slot:actions>
            <a
                href="{{ route('mahasiswa.jadwal') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Lihat Jadwal Kuliah
            </a>
        </x-slot:actions>
    </x-page-header>

    <section aria-label="Ringkasan Prestasi Akademik" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">IPK Kumulatif</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ number_format($summary['ipk'], 2) }}
            </p>
            <p class="mt-2 text-xs text-slate-600">Skala maksimal 4.00</p>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">SKS Kumulatif</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ $summary['sks_kumulatif'] }} SKS
            </p>
            <p class="mt-2 text-xs text-slate-600">Total SKS lulus dari {{ $summary['sks_dinilai'] }} SKS dinilai</p>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">SKS Periode Aktif</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ $activeSks }} SKS
            </p>
            <p class="mt-2 text-xs text-slate-600">
                {{ $activePeriode ? $activeScheduleCount.' mata kuliah pada '.$activePeriode->nama_periode : 'Belum ada periode aktif' }}
            </p>
        </div>

        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Status Biaya Periode Aktif</p>
            <div class="mt-2">
                @if ($activeBill && $activeBill->status_bayar === 'lunas')
                    <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-900">
                        Lunas
                    </span>
                @elseif ($activeBill)
                    <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-900">
                        Belum Bayar
                    </span>
                @else
                    <span class="inline-flex items-center rounded-md border border-slate-400 bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-800">
                        Belum Ada Tagihan
                    </span>
                @endif
            </div>
            <a href="{{ route('mahasiswa.biaya') }}" class="mt-3 inline-flex min-h-[44px] items-center text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900">
                Detail tagihan
            </a>
        </div>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
        <section class="rounded-lg border border-slate-300 bg-white p-5 sm:p-6 lg:col-span-7">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Perkembangan IPS per Semester (Skala 0.00 - 4.00)</h2>
                    <p class="text-sm text-slate-600">Indeks Prestasi Semester berdasarkan mata kuliah yang telah dinilai.</p>
                </div>
            </div>

            @if (count($summary['semester_ips']) === 0)
                <div class="mt-6 rounded-md border border-dashed border-slate-400 bg-gray-50 p-6 text-center">
                    <p class="text-sm text-slate-700">Belum ada nilai yang dipublikasikan untuk menghitung grafik IPS.</p>
                </div>
            @else
                <div class="mt-6 flex flex-col gap-4" role="img" aria-label="Grafik Indeks Prestasi Semester per periode">
                    @foreach ($summary['semester_ips'] as $row)
                        @php
                            $barPercent = min(100, (int) round(($row['ips'] / 4.0) * 100));
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-800">
                                    {{ $row['periode']->nama_periode }}
                                    <span class="text-xs text-slate-600">({{ $row['sks'] }} SKS, {{ $row['matkul_count'] }} MK)</span>
                                </span>
                                <span class="font-semibold text-slate-900 shrink-0">
                                    IPS {{ number_format($row['ips'], 2) }}
                                </span>
                            </div>
                            <div class="mt-1.5 h-3.5 w-full overflow-hidden rounded-sm bg-slate-200 border border-slate-300">
                                <div
                                    class="h-full bg-academic-navy"
                                    style="width: {{ $row['ips'] > 0 ? max(4, $barPercent) : 0 }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-slate-300 bg-white p-5 sm:p-6 lg:col-span-5 flex flex-col justify-between gap-5">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Akses Cepat Akademik</h2>
                <p class="text-sm text-slate-600">Menu utama untuk memantau perkuliahan dan administrasi Anda.</p>

                <div class="mt-4 flex flex-col gap-3">
                    <a
                        href="{{ route('mahasiswa.jadwal') }}"
                        class="min-h-[44px] flex items-center justify-between rounded-md border border-slate-300 bg-gray-50 px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-100"
                    >
                        <span>Jadwal Kuliah Semester Aktif</span>
                        <span class="text-xs font-semibold text-academic-navy">{{ $activeScheduleCount }} kelas</span>
                    </a>
                    <a
                        href="{{ route('mahasiswa.khs') }}"
                        class="min-h-[44px] flex items-center justify-between rounded-md border border-slate-300 bg-gray-50 px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-100"
                    >
                        <span>Kartu Hasil Studi (KHS)</span>
                        <span class="text-xs font-semibold text-academic-navy">IPK {{ number_format($summary['ipk'], 2) }}</span>
                    </a>
                    <a
                        href="{{ route('mahasiswa.biaya') }}"
                        class="min-h-[44px] flex items-center justify-between rounded-md border border-slate-300 bg-gray-50 px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-100"
                    >
                        <span>Informasi Biaya Kuliah</span>
                        <span class="text-xs font-semibold text-academic-navy">Lihat status</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>

