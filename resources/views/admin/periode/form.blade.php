<x-layouts.app :title="$isEdit ? 'Ubah Periode Akademik' : 'Tambah Periode Akademik'">
    <x-page-header
        :title="$isEdit ? 'Ubah Periode Akademik' : 'Tambah Periode Akademik'"
        subtitle="Tentukan nama semester dan tahun ajaran akademik."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.periode-akademik.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.periode-akademik.update', $periode) : route('admin.periode-akademik.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="nama_periode" class="block text-sm font-medium text-slate-700">Nama Periode Akademik</label>
                <input
                    id="nama_periode"
                    name="nama_periode"
                    type="text"
                    required
                    maxlength="100"
                    value="{{ old('nama_periode', $periode->nama_periode) }}"
                    placeholder="Contoh: 2024/2025 Genap"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
                @error('nama_periode')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            @unless ($isEdit)
                <div>
                    <label for="is_active" class="inline-flex items-center gap-2.5 min-h-[44px] text-sm text-slate-800">
                        <input
                            id="is_active"
                            name="is_active"
                            type="checkbox"
                            value="1"
                            @checked(old('is_active'))
                            class="h-4 w-4 rounded border-slate-500 text-academic-navy focus:ring-academic-navy"
                        >
                        <span>Langsung jadikan sebagai periode aktif (menonaktifkan periode lainnya)</span>
                    </label>
                </div>
            @endunless

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Periode' }}
                </button>
                <a
                    href="{{ route('admin.periode-akademik.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

