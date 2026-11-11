@php
    $showFormTab = $errors->any();
@endphp

<x-layouts.app :title="'Nilai: '.$kelas->mataKuliah->nama_mk">
    <x-page-header
        :title="$kelas->mataKuliah->kode_mk.' - '.$kelas->mataKuliah->nama_mk.' ('.$kelas->nama_kelas.')'"
        :subtitle="$kelas->periodeAkademik->nama_periode.' | '.$kelas->hari.', '.substr($kelas->jam_mulai, 0, 5).'-'.substr($kelas->jam_selesai, 0, 5).' | Ruang '.$kelas->ruangan.' | '.$kelas->mataKuliah->sks.' SKS'"
    >
        <x-slot:actions>
            <a
                href="{{ route('dosen.dashboard') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Jadwal
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <div
            class="border-b border-slate-200 px-4 pt-3 flex flex-wrap items-center justify-between gap-3"
            role="tablist"
            aria-label="Navigasi Daftar Mahasiswa dan Form Input Nilai"
        >
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    id="btn-tab-peserta"
                    role="tab"
                    aria-controls="tab-peserta"
                    aria-selected="{{ $showFormTab ? 'false' : 'true' }}"
                    class="min-h-[44px] px-4 py-2 text-sm font-semibold border-b-2 transition-colors {{ $showFormTab ? 'border-transparent text-slate-600 hover:text-slate-900' : 'border-academic-navy text-academic-navy' }}"
                >
                    Daftar Mahasiswa ({{ $pesertas->count() }})
                </button>

                <button
                    type="button"
                    id="btn-tab-form-nilai"
                    role="tab"
                    aria-controls="tab-form-nilai"
                    aria-selected="{{ $showFormTab ? 'true' : 'false' }}"
                    class="min-h-[44px] px-4 py-2 text-sm font-semibold border-b-2 transition-colors {{ $showFormTab ? 'border-academic-navy text-academic-navy' : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Input Nilai
                </button>
            </div>

            <p class="pb-2 text-xs text-slate-600">
                Formula: Tugas 20% + UTS 30% + UAS 50%
            </p>
        </div>

        @if ($pesertas->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Belum ada mahasiswa yang terdaftar pada kelas ini. Hubungi Admin akademik untuk menambahkan peserta kelas.</p>
            </div>
        @else
            <div id="tab-peserta" class="{{ $showFormTab ? 'hidden' : '' }}">
                <div class="p-4 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-gray-50">
                    <p class="text-sm text-slate-700">
                        Menampilkan nilai akhir dan nilai huruf tersimpan untuk setiap mahasiswa.
                    </p>
                    <button
                        type="button"
                        id="btn-switch-to-form"
                        class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                    >
                        Buka Form Input Nilai
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">NPM</th>
                                <th scope="col" class="px-4 py-3">Nama Mahasiswa</th>
                                <th scope="col" class="px-4 py-3">Program Studi</th>
                                <th scope="col" class="px-4 py-3">Tugas (20%)</th>
                                <th scope="col" class="px-4 py-3">UTS (30%)</th>
                                <th scope="col" class="px-4 py-3">UAS (50%)</th>
                                <th scope="col" class="px-4 py-3">Nilai Akhir</th>
                                <th scope="col" class="px-4 py-3">Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($pesertas as $peserta)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $peserta->mahasiswa->npm }}</td>
                                    <td class="px-4 py-3 text-slate-800">{{ $peserta->mahasiswa->nama_lengkap }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $peserta->mahasiswa->programStudi->nama_prodi }}</td>
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
                                            <span class="text-slate-600">Belum ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-form-nilai" class="{{ $showFormTab ? '' : 'hidden' }}">
                <form method="POST" action="{{ route('dosen.kelas.nilai.store', $kelas->id) }}">
                    @csrf

                    <div class="p-4 border-b border-slate-200 bg-gray-50 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-700">
                            Rentang Huruf: A (&ge; 85), B (75-84.99), C (60-74.99), D (50-59.99), E (&lt; 50).
                        </p>
                        <span class="text-xs font-medium text-slate-600">Skala input: 0 sampai 100</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3">NPM</th>
                                    <th scope="col" class="px-4 py-3">Nama Mahasiswa</th>
                                    <th scope="col" class="px-4 py-3">Tugas (20%)</th>
                                    <th scope="col" class="px-4 py-3">UTS (30%)</th>
                                    <th scope="col" class="px-4 py-3">UAS (50%)</th>
                                    <th scope="col" class="px-4 py-3">Estimasi Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach ($pesertas as $peserta)
                                    <tr class="grade-row" data-peserta-id="{{ $peserta->id }}">
                                        <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                            {{ $peserta->mahasiswa->npm }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-800">
                                            {{ $peserta->mahasiswa->nama_lengkap }}
                                            @error('nilai.'.$peserta->id)
                                                <p class="mt-1 text-xs text-red-800" role="alert">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="number"
                                                name="nilai[{{ $peserta->id }}][tugas]"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                aria-label="Nilai Tugas untuk {{ $peserta->mahasiswa->nama_lengkap }}"
                                                value="{{ old('nilai.'.$peserta->id.'.tugas', $peserta->nilai_tugas !== null ? (string) (float) $peserta->nilai_tugas : '') }}"
                                                class="grade-input grade-tugas w-28 min-h-[44px] rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm text-slate-900"
                                            >
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="number"
                                                name="nilai[{{ $peserta->id }}][uts]"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                aria-label="Nilai UTS untuk {{ $peserta->mahasiswa->nama_lengkap }}"
                                                value="{{ old('nilai.'.$peserta->id.'.uts', $peserta->nilai_uts !== null ? (string) (float) $peserta->nilai_uts : '') }}"
                                                class="grade-input grade-uts w-28 min-h-[44px] rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm text-slate-900"
                                            >
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="number"
                                                name="nilai[{{ $peserta->id }}][uas]"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                aria-label="Nilai UAS untuk {{ $peserta->mahasiswa->nama_lengkap }}"
                                                value="{{ old('nilai.'.$peserta->id.'.uas', $peserta->nilai_uas !== null ? (string) (float) $peserta->nilai_uas : '') }}"
                                                class="grade-input grade-uas w-28 min-h-[44px] rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm text-slate-900"
                                            >
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900 grade-preview whitespace-nowrap">
                                            @if ($peserta->nilai_akhir !== null)
                                                {{ $peserta->nilai_akhir }} ({{ $peserta->nilai_huruf }})
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3 bg-gray-50">
                        <button
                            type="button"
                            id="btn-back-to-peserta"
                            class="min-h-[44px] rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                        >
                            Lihat Daftar Nilai
                        </button>
                        <button
                            type="submit"
                            class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                        >
                            Simpan &amp; Hitung Nilai
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        (function () {
            const btnPeserta = document.getElementById('btn-tab-peserta');
            const btnForm = document.getElementById('btn-tab-form-nilai');
            const btnSwitchToForm = document.getElementById('btn-switch-to-form');
            const btnBackToPeserta = document.getElementById('btn-back-to-peserta');

            function updateTabButtons(showingForm) {
                if (!btnPeserta || !btnForm) {
                    return;
                }

                btnPeserta.setAttribute('aria-selected', showingForm ? 'false' : 'true');
                btnForm.setAttribute('aria-selected', showingForm ? 'true' : 'false');

                if (showingForm) {
                    btnForm.classList.add('border-academic-navy', 'text-academic-navy');
                    btnForm.classList.remove('border-transparent', 'text-slate-600');
                    btnPeserta.classList.remove('border-academic-navy', 'text-academic-navy');
                    btnPeserta.classList.add('border-transparent', 'text-slate-600');
                } else {
                    btnPeserta.classList.add('border-academic-navy', 'text-academic-navy');
                    btnPeserta.classList.remove('border-transparent', 'text-slate-600');
                    btnForm.classList.remove('border-academic-navy', 'text-academic-navy');
                    btnForm.classList.add('border-transparent', 'text-slate-600');
                }
            }

            function toggleTo(target) {
                const tabPeserta = document.getElementById('tab-peserta');
                const tabForm = document.getElementById('tab-form-nilai');

                if (!tabPeserta || !tabForm) {
                    return;
                }

                const isFormHidden = tabForm.classList.contains('hidden');

                if (target === 'form' && isFormHidden) {
                    document.getElementById('tab-peserta').classList.toggle('hidden');
                    document.getElementById('tab-form-nilai').classList.toggle('hidden');
                    updateTabButtons(true);
                } else if (target === 'peserta' && !isFormHidden) {
                    document.getElementById('tab-peserta').classList.toggle('hidden');
                    document.getElementById('tab-form-nilai').classList.toggle('hidden');
                    updateTabButtons(false);
                }
            }

            if (btnPeserta) {
                btnPeserta.addEventListener('click', function () {
                    toggleTo('peserta');
                });
            }

            if (btnForm) {
                btnForm.addEventListener('click', function () {
                    toggleTo('form');
                });
            }

            if (btnSwitchToForm) {
                btnSwitchToForm.addEventListener('click', function () {
                    toggleTo('form');
                });
            }

            if (btnBackToPeserta) {
                btnBackToPeserta.addEventListener('click', function () {
                    toggleTo('peserta');
                });
            }

            function toLetterGrade(na) {
                if (na >= 85) return 'A';
                if (na >= 75) return 'B';
                if (na >= 60) return 'C';
                if (na >= 50) return 'D';
                return 'E';
            }

            document.querySelectorAll('.grade-row').forEach(function (row) {
                const tugasEl = row.querySelector('.grade-tugas');
                const utsEl = row.querySelector('.grade-uts');
                const uasEl = row.querySelector('.grade-uas');
                const previewEl = row.querySelector('.grade-preview');

                if (!tugasEl || !utsEl || !uasEl || !previewEl) {
                    return;
                }

                function refreshPreview() {
                    if (tugasEl.value === '' || utsEl.value === '' || uasEl.value === '') {
                        previewEl.textContent = '-';
                        return;
                    }

                    const tugas = parseFloat(tugasEl.value);
                    const uts = parseFloat(utsEl.value);
                    const uas = parseFloat(uasEl.value);

                    if (Number.isNaN(tugas) || Number.isNaN(uts) || Number.isNaN(uas)) {
                        previewEl.textContent = '-';
                        return;
                    }

                    const na = Math.round(((tugas * 0.20) + (uts * 0.30) + (uas * 0.50)) * 100) / 100;
                    previewEl.textContent = na.toFixed(2) + ' (' + toLetterGrade(na) + ')';
                }

                [tugasEl, utsEl, uasEl].forEach(function (input) {
                    input.addEventListener('input', refreshPreview);
                });
            });
        })();
    </script>
</x-layouts.app>

