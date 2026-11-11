<x-layouts.app :title="$isEdit ? 'Ubah Data Mahasiswa' : 'Tambah Data Mahasiswa'">
    <x-page-header
        :title="$isEdit ? 'Ubah Data Mahasiswa' : 'Tambah Data Mahasiswa'"
        subtitle="Lengkapi biodata akademik mahasiswa dan kredensial login portal mahasiswa."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-mahasiswa.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-3xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.master-mahasiswa.update', $mahasiswa) : route('admin.master-mahasiswa.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="npm" class="block text-sm font-medium text-slate-700">NPM</label>
                    <input
                        id="npm"
                        name="npm"
                        type="text"
                        required
                        maxlength="30"
                        value="{{ old('npm', $mahasiswa->npm) }}"
                        placeholder="Contoh: 2024010002"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('npm')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input
                        id="nama_lengkap"
                        name="nama_lengkap"
                        type="text"
                        required
                        maxlength="255"
                        value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap) }}"
                        placeholder="Masukkan nama lengkap mahasiswa"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('nama_lengkap')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="program_studi_id" class="block text-sm font-medium text-slate-700">Program Studi</label>
                    <select
                        id="program_studi_id"
                        name="program_studi_id"
                        required
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="">Pilih Program Studi</option>
                        @foreach ($programStudis as $prodi)
                            <option value="{{ $prodi->id }}" @selected((int) old('program_studi_id', $mahasiswa->program_studi_id) === $prodi->id)>
                                {{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_studi_id')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="angkatan" class="block text-sm font-medium text-slate-700">Angkatan</label>
                    <input
                        id="angkatan"
                        name="angkatan"
                        type="text"
                        required
                        maxlength="4"
                        value="{{ old('angkatan', $mahasiswa->angkatan ?: date('Y')) }}"
                        placeholder="Contoh: 2024"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('angkatan')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700">Username Login</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        required
                        maxlength="50"
                        value="{{ old('username', $mahasiswa->user?->username) }}"
                        placeholder="Contoh: mhs2"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('username')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">
                        {{ $isEdit ? 'Kata Sandi Baru (Opsional)' : 'Kata Sandi' }}
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        @required(! $isEdit)
                        placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-slate-700">Jenis Kelamin (Opsional)</label>
                    <select
                        id="jenis_kelamin"
                        name="jenis_kelamin"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                    >
                        <option value="">Belum ditentukan</option>
                        <option value="L" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'P')>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-slate-700">Alamat (Opsional)</label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    placeholder="Masukkan alamat tempat tinggal mahasiswa"
                    class="mt-1.5 block w-full rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data Mahasiswa' }}
                </button>
                <a
                    href="{{ route('admin.master-mahasiswa.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

