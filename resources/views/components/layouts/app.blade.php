@props([
    'title' => 'Dashboard',
    'hideSidebar' => false,
])

@php
    $user = auth()->user();
    $role = $user?->role;
    $displayName = $user?->displayName() ?? 'Pengguna';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - SIAKAD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-slate-900 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-md focus:bg-academic-navy focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Langsung ke konten utama
    </a>

    <div id="erp-shell" class="min-h-screen flex flex-col">
        @unless ($hideSidebar)
            <div
                id="sidebar-backdrop"
                class="hidden fixed inset-0 z-30 bg-slate-950/60 lg:hidden"
                aria-hidden="true"
            ></div>

            <aside
                id="app-sidebar"
                aria-label="Navigasi Utama"
                class="fixed inset-y-0 left-0 z-40 w-64 bg-academic-navy text-white flex flex-col border-r border-slate-900/20 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out"
            >
                <div class="h-16 shrink-0 flex items-center justify-between gap-3 px-5 border-b border-white/15">
                    <a href="{{ route($user->dashboardRouteName()) }}" class="flex flex-col min-w-0">
                        <span class="text-base font-semibold tracking-tight text-white">SIAKAD</span>
                        <span class="text-xs text-slate-200 truncate">Sistem Informasi Akademik</span>
                    </a>

                    <button
                        type="button"
                        id="sidebar-close"
                        aria-label="Tutup menu navigasi"
                        class="lg:hidden min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-md text-slate-200 hover:bg-white/10 hover:text-white transition-colors"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-4 flex flex-col gap-5" aria-label="Menu Modul">
                    @if ($role === 'admin')
                        <div>
                            <ul class="flex flex-col gap-1">
                                <li>
                                    <a
                                        href="{{ route('admin.dashboard') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-300">Data Master</p>
                            <ul class="mt-2 flex flex-col gap-1">
                                <li>
                                    <a
                                        href="{{ route('admin.master-prodi.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.master-prodi.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6" />
                                        </svg>
                                        <span>Program Studi</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('admin.master-matkul.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.master-matkul.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5V5a2 2 0 0 1 2-2h14v18H6.5a2.5 2.5 0 0 1-2.5-1.5z" />
                                        </svg>
                                        <span>Mata Kuliah</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('admin.master-dosen.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.master-dosen.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM4 21a8 8 0 0 1 16 0" />
                                        </svg>
                                        <span>Data Dosen</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('admin.master-mahasiswa.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.master-mahasiswa.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 10v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75" />
                                        </svg>
                                        <span>Data Mahasiswa</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-300">Akademik &amp; Keuangan</p>
                            <ul class="mt-2 flex flex-col gap-1">
                                <li>
                                    <a
                                        href="{{ route('admin.periode-akademik.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.periode-akademik.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                                        </svg>
                                        <span>Periode Akademik</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('admin.kelas.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.kelas.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9h6m-6 4h4" />
                                        </svg>
                                        <span>Kelas Perkuliahan</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('admin.keuangan.index') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('admin.keuangan.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                        </svg>
                                        <span>Keuangan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @elseif ($role === 'dosen')
                        <div>
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-300">Menu Dosen</p>
                            <ul class="mt-2 flex flex-col gap-1">
                                <li>
                                    <a
                                        href="{{ route('dosen.dashboard') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('dosen.dashboard', 'dosen.kelas.*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Jadwal Mengajar</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('dosen.profil') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('dosen.profil*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Profil Saya</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @elseif ($role === 'mahasiswa')
                        <div>
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-300">Menu Mahasiswa</p>
                            <ul class="mt-2 flex flex-col gap-1">
                                <li>
                                    <a
                                        href="{{ route('mahasiswa.dashboard') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Dashboard Akademik</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('mahasiswa.jadwal') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('mahasiswa.jadwal') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Jadwal Kuliah</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('mahasiswa.khs') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('mahasiswa.khs') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Kartu Hasil Studi</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('mahasiswa.biaya') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('mahasiswa.biaya') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Biaya Kuliah</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('mahasiswa.profil') }}"
                                        class="min-h-[44px] flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium border-l-3 transition-colors {{ request()->routeIs('mahasiswa.profil*') ? 'bg-white/15 text-white border-amber-400' : 'text-slate-200 border-transparent hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span>Profil Saya</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </nav>

                <div class="shrink-0 border-t border-white/15 p-4">
                    <p class="text-sm font-medium text-white truncate">{{ $displayName }}</p>
                    <p class="text-xs text-slate-200 uppercase">{{ $role }}</p>
                </div>
            </aside>
        @endunless

        <div
            id="main-wrapper"
            class="flex-1 flex flex-col min-w-0 transition-[padding] duration-200 ease-out {{ $hideSidebar ? '' : 'lg:pl-64' }}"
        >
            <header class="sticky top-0 z-20 h-16 shrink-0 bg-white border-b border-slate-300 flex items-center justify-between gap-4 px-4 sm:px-6">
                <div class="flex items-center gap-3 min-w-0">
                    @unless ($hideSidebar)
                        <button
                            type="button"
                            id="sidebar-toggle"
                            aria-controls="app-sidebar"
                            aria-expanded="true"
                            aria-label="Buka atau tutup sidebar navigasi"
                            class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center gap-2 rounded-md border border-slate-500 bg-white px-3 text-sm font-medium text-slate-800 hover:bg-slate-100 active:bg-slate-200 transition-colors"
                        >
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span class="sm:hidden">Menu</span>
                        </button>
                    @else
                        <span class="text-base font-semibold tracking-tight text-academic-navy">SIAKAD</span>
                    @endunless

                    <span class="text-sm font-semibold text-slate-700 truncate hidden sm:inline">
                        {{ $title }}
                    </span>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if ($activePeriode)
                        <span class="hidden md:inline-flex items-center rounded-md border border-slate-400 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800">
                            Periode: {{ $activePeriode->nama_periode }}
                        </span>
                    @endif

                    <span class="hidden sm:inline text-sm text-slate-700">
                        {{ $displayName }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="min-h-[44px] rounded-md border border-slate-500 bg-white px-3.5 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100 active:bg-slate-200 transition-colors"
                        >
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            <main id="main-content" tabindex="-1" class="flex-1 p-4 sm:p-6 bg-gray-50 min-w-0">
                <x-flash-alerts />
                {{ $slot }}
            </main>
        </div>
    </div>

    @unless ($hideSidebar)
        <script>
            (function () {
                const sidebar = document.getElementById('app-sidebar');
                const mainWrapper = document.getElementById('main-wrapper');
                const toggleButton = document.getElementById('sidebar-toggle');
                const closeButton = document.getElementById('sidebar-close');
                const backdrop = document.getElementById('sidebar-backdrop');
                const storageKey = 'siakad_sidebar_collapsed';

                if (!sidebar || !mainWrapper || !toggleButton) {
                    return;
                }

                const desktopMq = window.matchMedia('(min-width: 1024px)');
                let mobileOpen = false;
                let desktopCollapsed = false;

                try {
                    desktopCollapsed = window.localStorage.getItem(storageKey) === '1';
                } catch (e) {
                    desktopCollapsed = false;
                }

                function applyLayout() {
                    if (desktopMq.matches) {
                        mobileOpen = false;
                        if (backdrop) {
                            backdrop.classList.add('hidden');
                        }
                        document.body.classList.remove('overflow-hidden');

                        if (desktopCollapsed) {
                            sidebar.classList.remove('lg:translate-x-0');
                            sidebar.classList.add('-translate-x-full');
                            sidebar.setAttribute('aria-hidden', 'true');
                            mainWrapper.classList.remove('lg:pl-64');
                            mainWrapper.classList.add('lg:pl-0');
                            toggleButton.setAttribute('aria-expanded', 'false');
                        } else {
                            sidebar.classList.add('lg:translate-x-0');
                            sidebar.classList.remove('-translate-x-full');
                            sidebar.removeAttribute('aria-hidden');
                            mainWrapper.classList.add('lg:pl-64');
                            mainWrapper.classList.remove('lg:pl-0');
                            toggleButton.setAttribute('aria-expanded', 'true');
                        }
                    } else {
                        mainWrapper.classList.remove('lg:pl-64', 'lg:pl-0');
                        sidebar.classList.remove('lg:translate-x-0');

                        if (mobileOpen) {
                            sidebar.classList.remove('-translate-x-full');
                            sidebar.classList.add('translate-x-0');
                            sidebar.removeAttribute('aria-hidden');
                            if (backdrop) {
                                backdrop.classList.remove('hidden');
                            }
                            document.body.classList.add('overflow-hidden');
                            toggleButton.setAttribute('aria-expanded', 'true');
                        } else {
                            sidebar.classList.add('-translate-x-full');
                            sidebar.classList.remove('translate-x-0');
                            sidebar.setAttribute('aria-hidden', 'true');
                            if (backdrop) {
                                backdrop.classList.add('hidden');
                            }
                            document.body.classList.remove('overflow-hidden');
                            toggleButton.setAttribute('aria-expanded', 'false');
                        }
                    }
                }

                toggleButton.addEventListener('click', function () {
                    if (desktopMq.matches) {
                        desktopCollapsed = !desktopCollapsed;
                        try {
                            window.localStorage.setItem(storageKey, desktopCollapsed ? '1' : '0');
                        } catch (e) {}
                    } else {
                        mobileOpen = !mobileOpen;
                    }
                    applyLayout();

                    if (!desktopMq.matches && mobileOpen && closeButton) {
                        closeButton.focus();
                    }
                });

                function closeMobileSidebar() {
                    if (!desktopMq.matches && mobileOpen) {
                        mobileOpen = false;
                        applyLayout();
                        toggleButton.focus();
                    }
                }

                if (closeButton) {
                    closeButton.addEventListener('click', closeMobileSidebar);
                }

                if (backdrop) {
                    backdrop.addEventListener('click', closeMobileSidebar);
                }

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeMobileSidebar();
                    }
                });

                if (typeof desktopMq.addEventListener === 'function') {
                    desktopMq.addEventListener('change', applyLayout);
                } else {
                    window.addEventListener('resize', applyLayout);
                }

                applyLayout();
            })();
        </script>
    @endunless
</body>
</html>

