<x-layouts.app title="Keuangan & Tagihan Mahasiswa">
    <x-page-header
        title="Keuangan &amp; Tagihan Kuliah"
        subtitle="Tetapkan nominal tagihan kuliah per mahasiswa pada setiap periode akademik."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.keuangan.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Buat Tagihan Baru
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        <form method="GET" action="{{ route('admin.keuangan.index') }}" class="border-b border-slate-200 p-4 grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-4">
                <label for="search-keuangan" class="block text-sm font-medium text-slate-700">Cari Mahasiswa</label>
                <input
                    id="search-keuangan"
                    name="q"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan NPM atau nama mahasiswa"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
            </div>
            <div class="md:col-span-3">
                <label for="filter-periode-keuangan" class="block text-sm font-medium text-slate-700">Periode</label>
                <select
                    id="filter-periode-keuangan"
                    name="periode_akademik_id"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $periode)
                        <option value="{{ $periode->id }}" @selected($periodeId === $periode->id)>
                            {{ $periode->nama_periode }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="filter-status-bayar" class="block text-sm font-medium text-slate-700">Status</label>
                <select
                    id="filter-status-bayar"
                    name="status_bayar"
                    class="mt-1 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" @selected($statusBayar === 'belum_bayar')>Belum Bayar</option>
                    <option value="lunas" @selected($statusBayar === 'lunas')>Lunas</option>
                </select>
            </div>
            <div class="md:col-span-3 flex items-center gap-2">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Filter
                </button>
                @if ($search !== '' || $periodeId > 0 || $statusBayar !== '')
                    <a
                        href="{{ route('admin.keuangan.index') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($tagihans->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">
                    {{ ($search !== '' || $periodeId > 0 || $statusBayar !== '') ? 'Tidak ada tagihan yang cocok dengan filter Anda.' : 'Belum ada data tagihan kuliah.' }}
                </p>
                <a
                    href="{{ ($search !== '' || $periodeId > 0 || $statusBayar !== '') ? route('admin.keuangan.index') : route('admin.keuangan.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    {{ ($search !== '' || $periodeId > 0 || $statusBayar !== '') ? 'Tampilkan Semua' : 'Buat Tagihan Baru' }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Mahasiswa</th>
                            <th scope="col" class="px-4 py-3">Program Studi</th>
                            <th scope="col" class="px-4 py-3">Periode</th>
                            <th scope="col" class="px-4 py-3">Nominal Tagihan</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($tagihans as $biaya)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-900">{{ $biaya->mahasiswa->npm }}</span>
                                    <span class="block text-slate-800">{{ $biaya->mahasiswa->nama_lengkap }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $biaya->mahasiswa->programStudi->nama_prodi }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $biaya->periodeAkademik->nama_periode }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    Rp {{ number_format((float) $biaya->nominal_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($biaya->status_bayar === 'lunas')
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.keuangan.toggle-status', $biaya) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-academic-navy hover:bg-slate-100"
                                            >
                                                {{ $biaya->status_bayar === 'lunas' ? 'Tandai Belum Bayar' : 'Tandai Lunas' }}
                                            </button>
                                        </form>
                                        <a
                                            href="{{ route('admin.keuangan.edit', $biaya) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.keuangan.destroy', $biaya) }}" onsubmit="return confirm('Hapus tagihan kuliah ini?');">
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

            @if ($tagihans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $tagihans->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

