<x-layouts.app :title="$isEdit ? 'Ubah Kelas Perkuliahan' : 'Buat Kelas Perkuliahan'">
    <x-page-header
        :title="$isEdit ? 'Ubah Kelas Perkuliahan' : 'Buat Kelas Perkuliahan'"
        subtitle="Pasangkan mata kuliah, dosen pengampu, periode akademik, hari, jam, dan ruangan."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.kelas.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-3xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.kelas.update', $kelas) : route('admin.kelas.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
                            <option value="{{ $periode->id }}" @selected((int) old('periode_akademik_id', $kelas->periode_akademik_id) === $periode->id)>
                                {{ $periode->nama_periode }}{{ $periode->is_active ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_akademik_id')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mata_kuliah_id" class="block text-sm font-medium text-slate-700">Mata Kuliah</label>
                    <select
                        id="mata_kuliah_id"
                        name="mata_kuliah_id"
                        required
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach ($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" @selected((int) old('mata_kuliah_id', $kelas->mata_kuliah_id) === $mk->id)>
                                {{ $mk->kode_mk }} - {{ $mk->nama_mk }} ({{ $mk->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="dosen_id" class="block text-sm font-medium text-slate-700">Dosen Pengampu</label>
                    <select
                        id="dosen_id"
                        name="dosen_id"
                        required
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="">Pilih Dosen Pengampu</option>
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->id }}" @selected((int) old('dosen_id', $kelas->dosen_id) === $dosen->id)>
                                {{ $dosen->nama_lengkap }} ({{ $dosen->nip }})
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_kelas" class="block text-sm font-medium text-slate-700">Nama Kelas</label>
                    <input
                        id="nama_kelas"
                        name="nama_kelas"
                        type="text"
                        required
                        maxlength="50"
                        value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                        placeholder="Contoh: Kelas A"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('nama_kelas')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div>
                    <label for="hari" class="block text-sm font-medium text-slate-700">Hari</label>
                    <select
                        id="hari"
                        name="hari"
                        required
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="">Pilih Hari</option>
                        @foreach (\App\Models\KelasPerkuliahan::DAYS as $hari)
                            <option value="{{ $hari }}" @selected(old('hari', $kelas->hari) === $hari)>
                                {{ $hari }}
                            </option>
                        @endforeach
                    </select>
                    @error('hari')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jam_mulai" class="block text-sm font-medium text-slate-700">Jam Mulai</label>
                    <input
                        id="jam_mulai"
                        name="jam_mulai"
                        type="time"
                        required
                        value="{{ old('jam_mulai', $kelas->jam_mulai ? substr($kelas->jam_mulai, 0, 5) : '08:00') }}"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                    @error('jam_mulai')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jam_selesai" class="block text-sm font-medium text-slate-700">Jam Selesai</label>
                    <input
                        id="jam_selesai"
                        name="jam_selesai"
                        type="time"
                        required
                        value="{{ old('jam_selesai', $kelas->jam_selesai ? substr($kelas->jam_selesai, 0, 5) : '10:00') }}"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                    @error('jam_selesai')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ruangan" class="block text-sm font-medium text-slate-700">Ruangan</label>
                    <input
                        id="ruangan"
                        name="ruangan"
                        type="text"
                        required
                        maxlength="50"
                        value="{{ old('ruangan', $kelas->ruangan) }}"
                        placeholder="Contoh: R-301"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('ruangan')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kelas Perkuliahan' }}
                </button>
                <a
                    href="{{ route('admin.kelas.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

