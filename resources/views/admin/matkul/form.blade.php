<x-layouts.app :title="$isEdit ? 'Ubah Mata Kuliah' : 'Tambah Mata Kuliah'">
    <x-page-header
        :title="$isEdit ? 'Ubah Mata Kuliah' : 'Tambah Mata Kuliah'"
        subtitle="Tentukan kode mata kuliah, nama mata kuliah, dan bobot SKS."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-matkul.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.master-matkul.update', $matkul) : route('admin.master-matkul.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="kode_mk" class="block text-sm font-medium text-slate-700">Kode Mata Kuliah</label>
                <input
                    id="kode_mk"
                    name="kode_mk"
                    type="text"
                    required
                    maxlength="20"
                    value="{{ old('kode_mk', $matkul->kode_mk) }}"
                    placeholder="Contoh: IF101"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
                @error('kode_mk')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama_mk" class="block text-sm font-medium text-slate-700">Nama Mata Kuliah</label>
                <input
                    id="nama_mk"
                    name="nama_mk"
                    type="text"
                    required
                    maxlength="255"
                    value="{{ old('nama_mk', $matkul->nama_mk) }}"
                    placeholder="Contoh: Pemrograman Web Lanjut"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
                @error('nama_mk')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sks" class="block text-sm font-medium text-slate-700">Jumlah SKS</label>
                <input
                    id="sks"
                    name="sks"
                    type="number"
                    required
                    min="1"
                    max="6"
                    value="{{ old('sks', $matkul->sks ?: 3) }}"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                @error('sks')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Mata Kuliah' }}
                </button>
                <a
                    href="{{ route('admin.master-matkul.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

