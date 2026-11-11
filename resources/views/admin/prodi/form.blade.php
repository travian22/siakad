<x-layouts.app :title="$isEdit ? 'Ubah Program Studi' : 'Tambah Program Studi'">
    <x-page-header
        :title="$isEdit ? 'Ubah Program Studi' : 'Tambah Program Studi'"
        subtitle="Isi kode unik dan nama resmi program studi."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-prodi.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.master-prodi.update', $prodi) : route('admin.master-prodi.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="kode_prodi" class="block text-sm font-medium text-slate-700">Kode Program Studi</label>
                <input
                    id="kode_prodi"
                    name="kode_prodi"
                    type="text"
                    required
                    maxlength="20"
                    value="{{ old('kode_prodi', $prodi->kode_prodi) }}"
                    placeholder="Contoh: 01 atau TI"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
                @error('kode_prodi')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama_prodi" class="block text-sm font-medium text-slate-700">Nama Program Studi</label>
                <input
                    id="nama_prodi"
                    name="nama_prodi"
                    type="text"
                    required
                    maxlength="255"
                    value="{{ old('nama_prodi', $prodi->nama_prodi) }}"
                    placeholder="Contoh: Teknik Informatika"
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >
                @error('nama_prodi')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Program Studi' }}
                </button>
                <a
                    href="{{ route('admin.master-prodi.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

