<x-layouts.app title="Master Data Dosen">
    <x-page-header
        title="Master Data Dosen"
        subtitle="Kelola data induk dosen sekaligus akun login SIAKAD."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-dosen.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Tambah Dosen
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.master-dosen.index') }}" class="border-b border-slate-200 p-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1 min-w-0">
                <label for="search-dosen" class="block text-sm font-medium text-slate-700">Cari Dosen</label>
                <input
                    id="search-dosen"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan NIP, nama lengkap, atau username"
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
                        href="{{ route('admin.master-dosen.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($dosens->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ $search !== '' ? 'Tidak ada data dosen yang cocok dengan pencarian Anda.' : 'Belum ada data dosen terdaftar.' }}
                </p>
                <a
                    href="{{ $search !== '' ? route('admin.master-dosen.index') : route('admin.master-dosen.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ $search !== '' ? 'Tampilkan Semua' : 'Tambah Dosen' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">NIP</th>
                            <th scope="col" class="px-4 py-3">Nama Lengkap</th>
                            <th scope="col" class="px-4 py-3">Username</th>
                            <th scope="col" class="px-4 py-3">Status Profil</th>
                            <th scope="col" class="px-4 py-3">Kelas Diampu</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($dosens as $dosen)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $dosen->nip }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $dosen->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $dosen->user->username }}</td>
                                <td class="px-4 py-3">
                                    @if ($dosen->isProfileComplete())
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Lengkap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                            Belum Lengkap
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $dosen->kelas_perkuliahan_count }} kelas</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.master-dosen.edit', $dosen) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.master-dosen.destroy', $dosen) }}" onsubmit="return confirm('Hapus data dosen beserta akun loginnya?');">
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

            @if ($dosens->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $dosens->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

