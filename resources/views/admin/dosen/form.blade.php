<x-layouts.app :title="$isEdit ? 'Ubah Data Dosen' : 'Tambah Data Dosen'">
    <x-page-header
        :title="$isEdit ? 'Ubah Data Dosen' : 'Tambah Data Dosen'"
        subtitle="Lengkapi identitas dosen dan kredensial login untuk akses portal dosen."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.master-dosen.index') }}"
                class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
            >
                Kembali ke Daftar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-3xl rounded-lg border border-slate-300 bg-white p-6">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.master-dosen.update', $dosen) : route('admin.master-dosen.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="nip" class="block text-sm font-medium text-slate-700">NIP</label>
                    <input
                        id="nip"
                        name="nip"
                        type="text"
                        required
                        maxlength="30"
                        value="{{ old('nip', $dosen->nip) }}"
                        placeholder="Contoh: 198501012010011001"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('nip')
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
                        value="{{ old('nama_lengkap', $dosen->nama_lengkap) }}"
                        placeholder="Contoh: Dr. Ahmad Fauzi"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('nama_lengkap')
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
                        value="{{ old('username', $dosen->user?->username) }}"
                        placeholder="Contoh: dosen2"
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
                        <option value="L" @selected(old('jenis_kelamin', $dosen->jenis_kelamin) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin', $dosen->jenis_kelamin) === 'P')>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-slate-700">Nomor Telepon (Opsional)</label>
                    <input
                        id="telepon"
                        name="telepon"
                        type="text"
                        maxlength="25"
                        value="{{ old('telepon', $dosen->telepon) }}"
                        placeholder="Contoh: 081234567890"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >
                    @error('telepon')
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
                    placeholder="Masukkan alamat tempat tinggal dosen"
                    class="mt-1.5 block w-full rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                >{{ old('alamat', $dosen->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button
                    type="submit"
                    class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data Dosen' }}
                </button>
                <a
                    href="{{ route('admin.master-dosen.index') }}"
                    class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>

