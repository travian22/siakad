<x-layouts.app title="Kelas Perkuliahan">
    <x-page-header
        title="Kelas Perkuliahan"
        subtitle="Kelola penjadwalan mata kuliah, dosen pengampu, ruang, dan peserta kelas."
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

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.kelas.index') }}" class="border-b border-slate-200 p-4 grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-5">
                <label for="search-kelas" class="block text-sm font-medium text-slate-700">Cari Kelas</label>
                <input
                    id="search-kelas"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari mata kuliah, nama kelas, dosen, atau ruangan"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
            </div>
            <div class="md:col-span-4">
                <label for="filter-periode" class="block text-sm font-medium text-slate-700">Filter Periode Akademik</label>
                <select
                    id="filter-periode"
                    name="periode_akademik_id"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $periode)
                        <option value="{{ $periode->id }}" @selected($periodeId === $periode->id)>
                            {{ $periode->nama_periode }}{{ $periode->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex items-center gap-2">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Filter
                </button>
                @if ($search !== '' || $periodeId > 0)
                    <a
                        href="{{ route('admin.kelas.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($kelasList->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ ($search !== '' || $periodeId > 0) ? 'Tidak ada kelas yang cocok dengan filter Anda.' : 'Belum ada kelas perkuliahan yang dijadwalkan.' }}
                </p>
                <a
                    href="{{ ($search !== '' || $periodeId > 0) ? route('admin.kelas.index') : route('admin.kelas.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ ($search !== '' || $periodeId > 0) ? 'Tampilkan Semua' : 'Buat Kelas Perkuliahan' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Periode</th>
                            <th scope="col" class="px-4 py-3">Mata Kuliah</th>
                            <th scope="col" class="px-4 py-3">Kelas</th>
                            <th scope="col" class="px-4 py-3">Dosen Pengampu</th>
                            <th scope="col" class="px-4 py-3">Jadwal &amp; Ruang</th>
                            <th scope="col" class="px-4 py-3">Peserta</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($kelasList as $kelas)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">
                                    <span>{{ $kelas->periodeAkademik->nama_periode }}</span>
                                    @if ($kelas->periodeAkademik->is_active)
                                        <span class="ml-1 inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-900">
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $kelas->mataKuliah->kode_mk }} - {{ $kelas->mataKuliah->nama_mk }}
                                    <span class="block text-xs text-slate-600">{{ $kelas->mataKuliah->sks }} SKS</span>
                                </td>
                                <td class="px-4 py-3 text-slate-800">{{ $kelas->nama_kelas }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $kelas->dosen->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $kelas->hari }}, {{ substr($kelas->jam_mulai, 0, 5) }}-{{ substr($kelas->jam_selesai, 0, 5) }}
                                    <span class="block text-xs text-slate-600">Ruang {{ $kelas->ruangan }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('admin.kelas.peserta', $kelas) }}"
                                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-academic-navy hover:bg-slate-100"
                                    >
                                        Kelola ({{ $kelas->peserta_kelas_count }})
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.kelas.edit', $kelas) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.kelas.destroy', $kelas) }}" onsubmit="return confirm('Hapus jadwal kelas perkuliahan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="min-h-[44px] inline-flex items-center rounded-md border border-red-700 bg-white px-3 py-1.5 text-sm font-medium text-red-800 hover:bg-red-50"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($kelasList->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $kelasList->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

