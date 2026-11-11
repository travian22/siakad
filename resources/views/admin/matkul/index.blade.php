<x-layouts.app title="Master Mata Kuliah">
    <x-page-header
        title="Master Mata Kuliah"
        subtitle="Kelola katalog mata kuliah dan bobot SKS."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-matkul.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Tambah Mata Kuliah
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.master-matkul.index') }}" class="border-b border-slate-200 p-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1 min-w-0">
                <label for="search-matkul" class="block text-sm font-medium text-slate-700">Cari Mata Kuliah</label>
                <input
                    id="search-matkul"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan kode atau nama mata kuliah"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Cari
                </button>
                @if ($search !== '')
                    <a
                        href="{{ route('admin.master-matkul.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($mataKuliahs->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ $search !== '' ? 'Tidak ada mata kuliah yang cocok dengan pencarian Anda.' : 'Belum ada data mata kuliah.' }}
                </p>
                <a
                    href="{{ $search !== '' ? route('admin.master-matkul.index') : route('admin.master-matkul.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ $search !== '' ? 'Tampilkan Semua' : 'Tambah Mata Kuliah' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Kode MK</th>
                            <th scope="col" class="px-4 py-3">Nama Mata Kuliah</th>
                            <th scope="col" class="px-4 py-3">SKS</th>
                            <th scope="col" class="px-4 py-3">Jumlah Kelas</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($mataKuliahs as $matkul)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $matkul->kode_mk }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $matkul->nama_mk }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $matkul->sks }} SKS</td>
                                <td class="px-4 py-3 text-slate-700">{{ $matkul->kelas_perkuliahan_count }} kelas</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.master-matkul.edit', $matkul) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.master-matkul.destroy', $matkul) }}" onsubmit="return confirm('Hapus mata kuliah ini?');">
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

            @if ($mataKuliahs->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $mataKuliahs->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

