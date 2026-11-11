<x-layouts.app title="Master Program Studi">
    <x-page-header
        title="Master Program Studi"
        subtitle="Kelola daftar kode dan nama program studi perguruan tinggi."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-prodi.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Tambah Program Studi
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.master-prodi.index') }}" class="border-b border-slate-200 p-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1 min-w-0">
                <label for="search-prodi" class="block text-sm font-medium text-slate-700">Cari Program Studi</label>
                <input
                    id="search-prodi"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan kode atau nama prodi"
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
                        href="{{ route('admin.master-prodi.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($programStudis->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ $search !== '' ? 'Tidak ada program studi yang cocok dengan pencarian Anda.' : 'Belum ada data program studi.' }}
                </p>
                <a
                    href="{{ $search !== '' ? route('admin.master-prodi.index') : route('admin.master-prodi.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ $search !== '' ? 'Tampilkan Semua' : 'Tambah Program Studi' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Kode Prodi</th>
                            <th scope="col" class="px-4 py-3">Nama Program Studi</th>
                            <th scope="col" class="px-4 py-3">Jumlah Mahasiswa</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($programStudis as $prodi)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $prodi->kode_prodi }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $prodi->nama_prodi }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $prodi->mahasiswa_count }} mahasiswa</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.master-prodi.edit', $prodi) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.master-prodi.destroy', $prodi) }}" onsubmit="return confirm('Hapus program studi ini?');">
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

            @if ($programStudis->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $programStudis->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

