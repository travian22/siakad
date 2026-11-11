<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIAKAD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-slate-900 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-md focus:bg-academic-navy focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Langsung ke formulir masuk
    </a>

    <main id="main-content" class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm grid grid-cols-1 lg:grid-cols-12">
            <section class="bg-academic-navy text-white p-6 sm:p-8 lg:col-span-5 lg:p-10 flex flex-col justify-between gap-6">
                <div>
                    <p class="text-sm font-semibold tracking-wide text-slate-200 uppercase">Portal Akademik</p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-semibold tracking-tight text-white">
                        Sistem Informasi Akademik
                    </h1>
                    <div class="mt-4 h-0.5 w-14 bg-amber-500" aria-hidden="true"></div>
                    <p class="mt-4 text-sm leading-relaxed text-slate-200">
                        Akses terpusat untuk pengelolaan data akademik, penjadwalan kelas, penilaian mahasiswa, dan pemantauan hasil studi.
                    </p>
                </div>

                <div class="border-t border-white/20 pt-5">
                    <h2 class="text-sm font-semibold text-white">Akses Pengguna</h2>
                    <ul class="mt-2.5 flex flex-col gap-2 text-sm text-slate-200">
                        <li>Admin: pengelolaan data master, periode aktif, jadwal kelas, dan tagihan.</li>
                        <li>Dosen: jadwal mengajar dan pengisian nilai mahasiswa.</li>
                        <li>Mahasiswa: jadwal kuliah, KHS, dan status biaya kuliah.</li>
                    </ul>
                </div>
            </section>

            <section class="p-6 sm:p-8 lg:col-span-7 lg:p-10 flex flex-col justify-center">
                <div class="w-full max-w-md mx-auto">
                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-600">
                        Gunakan username dan kata sandi yang terdaftar di SIAKAD.
                    </p>

                    @if (session('status'))
                        <div class="mt-5 rounded-md border border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mt-5 rounded-md border border-amber-700 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-md border border-red-700 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
                            <p class="font-semibold">Gagal masuk ke sistem:</p>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="login-form" method="POST" action="{{ route('login.store') }}" class="mt-6 flex flex-col gap-5" novalidate>
                        @csrf

                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700">
                                Username
                            </label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                autocomplete="username"
                                @if ($errors->has('username'))
                                    aria-invalid="true"
                                    aria-describedby="username-error"
                                @endif
                                placeholder="Contoh: admin, dosen1, atau mhs1"
                                class="mt-1.5 block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-academic-navy focus:ring-2 focus:ring-academic-navy/25"
                            >
                            @error('username')
                                <p id="username-error" class="mt-1.5 text-sm text-red-800" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">
                                Kata Sandi
                            </label>
                            <div class="mt-1.5 flex items-stretch gap-2">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    @if ($errors->has('password'))
                                        aria-invalid="true"
                                        aria-describedby="password-error"
                                    @endif
                                    placeholder="Masukkan kata sandi"
                                    class="block w-full min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-academic-navy focus:ring-2 focus:ring-academic-navy/25"
                                >
                                <button
                                    type="button"
                                    id="toggle-password"
                                    aria-label="Tampilkan kata sandi"
                                    aria-pressed="false"
                                    class="min-h-[44px] shrink-0 rounded-md border border-slate-500 bg-white px-3.5 text-sm font-medium text-slate-700 hover:bg-slate-100 active:bg-slate-200 transition-colors"
                                >
                                    Tampilkan
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="mt-1.5 text-sm text-red-800" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember" class="inline-flex items-center gap-2.5 min-h-[44px] text-sm text-slate-700">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    value="1"
                                    @checked(old('remember'))
                                    class="h-4 w-4 rounded border-slate-500 text-academic-navy focus:ring-academic-navy"
                                >
                                <span>Ingat sesi saya</span>
                            </label>
                        </div>

                        <button
                            id="login-submit"
                            type="submit"
                            class="w-full min-h-[44px] rounded-md bg-academic-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-navy-hover active:bg-slate-900 disabled:opacity-60 transition-colors"
                        >
                            Masuk ke SIAKAD
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const loginForm = document.getElementById('login-form');
            const submitButton = document.getElementById('login-submit');

            if (passwordInput && toggleButton) {
                toggleButton.addEventListener('click', function () {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    toggleButton.textContent = isHidden ? 'Sembunyikan' : 'Tampilkan';
                    toggleButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                    toggleButton.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                });
            }

            if (loginForm && submitButton) {
                loginForm.addEventListener('submit', function () {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Memproses...';
                });

                window.addEventListener('pageshow', function () {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Masuk ke SIAKAD';
                });
            }
        })();
    </script>
</body>
</html>
