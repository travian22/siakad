<x-layouts.app title="Peserta Kelas Perkuliahan">
    <x-page-header
        :title="'Peserta Kelas: '.$kelas->mataKuliah->nama_mk.' ('.$kelas->nama_kelas.')'"
        :subtitle="$kelas->periodeAkademik->nama_periode.' | Dosen: '.$kelas->dosen->nama_lengkap.' | '.$kelas->hari.', '.substr($kelas->jam_mulai, 0, 5).'-'.substr($kelas->jam_selesai, 0, 5).' ('.$kelas->ruangan.')'"
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.kelas.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Kelas
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <section class="rounded-lg border border-slate-300 bg-white p-5 sm:p-6 lg:col-span-5">
            <h2 class="text-base font-semibold text-slate-900">Daftarkan Mahasiswa ke Kelas</h2>
            <p class="mt-1 text-sm text-slate-600">Pilih mahasiswa yang mengikuti kelas ini agar tampil di jadwal kuliah dan daftar penilaian dosen.</p>

            @if ($availableMahasiswas->isEmpty())
                <div class="mt-5 rounded-md border border-dashed border-slate-400 bg-gray-50 p-5 text-center">
                    <p class="text-sm text-slate-700">Semua mahasiswa yang terdaftar sudah menjadi peserta kelas ini.</p>
                </div>
            @else
                <form method="POST" action="{{ route('admin.kelas.peserta.store', $kelas) }}" class="mt-5 flex flex-col gap-4">
                    @csrf

                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-700">
                            {{ $availableMahasiswas->count() }} Mahasiswa Tersedia
                        </span>
                        <button
                            type="button"
                            id="toggle-all-mhs"
                            class="min-h-[44px] text-sm font-medium text-academic-navy underline underline-offset-4 hover:text-slate-900"
                        >
                            Pilih Semua
                        </button>
                    </div>

                    <div class="max-h-72 overflow-y-auto divide-y divide-slate-200 rounded-md border border-slate-300">
                        @foreach ($availableMahasiswas as $mhs)
                            <label class="flex items-center gap-3 px-3.5 py-2.5 min-h-[44px] hover:bg-gray-50">
                                <input
                                    type="checkbox"
                                    name="mahasiswa_ids[]"
                                    value="{{ $mhs->id }}"
                                    class="peserta-checkbox h-4 w-4 rounded border-slate-500 text-academic-navy focus:ring-academic-navy"
                                >
                                <span class="text-sm text-slate-900">
                                    <span class="font-semibold">{{ $mhs->npm }}</span> - {{ $mhs->nama_lengkap }}
                                    <span class="block text-xs text-slate-600">{{ $mhs->programStudi->nama_prodi }} ({{ $mhs->angkatan }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <button
                        type="submit"
                        class="min-h-[44px] rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                    >
                        Tambahkan Peserta Terpilih
                    </button>
                </form>
            @endif
        </section>

        <section class="rounded-lg border border-slate-300 bg-white lg:col-span-7">
            <div class="border-b border-slate-200 p-5 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Daftar Peserta Terdaftar</h2>
                    <p class="text-sm text-slate-600">Total peserta: {{ $enrolledPeserta->count() }} mahasiswa.</p>
                </div>
            </div>

            @if ($enrolledPeserta->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-700">Belum ada mahasiswa yang didaftarkan ke kelas ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">NPM</th>
                                <th scope="col" class="px-4 py-3">Nama Mahasiswa</th>
                                <th scope="col" class="px-4 py-3">Program Studi</th>
                                <th scope="col" class="px-4 py-3">Nilai</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($enrolledPeserta as $peserta)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $peserta->mahasiswa->npm }}</td>
                                    <td class="px-4 py-3 text-slate-800">{{ $peserta->mahasiswa->nama_lengkap }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $peserta->mahasiswa->programStudi->nama_prodi }}</td>
                                    <td class="px-4 py-3 text-slate-700">
                                        @if ($peserta->nilai_akhir !== null)
                                            {{ $peserta->nilai_akhir }} ({{ $peserta->nilai_huruf }})
                                        @else
                                            Belum dinilai
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.kelas.peserta.destroy', [$kelas, $peserta]) }}" onsubmit="return confirm('Keluarkan mahasiswa ini dari kelas?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="min-h-[44px] inline-flex items-center rounded-md border border-red-700 bg-white px-3 py-1.5 text-sm font-medium text-red-800 hover:bg-red-50"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <script>
        (function () {
            const toggleAllButton = document.getElementById('toggle-all-mhs');
            if (!toggleAllButton) {
                return;
            }

            toggleAllButton.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('.peserta-checkbox');
                const allChecked = Array.from(checkboxes).every(function (cb) {
                    return cb.checked;
                });

                checkboxes.forEach(function (cb) {
                    cb.checked = !allChecked;
                });

                toggleAllButton.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
            });
        })();
    </script>
</x-layouts.app>

