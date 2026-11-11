<x-layouts.app :title="$isEdit ? 'Ubah Tagihan Kuliah' : 'Buat Tagihan Kuliah'">
    <x-page-header
        :title="$isEdit ? 'Ubah Tagihan Kuliah' : 'Buat Tagihan Kuliah'"
        subtitle="Tetapkan mahasiswa, periode akademik, nominal tagihan, dan status pembayaran."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.keuangan.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.keuangan.update', $biaya) : route('admin.keuangan.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="mahasiswa_id" class="block text-sm font-medium text-slate-700">Mahasiswa</label>
                <select
                    id="mahasiswa_id"
                    name="mahasiswa_id"
                    required
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Pilih Mahasiswa</option>
                    @foreach ($mahasiswas as $mhs)
                        <option value="{{ $mhs->id }}" @selected((int) old('mahasiswa_id', $biaya->mahasiswa_id) === $mhs->id)>
                            {{ $mhs->npm }} - {{ $mhs->nama_lengkap }} ({{ $mhs->programStudi->nama_prodi }})
                        </option>
                    @endforeach
                </select>
                @error('mahasiswa_id')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="periode_akademik_id" class="block text-sm font-medium text-slate-700">Periode Akademik</label>
                <select
                    id="periode_akademik_id"
                    name="periode_akademik_id"
                    required
                    class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                >
                    <option value="">Pilih Periode Akademik</option>
                    @foreach ($periodes as $periode)
                        <option value="{{ $periode->id }}" @selected((int) old('periode_akademik_id', $biaya->periode_akademik_id) === $periode->id)>
                            {{ $periode->nama_periode }}{{ $periode->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('periode_akademik_id')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="nominal_tagihan" class="block text-sm font-medium text-slate-700">Nominal Tagihan (Rp)</label>
                    <input
                        id="nominal_tagihan"
                        name="nominal_tagihan"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        value="{{ old('nominal_tagihan', $biaya->nominal_tagihan !== null ? (string) (float) $biaya->nominal_tagihan : '') }}"
                        placeholder="Contoh: 4500000"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('nominal_tagihan')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_bayar" class="block text-sm font-medium text-slate-700">Status Pembayaran</label>
                    <select
                        id="status_bayar"
                        name="status_bayar"
                        required
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="belum_bayar" @selected(old('status_bayar', $biaya->status_bayar) === 'belum_bayar')>Belum Bayar</option>
                        <option value="lunas" @selected(old('status_bayar', $biaya->status_bayar) === 'lunas')>Lunas</option>
                    </select>
                    @error('status_bayar')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Tagihan' }}
                </button>
                <a
                    href="{{ route('admin.keuangan.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

