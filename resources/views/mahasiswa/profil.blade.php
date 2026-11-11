<x-layouts.app :title="$isProfileComplete ? 'Profil Mahasiswa' : 'Lengkapi Data Diri'" :hide-sidebar="! $isProfileComplete">
    <div class="{{ $isProfileComplete ? 'max-w-3xl' : 'max-w-2xl mx-auto py-4' }}">
        <x-page-header
            :title="$isProfileComplete ? 'Profil Saya' : 'Lengkapi Data Diri'"
            :subtitle="$isProfileComplete
                ? 'Perbarui biodata dan foto profil mahasiswa Anda.'
                : 'Sebelum mengakses dashboard, jadwal kuliah, KHS, dan informasi biaya, lengkapi biodata diri Anda terlebih dahulu.'"
        >
            @if ($isProfileComplete)
                <x-slot:actions>
                    <a
                        href="{{ route('mahasiswa.dashboard') }}"
                        class="min-h-[44px] inline-flex items-center rounded-md border border-slate-500 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100"
                    >
                        Kembali ke Dashboard
                    </a>
                </x-slot:actions>
            @endif
        </x-page-header>

        <div class="rounded-lg border border-slate-300 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center">
                @if ($mahasiswa->foto_profil)
                    <img
                        src="{{ Storage::url($mahasiswa->foto_profil) }}"
                        alt="Foto profil {{ $mahasiswa->nama_lengkap }}"
                        width="80"
                        height="80"
                        class="h-20 w-20 shrink-0 rounded-lg border border-slate-300 object-cover"
                    >
                @else
                    <div
                        class="h-20 w-20 shrink-0 rounded-lg border border-slate-300 bg-academic-navy text-white flex items-center justify-center text-xl font-semibold"
                        aria-hidden="true"
                    >
                        {{ strtoupper(mb_substr($mahasiswa->nama_lengkap, 0, 2)) }}
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                        NPM: {{ $mahasiswa->npm }} | Angkatan {{ $mahasiswa->angkatan }}
                    </p>
                    <p class="mt-0.5 text-lg font-semibold text-slate-900 truncate">{{ $mahasiswa->nama_lengkap }}</p>
                    <p class="text-sm text-slate-600">
                        Program Studi: {{ $mahasiswa->programStudi->nama_prodi }}
                    </p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('mahasiswa.profil.update') }}"
                enctype="multipart/form-data"
                class="mt-6 flex flex-col gap-5"
            >
                @csrf

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <input
                            id="nama_lengkap"
                            name="nama_lengkap"
                            type="text"
                            required
                            maxlength="255"
                            value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap) }}"
                            class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                        >
                        @error('nama_lengkap')
                            <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            required
                            class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900"
                        >
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'P')>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-slate-700">Alamat Lengkap</label>
                    <textarea
                        id="alamat"
                        name="alamat"
                        rows="3"
                        required
                        placeholder="Masukkan alamat tempat tinggal saat ini"
                        class="mt-1.5 block w-full rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-500"
                    >{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="foto_profil" class="block text-sm font-medium text-slate-700">
                        {{ $mahasiswa->foto_profil ? 'Ganti Foto Profil (Opsional)' : 'Foto Profil (Opsional)' }}
                    </label>
                    <input
                        id="foto_profil"
                        name="foto_profil"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3 py-2 text-sm text-slate-900 file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-800 hover:file:bg-slate-200"
                    >
                    <p class="mt-1 text-xs text-slate-600">Format yang didukung: JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                    @error('foto_profil')
                        <p class="mt-1.5 text-sm text-red-800" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                    <button
                        type="submit"
                        class="min-h-[44px] rounded-md bg-academic-navy px-5 py-2 text-sm font-semibold text-white hover:bg-academic-navy-hover transition-colors"
                    >
                        Simpan Profil Mahasiswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

