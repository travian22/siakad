<x-layouts.app title="Master Data Mahasiswa">
    <x-page-header
        title="Master Data Mahasiswa"
        subtitle="Kelola data mahasiswa, program studi, angkatan, dan akun login SIAKAD."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-mahasiswa.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Tambah Mahasiswa
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.master-mahasiswa.index') }}" class="border-b border-slate-200 p-4 grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-5">
                <label for="search-mahasiswa" class="block text-sm font-medium text-slate-700">Cari Mahasiswa</label>
                <input
                    id="search-mahasiswa"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan NPM, nama lengkap, atau username"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
            </div>
            <div class="md:col-span-4">
                <label for="filter-prodi" class="block text-sm font-medium text-slate-700">Filter Program Studi</label>
                <select
                    id="filter-prodi"
                    name="program_studi_id"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Semua Program Studi</option>
                    @foreach ($programStudis as $prodi)
                        <option value="{{ $prodi->id }}" @selected($prodiId === $prodi->id)>
                            {{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}
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
                @if ($search !== '' || $prodiId > 0)
                    <a
                        href="{{ route('admin.master-mahasiswa.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($mahasiswas->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ ($search !== '' || $prodiId > 0) ? 'Tidak ada mahasiswa yang cocok dengan filter Anda.' : 'Belum ada data mahasiswa terdaftar.' }}
                </p>
                <a
                    href="{{ ($search !== '' || $prodiId > 0) ? route('admin.master-mahasiswa.index') : route('admin.master-mahasiswa.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ ($search !== '' || $prodiId > 0) ? 'Tampilkan Semua' : 'Tambah Mahasiswa' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">NPM</th>
                            <th scope="col" class="px-4 py-3">Nama Lengkap</th>
                            <th scope="col" class="px-4 py-3">Program Studi</th>
                            <th scope="col" class="px-4 py-3">Angkatan</th>
                            <th scope="col" class="px-4 py-3">Username</th>
                            <th scope="col" class="px-4 py-3">Status Profil</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($mahasiswas as $mhs)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $mhs->npm }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $mhs->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $mhs->programStudi->nama_prodi }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $mhs->angkatan }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $mhs->user->username }}</td>
                                <td class="px-4 py-3">
                                    @if ($mhs->isProfileComplete())
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Lengkap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                            Belum Lengkap
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.master-mahasiswa.edit', $mhs) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.master-mahasiswa.destroy', $mhs) }}" onsubmit="return confirm('Hapus data mahasiswa beserta akun loginnya?');">
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

            @if ($mahasiswas->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $mahasiswas->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

