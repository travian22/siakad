<x-layouts.app title="Periode Akademik">
    <x-page-header
        title="Periode Akademik"
        subtitle="Kelola semester akademik. Hanya satu periode yang dapat berstatus aktif pada satu waktu."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.periode-akademik.create') }}"
                class="min-h-[44px] inline-flex items-center justify-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
            >
                Tambah Periode
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-lg border border-slate-300 bg-white">
        @if ($periodes->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Belum ada periode akademik yang dibuat.</p>
                <a
                    href="{{ route('admin.periode-akademik.create') }}"
                    class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-academic-navy px-4 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                >
                    Tambah Periode
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Nama Periode</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Kelas</th>
                            <th scope="col" class="px-4 py-3">Tagihan</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($periodes as $periode)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $periode->nama_periode }}</td>
                                <td class="px-4 py-3">
                                    @if ($periode->is_active)
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-slate-400 bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-800">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $periode->kelas_perkuliahan_count }} kelas</td>
                                <td class="px-4 py-3 text-slate-700">{{ $periode->biaya_kuliah_count }} tagihan</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @unless ($periode->is_active)
                                            <form method="POST" action="{{ route('admin.periode-akademik.activate', $periode) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="min-h-[44px] inline-flex items-center rounded-md bg-academic-navy px-3 py-1.5 text-sm font-semibold text-white hover:bg-academic-navy-hover"
                                                >
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endunless

                                        <a
                                            href="{{ route('admin.periode-akademik.edit', $periode) }}"
                                            class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-3 py-1.5 text-sm font-medium text-slate-800 hover:bg-slate-100"
                                        >
                                            Ubah
                                        </a>

                                        @unless ($periode->is_active)
                                            <form method="POST" action="{{ route('admin.periode-akademik.destroy', $periode) }}" onsubmit="return confirm('Hapus periode akademik ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="min-h-[44px] inline-flex items-center rounded-md border border-red-700 bg-white px-3 py-1.5 text-sm font-medium text-red-800 hover:bg-red-50"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($periodes->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $periodes->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>

