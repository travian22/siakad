# SIAKAD (Sistem Informasi Akademik)

Aplikasi web manajemen akademik kampus berbasis **Laravel 13**, **Tailwind CSS v4**, **Vanilla JavaScript**, dan **MySQL/MariaDB** dengan arsitektur pemisahan akses tiga peran pengguna: **Admin**, **Dosen**, dan **Mahasiswa**.

Dokumen ini berisi seluruh panduan dalam satu tempat:
1. [Panduan Instalasi & Menjalankan Aplikasi](#1-panduan-instalasi--menjalankan-aplikasi) (dari clone repository sampai `php artisan serve`).
2. [Panduan Letak Kode & Alur Logika Fitur](#2-panduan-letak-kode--alur-logika-fitur) (penjelasan lengkap untuk pemula: setiap fitur ada di file mana saja dan bagaimana cara kerjanya).

---

## Ringkasan Teknologi & Arsitektur

| Komponen | Teknologi yang Dipakai |
| --- | --- |
| Backend Framework | Laravel 13 (`laravel/framework: ^13.17`), PHP `^8.3` (diuji di PHP 8.4) |
| Database | MySQL / MariaDB |
| Frontend & Styling | Blade Templates + Tailwind CSS v4 (`@tailwindcss/vite`) + Vanilla JS |
| Asset Bundler | Vite 8 (`vite: ^8.0.0`) |
| Testing & Formatter | PHPUnit 12 & Laravel Pint |

### Cara Kerja Alur Request di Project Ini (Untuk Pemula)
Setiap kali pengguna membuka URL atau mengirim form di browser, alurnya berjalan seperti ini:
1. **Browser** mengirim request ke alamat URL tertentu (misalnya `/dosen/kelas/3/nilai`).
2. **`routes/web.php`** mencocokkan URL tersebut, lalu menjalankan **Middleware** (satpam pengecekan) seperti `auth` (sudah login?), `role:dosen` (apakah rolenya dosen?), dan `profile.complete` (apakah biodatanya sudah diisi?).
3. Jika request mengirim form (`POST`/`PUT`), Laravel memvalidasi input lewat **Form Request** (`app/Http/Requests/...`) atau `$request->validate()`.
4. **Controller** (`app/Http/Controllers/...`) mengambil/menyimpan data menggunakan **Model Eloquent** (`app/Models/...`) dan **Service** (`app/Services/...`), misalnya `GradingCalculator` untuk hitung nilai atau `AcademicTranscriptService` untuk hitung IPK.
5. **Controller** mengembalikan tampilan **Blade View** (`resources/views/...`) atau mengarahkan ulang (`redirect()`) dengan pesan notifikasi.

---

## 1. Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat Sistem
Pastikan perangkat Anda sudah terpasang:
- **PHP >= 8.3** (dengan ekstensi aktif: `pdo_mysql`, `gd`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`)
- **Composer**
- **Node.js & npm**
- **MySQL / MariaDB** (bisa menggunakan Laragon, XAMPP, Docker, atau service MySQL lokal)
- **Git**

> Catatan: Untuk meng-generate avatar demo bawaan di seeder (`profiles/dosen2.png`), pastikan ekstensi PHP `gd` aktif. Jika belum aktif, seeder tetap berjalan, hanya gambar PNG demo tidak dibuat otomatis.

---

### Langkah 1: Clone Repository
Buka terminal (PowerShell, CMD, atau Terminal) lalu clone repository dan masuk ke folder project:

```bash
git clone <url-repository-anda> siakad
cd siakad
```

---

### Langkah 2: Install Dependensi PHP & JavaScript
Install seluruh library Laravel (Composer) dan paket frontend Tailwind/Vite (npm):

```bash
composer install
npm install
```

---

### Langkah 3: Buat & Konfigurasi File `.env`
Salin file template `.env.example` menjadi `.env`:

- **Windows (PowerShell / CMD):**
  ```powershell
  copy .env.example .env
  ```
- **macOS / Linux / Git Bash:**
  ```bash
  cp .env.example .env
  ```

Setelah itu, generate application encryption key:

```bash
php artisan key:generate
```

Lalu buka file `.env` dan ubah bagian koneksi database dari `sqlite` menjadi `mysql` sesuai pengaturan MySQL lokal Anda:

```dotenv
APP_NAME=SIAKAD
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad
DB_USERNAME=root
DB_PASSWORD=
```

*(Jika MySQL Anda menggunakan password atau port selain `3306`, sesuaikan `DB_PORT`, `DB_USERNAME`, dan `DB_PASSWORD`.)*

---

### Langkah 4: Buat Database MySQL
Pastikan service MySQL sudah berjalan, lalu buat database kosong bernama `siakad` (bisa melalui phpMyAdmin, HeidiSQL, TablePlus, atau terminal MySQL):

```sql
CREATE DATABASE IF NOT EXISTS siakad;
```

Jika Anda juga ingin menjalankan suite automated test (`php artisan test`), buat satu database terpisah bernama `siakad_testing` (karena `phpunit.xml` dikonfigurasi ke `siakad_testing` agar data utama di `siakad` tidak ikut terhapus saat testing):

```sql
CREATE DATABASE IF NOT EXISTS siakad_testing;
```

---

### Langkah 5: Jalankan Migrasi Database & Seeder Data Awal
Jalankan perintah berikut untuk membuat seluruh tabel database dan mengisi data demo awal (Admin, Periode Akademik, Prodi, Mata Kuliah, Dosen, Mahasiswa, Kelas, Nilai, dan Tagihan):

```bash
php artisan migrate --seed
```

Jika di tengah jalan Anda ingin mereset ulang seluruh isi database ke kondisi awal seeder:

```bash
php artisan migrate:fresh --seed
```

> Peringatan: `migrate:fresh` akan menghapus semua tabel dan data yang ada di database `siakad`, lalu membuatnya ulang dari nol.

---

### Langkah 6: Hubungkan Folder Storage Publik (`storage:link`)
Foto profil yang diunggah Dosen dan Mahasiswa disimpan di `storage/app/public`. Agar foto bisa diakses browser melalui URL `/storage/...`, jalankan:

```bash
php artisan storage:link
```

Perintah ini membuat shortcut/symlink `public/storage` menuju `storage/app/public`.

---

### Langkah 7: Compile Aset Frontend (Tailwind CSS & Vite)
Build file CSS dan JS ke folder `public/build`:

```bash
npm run build
```

*(Opsional saat sedang ngoding tampilan: jalankan `npm run dev` di terminal terpisah agar perubahan class Tailwind langsung ter-refresh otomatis tanpa harus `npm run build` berulang kali.)*

---

### Langkah 8: Jalankan Server Lokal
Jalankan server bawaan Laravel:

```bash
php artisan serve
```

Buka browser dan kunjungi:
- **`http://localhost:8000`** (otomatis diarahkan ke halaman login `http://localhost:8000/login`).

---

### Daftar Akun Demo Hasil Seeder
Semua akun di bawah ini menggunakan kata sandi yang sama: **`password`**.

| Role | Username | Password | Kondisi & Kegunaan Pengujian |
| --- | --- | --- | --- |
| **Admin** | `admin` | `password` | Mengelola Dashboard, Data Master, Periode Akademik, Kelas & Peserta, dan Keuangan. |
| **Dosen** | `dosen1` | `password` | **Profil belum lengkap.** Saat login akan dicegat middleware dan diarahkan ke form *Lengkapi Data Diri* tanpa sidebar. |
| **Dosen** | `dosen2` | `password` | **Profil sudah lengkap.** Langsung masuk ke Jadwal Mengajar dan halaman Input Nilai (`IF102 Basis Data` & `IF101 Algoritma`). |
| **Mahasiswa** | `mhs1` | `password` | **Profil belum lengkap.** Saat login akan dicegat middleware dan diarahkan ke form *Lengkapi Data Diri* tanpa sidebar. |
| **Mahasiswa** | `mhs2` | `password` | **Profil sudah lengkap.** Sudah memiliki data IPK (`4.00`), 6 SKS lulus, grafik IPS, jadwal kuliah, KHS, dan status tagihan `Lunas`. |
| **Mahasiswa** | `mhs3`, `mhs4`, `mhs5` | `password` | **Profil sudah lengkap.** Memiliki data kelas aktif dan tagihan yang beragam (`Lunas` dan `Belum Bayar`). |

---

### Menjalankan Test & Pengecekan Gaya Kode
Untuk memastikan seluruh fitur berjalan baik (memerlukan database `siakad_testing`):

```bash
php artisan test --compact
```

Untuk merapikan format kode PHP sesuai standar Laravel Pint:

```bash
vendor/bin/pint --format agent
```

---

### Troubleshooting Umum (Untuk Pemula)
1. **Error `Unable to locate file in Vite manifest` saat membuka browser**
   - Penyebab: `npm run build` belum dijalankan (folder `public/build/manifest.json` belum ada).
   - Solusi: Jalankan `npm install` lalu `npm run build`.
2. **Error `could not find driver` saat migrate atau menjalankan test**
   - Penyebab: Ekstensi `pdo_mysql` belum aktif di `php.ini`, atau `phpunit.xml` mengarah ke driver yang belum terpasang.
   - Solusi: Aktifkan `extension=pdo_mysql` di PHP, dan pastikan database `siakad` serta `siakad_testing` sudah dibuat di MySQL.
3. **Foto profil Dosen/Mahasiswa tidak muncul (gambar pecah / 404)**
   - Penyebab: Symlink `public/storage` belum dibuat.
   - Solusi: Jalankan `php artisan storage:link`.
4. **Error koneksi MySQL (`SQLSTATE[HY000] [1049] Unknown database 'siakad'`)**
   - Penyebab: Database `siakad` belum dibuat di MySQL atau service MySQL belum menyala.
   - Solusi: Nyalakan MySQL dan buat database `siakad` terlebih dahulu.

---

## 2. Panduan Letak Kode & Alur Logika Fitur

### Peta Struktur Direktori Inti

| Folder / File | Fungsi |
| --- | --- |
| `routes/web.php` | Daftar seluruh URL, nama route, grup prefix (`/admin`, `/dosen`, `/mahasiswa`), dan middleware. |
| `bootstrap/app.php` | Pendaftaran alias middleware (`role`, `profile.complete`) serta aturan redirect guest/user login. |
| `app/Providers/AppServiceProvider.php` | Konfigurasi rate limiter login dan View Composer untuk membagikan `$activePeriode` ke layout. |
| `app/Http/Middleware/` | Pengecekan hak akses role (`RoleMiddleware`) dan kelengkapan profil (`CheckProfileComplete`). |
| `app/Http/Requests/` | Validasi form input dan otorisasi sebelum masuk ke controller. |
| `app/Http/Controllers/` | Logika pemrosesan request per peran (`AuthController`, `Admin/*`, `Dosen/*`, `Mahasiswa/*`). |
| `app/Models/` | Representasi tabel database, `$fillable`, `casts()`, relasi Eloquent, dan helper domain. |
| `app/Services/` | Logika bisnis murni/khusus: perhitungan nilai (`GradingCalculator`), IPK/IPS (`AcademicTranscriptService`), upload foto (`ProfilePhotoService`). |
| `database/migrations/` | Definisi skema 9 tabel aplikasi + tabel sistem Laravel (`cache`, `jobs`, `sessions`). |
| `database/factories/` | Pembuat data palsu/dummy untuk kebutuhan automated testing. |
| `database/seeders/DatabaseSeeder.php` | Pengisi data awal demo aplikasi. |
| `resources/views/` | Seluruh tampilan HTML/Blade (`auth/`, `components/`, `admin/`, `dosen/`, `mahasiswa/`). |
| `resources/css/app.css` | Konfigurasi Tailwind CSS v4, token warna brand (`#1E3A5F`), cursor pointer, dan focus style aksesibilitas. |
| `tests/Unit/` & `tests/Feature/` | Pengujian otomatis logika kalkulasi dan seluruh alur HTTP aplikasi. |

---

### Skema Database & Relasi Model

Seluruh tabel menggunakan penamaan `snake_case` persis sesuai PRD:

| Tabel | File Migration | Model | Kolom Utama & Relasi |
| --- | --- | --- | --- |
| `users` | `database/migrations/0001_01_01_000000_create_users_table.php` | `app/Models/User.php` | `id`, `username` (unik), `password`, `role` (`admin`/`dosen`/`mahasiswa`). Relasi: `hasOne(Dosen)`, `hasOne(Mahasiswa)`. |
| `periode_akademik` | `database/migrations/2026_11_11_170905_create_periode_akademiks_table.php` | `app/Models/PeriodeAkademik.php` | `id`, `nama_periode`, `is_active` (`boolean`). Relasi: `hasMany(KelasPerkuliahan)`, `hasMany(BiayaKuliah)`. |
| `program_studi` | `database/migrations/2026_11_11_170906_create_program_studis_table.php` | `app/Models/ProgramStudi.php` | `id`, `kode_prodi` (unik), `nama_prodi`. Relasi: `hasMany(Mahasiswa)`. |
| `dosen` | `database/migrations/2026_11_11_170907_create_dosens_table.php` | `app/Models/Dosen.php` | `id`, `user_id`, `nip` (unik), `nama_lengkap`, `jenis_kelamin`, `telepon`, `alamat`, `foto_profil`. Relasi: `belongsTo(User)`, `hasMany(KelasPerkuliahan)`. |
| `mahasiswa` | `database/migrations/2026_11_11_170908_create_mahasiswas_table.php` | `app/Models/Mahasiswa.php` | `id`, `user_id`, `program_studi_id`, `npm` (unik), `nama_lengkap`, `angkatan`, `jenis_kelamin`, `alamat`, `foto_profil`. Relasi: `belongsTo(User)`, `belongsTo(ProgramStudi)`, `hasMany(PesertaKelas)`, `hasMany(BiayaKuliah)`. |
| `mata_kuliah` | `database/migrations/2026_11_11_170909_create_mata_kuliahs_table.php` | `app/Models/MataKuliah.php` | `id`, `kode_mk` (unik), `nama_mk`, `sks`. Relasi: `hasMany(KelasPerkuliahan)`. |
| `kelas_perkuliahan` | `database/migrations/2026_11_11_170910_create_kelas_perkuliahans_table.php` | `app/Models/KelasPerkuliahan.php` | `id`, `periode_akademik_id`, `mata_kuliah_id`, `dosen_id`, `nama_kelas`, `hari`, `jam_mulai`, `jam_selesai`, `ruangan`. Relasi: `belongsTo` Periode/Matkul/Dosen, `hasMany(PesertaKelas)`. |
| `peserta_kelas` | `database/migrations/2026_11_11_170911_create_peserta_kelas_table.php` | `app/Models/PesertaKelas.php` | `id`, `kelas_perkuliahan_id`, `mahasiswa_id`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `nilai_akhir`, `nilai_huruf`. Relasi: `belongsTo(KelasPerkuliahan)`, `belongsTo(Mahasiswa)`. |
| `biaya_kuliah` | `database/migrations/2026_11_11_170912_create_biaya_kuliahs_table.php` | `app/Models/BiayaKuliah.php` | `id`, `mahasiswa_id`, `periode_akademik_id`, `nominal_tagihan`, `status_bayar` (`belum_bayar`/`lunas`). Relasi: `belongsTo(Mahasiswa)`, `belongsTo(PeriodeAkademik)`. |

---

### Detail Letak Kode & Logika Per Fitur

#### 1. Autentikasi (Login, Logout, Rate Limiter, Redirect per Role)
- **File terkait:**
  - Route: `routes/web.php` (`/`, `/login`, `/logout`)
  - Konfigurasi Rate Limit: `app/Providers/AppServiceProvider.php`
  - Validasi Input: `app/Http/Requests/LoginRequest.php`
  - Controller: `app/Http/Controllers/AuthController.php`
  - Helper Redirect Role: `app/Models/User.php` (`dashboardRouteName()`) & `bootstrap/app.php` (`redirectGuestsTo`, `redirectUsersTo`)
  - Tampilan: `resources/views/auth/login.blade.php`
- **Alur logika:**
  1. Membuka `/` saat belum login akan diarahkan ke `/login` (`route('login')`); jika sudah login langsung diarahkan ke dashboard sesuai role (`admin.dashboard`, `dosen.dashboard`, atau `mahasiswa.dashboard`) lewat `User::dashboardRouteName()`.
  2. Halaman login memakai `username` dan `password` (bukan email). `POST /login` dibatasi maksimal 5 kali per menit berdasarkan kombinasi `username` + IP (`RateLimiter::for('login', ...)` di `AppServiceProvider`).
  3. `AuthController::login()` memanggil `Auth::attempt(['username' => ..., 'password' => ...], $remember)`. Jika berhasil, session di-regenerate untuk keamanan session fixation lalu diarahkan ke dashboard rolenya.
  4. `POST /logout` memanggil `Auth::logout()`, menginvalidasi session, dan mereset CSRF token.
  5. Di `login.blade.php`, Vanilla JS menangani tombol toggle tampil/sembunyikan password (`#toggle-password`) dan mencegah double-submit saat tombol ditekan (`#login-submit`).

#### 2. Keamanan Role & Interceptor Kelengkapan Profil
- **File terkait:**
  - Registrasi Alias: `bootstrap/app.php` (`'role' => RoleMiddleware::class`, `'profile.complete' => CheckProfileComplete::class`)
  - Middleware Role: `app/Http/Middleware/RoleMiddleware.php`
  - Middleware Profil: `app/Http/Middleware/CheckProfileComplete.php`
  - Pengecekan Status Profil: `app/Models/User.php` (`isProfileComplete()`), `app/Models/Dosen.php` (`isProfileComplete()`), `app/Models/Mahasiswa.php` (`isProfileComplete()`)
  - Penerapan Route Group: `routes/web.php`
- **Alur logika:**
  1. `RoleMiddleware` memeriksa `in_array($user->role, $roles, true)`. Jika user mencoba membuka URL role lain (misalnya Mahasiswa membuka `/admin/dashboard`), request langsung ditolak dengan status `403 Forbidden`.
  2. `CheckProfileComplete` dipasang pada route Dosen (`/dosen/dashboard`, `/dosen/kelas/{id}/nilai`) dan route Mahasiswa (`/mahasiswa/dashboard`, `/mahasiswa/jadwal`, `/mahasiswa/khs`, `/mahasiswa/biaya`).
  3. Route `/dosen/profil` dan `/mahasiswa/profil` sengaja diletakkan di **luar** middleware `profile.complete` (dan middleware sendiri juga mengecualikan `dosen.profil*` / `mahasiswa.profil*`) agar tidak terjadi infinite redirect loop.
  4. Kriteria profil lengkap:
     - **Dosen (`Dosen::isProfileComplete()`):** `jenis_kelamin`, `telepon`, `alamat`, dan `foto_profil` harus terisi semua.
     - **Mahasiswa (`Mahasiswa::isProfileComplete()`):** `jenis_kelamin` dan `alamat` harus terisi (`foto_profil` opsional).
  5. Jika belum lengkap, middleware mengarahkan user ke `/dosen/profil` atau `/mahasiswa/profil` dengan flash `warning`. Pada view profil tersebut, `<x-layouts.app :hide-sidebar="! $isProfileComplete">` akan menyembunyikan sidebar navigasi sehingga halaman menjadi full-page form *Lengkapi Data Diri*.

#### 3. Global Layout ERP & Sidebar Buka-Tutup (Desktop & Mobile)
- **File terkait:**
  - Komponen Layout Utama: `resources/views/components/layouts/app.blade.php`
  - Komponen Header Halaman: `resources/views/components/page-header.blade.php`
  - Komponen Alert Flash & Validasi: `resources/views/components/flash-alerts.blade.php`
  - View Composer Periode Aktif: `app/Providers/AppServiceProvider.php`
  - CSS & Tema Brand: `resources/css/app.css`
- **Alur logika:**
  1. Layout mengikuti struktur ERP: Sidebar fixed selebar `w-64` berwarna Academic Navy (`#1E3A5F`), Header sticky `justify-between`, dan Main Content `flex-1 p-6 bg-gray-50` dengan `min-w-0` agar tabel lebar tidak merusak layout mobile.
  2. `AppServiceProvider` menyuntikkan variabel `$activePeriode` (diambil dari `PeriodeAkademik::currentActive()`) ke `components.layouts.app` setiap kali layout dirender, sehingga file Blade tidak melakukan query database langsung.
  3. Script Vanilla JS di bagian bawah `app.blade.php` mengatur tombol `#sidebar-toggle`:
     - Pada layar **Desktop (`>= 1024px`)**: membuka/menutup sidebar dengan menggeser sidebar (`-translate-x-full`) dan menyesuaikan padding konten (`lg:pl-64` <-> `lg:pl-0`), lalu menyimpan preferensi di `localStorage` (`siakad_sidebar_collapsed`).
     - Pada layar **Mobile/Tablet (`< 1024px`)**: membuka sidebar sebagai slide-over drawer beserta backdrop gelap (`#sidebar-backdrop`), dan dapat ditutup lewat tombol `#sidebar-close`, klik backdrop, atau tombol keyboard `Escape`.

#### 4. Modul Admin: Dashboard Statistik & Grafik
- **File terkait:**
  - Controller: `app/Http/Controllers/Admin/DashboardController.php`
  - View: `resources/views/admin/dashboard.blade.php`
  - Route: `admin.dashboard` (`GET /admin/dashboard`)
- **Alur logika:**
  - Menghitung jumlah riil Mahasiswa, Dosen, Program Studi, Mata Kuliah, jumlah kelas pada periode aktif (`PeriodeAkademik::currentActive()`), serta ringkasan tagihan (`lunas` vs `belum_bayar`).
  - Mengambil `ProgramStudi::withCount('mahasiswa')` untuk menggambar grafik batang distribusi mahasiswa per program studi menggunakan bar persentase terukur, plus menampilkan 5 kelas perkuliahan terbaru.

#### 5. Modul Admin: Master Data (Prodi, Mata Kuliah, Dosen, Mahasiswa)
- **File terkait:**
  - **Program Studi:** `app/Http/Controllers/Admin/ProgramStudiController.php`, `resources/views/admin/prodi/index.blade.php`, `resources/views/admin/prodi/form.blade.php`
  - **Mata Kuliah:** `app/Http/Controllers/Admin/MataKuliahController.php`, `resources/views/admin/matkul/index.blade.php`, `resources/views/admin/matkul/form.blade.php`
  - **Dosen:** `app/Http/Controllers/Admin/DosenController.php`, `app/Http/Requests/Admin/DosenRequest.php`, `resources/views/admin/dosen/index.blade.php`, `resources/views/admin/dosen/form.blade.php`
  - **Mahasiswa:** `app/Http/Controllers/Admin/MahasiswaController.php`, `app/Http/Requests/Admin/MahasiswaRequest.php`, `resources/views/admin/mahasiswa/index.blade.php`, `resources/views/admin/mahasiswa/form.blade.php`
- **Alur logika:**
  1. Seluruh halaman index mendukung pencarian (`?q=`) dan pagination (`10` data per halaman), serta filter `program_studi_id` khusus di Master Mahasiswa.
  2. Pada **Dosen** dan **Mahasiswa**, proses simpan (`store`), ubah (`update`), dan hapus (`destroy`) dijalankan di dalam `DB::transaction()` karena sekaligus mengelola akun login di tabel `users` (`role: 'dosen'` atau `'mahasiswa'`) dan profilnya di tabel `dosen` atau `mahasiswa`.
  3. Saat edit Dosen/Mahasiswa, input `password` bersifat opsional: hanya di-hash dan diperbarui jika Admin mengisinya.
  4. **Proteksi hapus:** Program Studi yang masih punya Mahasiswa, Matkul yang dipakai di Kelas, Dosen yang masih mengampu Kelas, serta Mahasiswa yang sudah ikut Kelas atau memiliki Tagihan tidak bisa dihapus langsung (Controller membatalkan dan mengembalikan pesan error agar integritas data terjaga).

#### 6. Modul Admin: Periode Akademik (Single Active Semester)
- **File terkait:**
  - Controller: `app/Http/Controllers/Admin/PeriodeAkademikController.php`
  - Model Helper: `app/Models/PeriodeAkademik.php` (`currentActive()`, `scopeOrdered()`)
  - View: `resources/views/admin/periode/index.blade.php`, `resources/views/admin/periode/form.blade.php`
- **Alur logika:**
  - Menjaga aturan PRD bahwa **hanya boleh ada 1 periode dengan `is_active = true`**.
  - Ketika tombol **Aktifkan** ditekan (`POST /admin/periode-akademik/{periode}/activate`) atau checkbox langsung aktif dicentang saat membuat periode baru, backend menjalankan `DB::transaction()`: mengubah semua record `periode_akademik` menjadi `is_active = false`, lalu mengubah periode terpilih menjadi `is_active = true`.
  - Periode yang sedang aktif atau sudah memiliki kelas/tagihan tidak dapat dihapus.

#### 7. Modul Admin: Kelas Perkuliahan & Peserta Kelas
- **File terkait:**
  - Controller: `app/Http/Controllers/Admin/KelasPerkuliahanController.php`
  - Form Request: `app/Http/Requests/Admin/KelasPerkuliahanRequest.php`
  - Model Constant: `app/Models/KelasPerkuliahan.php` (`DAYS`)
  - View: `resources/views/admin/kelas/index.blade.php`, `resources/views/admin/kelas/form.blade.php`, `resources/views/admin/kelas/peserta.blade.php`
- **Alur logika:**
  1. Admin membuat jadwal kelas dengan menghubungkan `periode_akademik_id` (otomatis memilih periode aktif saat create), `mata_kuliah_id`, `dosen_id`, `nama_kelas`, `hari`, `jam_mulai`, `jam_selesai` (`after:jam_mulai`), dan `ruangan`.
  2. Melalui halaman **Kelola Peserta** (`GET /admin/kelas/{kelas}/peserta`), Admin bisa memilih satu atau banyak mahasiswa sekaligus (dengan tombol JS *Pilih Semua*) untuk didaftarkan ke `peserta_kelas` (`PesertaKelas::firstOrCreate` di dalam `DB::transaction()` agar tidak ganda).
  3. Menghapus peserta kelas (`DELETE /admin/kelas/{kelas}/peserta/{peserta}`) memverifikasi `$peserta->kelas_perkuliahan_id === $kelas->id` (`404` jika tidak cocok) dan menolak penghapusan jika mahasiswa tersebut sudah memiliki `nilai_akhir`.

#### 8. Modul Admin: Keuangan (Tagihan Mahasiswa)
- **File terkait:**
  - Controller: `app/Http/Controllers/Admin/KeuanganController.php`
  - Form Request: `app/Http/Requests/Admin/BiayaKuliahRequest.php`
  - View: `resources/views/admin/keuangan/index.blade.php`, `resources/views/admin/keuangan/form.blade.php`
- **Alur logika:**
  - Mengelola record `biaya_kuliah` per mahasiswa per periode akademik (`nominal_tagihan` dan `status_bayar`: `belum_bayar` / `lunas`).
  - `BiayaKuliahRequest` mencegah tagihan ganda untuk kombinasi `mahasiswa_id` + `periode_akademik_id` yang sama.
  - Terdapat aksi cepat `PATCH /admin/keuangan/{biaya}/toggle-status` untuk mengubah status `Belum Bayar` <-> `Lunas` langsung dari tabel.

#### 9. Modul Dosen: Lengkapi Profil & Upload Foto
- **File terkait:**
  - Controller: `app/Http/Controllers/Dosen/ProfilController.php`
  - Form Request: `app/Http/Requests/Dosen/UpdateProfilRequest.php`
  - Service Foto: `app/Services/ProfilePhotoService.php`
  - View: `resources/views/dosen/profil.blade.php`
- **Alur logika:**
  - Jika dosen belum memiliki `foto_profil`, unggahan foto wajib diisi (`required|image|mimes:jpg,jpeg,png,webp|max:2048`). Jika sudah pernah mengunggah foto, unggahan baru bersifat opsional (`nullable`).
  - `ProfilePhotoService::replace()` menyimpan gambar baru ke `storage/app/public/profiles/dosen` dan menghapus file foto lama jika ada.
  - Setelah simpan berhasil, dosen diarahkan ke `dosen.dashboard`.

#### 10. Modul Dosen: Jadwal Mengajar
- **File terkait:**
  - Controller: `app/Http/Controllers/Dosen/DashboardController.php`
  - Model Helper: `app/Models/KelasPerkuliahan.php` (`daySortOrder()`)
  - View: `resources/views/dosen/dashboard.blade.php`
- **Alur logika:**
  - Mengambil seluruh kelas di tabel `kelas_perkuliahan` yang dimiliki dosen login (`where('dosen_id', $dosen->id)`) khusus pada periode aktif (`PeriodeAkademik::currentActive()`), lengkap dengan jumlah mahasiswa dan jumlah yang sudah dinilai (`dinilai_count`), lalu diurutkan berdasarkan hari (`daySortOrder()`) dan `jam_mulai`.

#### 11. Modul Dosen: Input Nilai (Tab Switch Vanilla JS & Transactional Grading)
- **File terkait:**
  - Controller: `app/Http/Controllers/Dosen/NilaiController.php`
  - Form Request: `app/Http/Requests/Dosen/StoreNilaiRequest.php`
  - Service Formula Nilai: `app/Services/GradingCalculator.php`
  - View: `resources/views/dosen/nilai.blade.php`
- **Alur logika:**
  1. `NilaiController` mengisolasi akses kelas dengan `KelasPerkuliahan::where('dosen_id', $dosen->id)->findOrFail($id)` sehingga dosen tidak bisa membuka atau mengisi nilai kelas dosen lain (`404`).
  2. Di halaman `dosen/nilai.blade.php`, terdapat dua container utama sesuai PRD:
     - `<div id="tab-peserta">...</div>` (tabel rekap nilai tersimpan).
     - `<div id="tab-form-nilai" class="hidden">...</div>` (form input batch Tugas, UTS, UAS; otomatis terbuka jika ada error validasi).
  3. Perpindahan tab dilakukan di browser tanpa reload halaman menggunakan Vanilla JS `document.getElementById(...).classList.toggle('hidden')`. Saat mengetik angka, JS juga menghitung estimasi nilai di kolom *Estimasi Akhir* untuk membantu dosen sebelum menekan simpan.
  4. Saat form disubmit (`POST /dosen/kelas/{id}/nilai`):
     - `StoreNilaiRequest` memeriksa angka `0` sampai `100`, serta lewat `after()` hook memastikan jika salah satu komponen (Tugas/UTS/UAS) untuk seorang mahasiswa diisi, maka ketiga komponen wajib lengkap (menggunakan `filled()` agar nilai `0` tetap dianggap valid).
     - `NilaiController::store()` berjalan di dalam **`DB::transaction()`**, mencocokkan ID peserta hanya terhadap kelas terkait (mengabaikan ID asing yang dimanipulasi), lalu memanggil `GradingCalculator::evaluate()`:
       - `Nilai Akhir = round((Tugas * 0.20) + (UTS * 0.30) + (UAS * 0.50), 2)`
       - `>= 85` -> `A` | `75 - 84.99` -> `B` | `60 - 74.99` -> `C` | `50 - 59.99` -> `D` | `< 50` -> `E`

#### 12. Modul Mahasiswa: Lengkapi Profil
- **File terkait:**
  - Controller: `app/Http/Controllers/Mahasiswa/ProfilController.php`
  - Form Request: `app/Http/Requests/Mahasiswa/UpdateProfilRequest.php`
  - Service Foto: `app/Services/ProfilePhotoService.php`
  - View: `resources/views/mahasiswa/profil.blade.php`
- **Alur logika:**
  - Mewajibkan `nama_lengkap`, `jenis_kelamin`, dan `alamat`, sementara `foto_profil` bersifat opsional (disimpan ke `storage/app/public/profiles/mahasiswa` via `ProfilePhotoService`).
  - Setelah data tersimpan, `Mahasiswa::isProfileComplete()` bernilai `true` dan mahasiswa diarahkan ke `mahasiswa.dashboard`.

#### 13. Modul Mahasiswa: Dashboard Akademik (IPK, SKS Kumulatif, Chart IPS)
- **File terkait:**
  - Controller: `app/Http/Controllers/Mahasiswa/DashboardController.php`
  - Service Transkrip: `app/Services/AcademicTranscriptService.php`
  - View: `resources/views/mahasiswa/dashboard.blade.php`
- **Alur logika:**
  - `AcademicTranscriptService::summarize()` membaca seluruh `PesertaKelas` mahasiswa yang sudah memiliki `nilai_huruf`, mengonversi bobot huruf (`A=4.0`, `B=3.0`, `C=2.0`, `D=1.0`, `E=0.0`), lalu menghitung:
    - **IPK Kumulatif:** `total(bobot * sks) / total_sks_dinilai` (dibulatkan 2 desimal).
    - **SKS Kumulatif:** total SKS mata kuliah yang lulus (`nilai_huruf !== 'E'`).
    - **IPS per Semester:** pengelompokan nilai per `periode_akademik_id` untuk ditampilkan menjadi grafik batang **Perkembangan IPS per Semester (Skala 0.00 - 4.00)**.
  - Dashboard juga menampilkan SKS yang sedang diambil pada periode aktif dan status tagihan `BiayaKuliah` periode aktif.

#### 14. Modul Mahasiswa: Jadwal Kuliah, KHS, dan Biaya Kuliah
- **Jadwal Kuliah:** `app/Http/Controllers/Mahasiswa/JadwalController.php` + `resources/views/mahasiswa/jadwal.blade.php`
  - Mengambil seluruh `PesertaKelas` milik mahasiswa login yang kelasnya berada pada `PeriodeAkademik::currentActive()`, diurutkan menurut hari (`daySortOrder()`) dan `jam_mulai`.
- **Kartu Hasil Studi (KHS):** `app/Http/Controllers/Mahasiswa/KhsController.php` + `resources/views/mahasiswa/khs.blade.php`
  - Menampilkan seluruh riwayat mata kuliah yang dikelompokkan per semester (atau difilter lewat `?periode_akademik_id=`), menampilkan SKS, IPS per semester, nilai Tugas/UTS/UAS, Nilai Akhir, dan Nilai Huruf.
- **Biaya Kuliah:** `app/Http/Controllers/Mahasiswa/BiayaController.php` + `resources/views/mahasiswa/biaya.blade.php`
  - Menampilkan total nominal yang belum dibayar, total yang sudah lunas, serta daftar tagihan per periode milik mahasiswa login.

---

### Peta Automated Test Suite
Seluruh file test berada di `tests/` dan dapat dijalankan sekaligus dengan `php artisan test --compact`:

| File Test | Fokus Pengujian |
| --- | --- |
| `tests/Unit/GradingCalculatorTest.php` | Formula nilai akhir (`20/30/50`) dan seluruh nilai batas konversi huruf `A` sampai `E`. |
| `tests/Unit/AcademicTranscriptServiceTest.php` | Bobot nilai huruf, perhitungan IPK, SKS lulus vs dinilai, dan IPS per semester. |
| `tests/Feature/DatabaseSeederTest.php` | Verifikasi data awal `DatabaseSeeder` dan seluruh relasi antar 9 model Eloquent. |
| `tests/Feature/ExampleTest.php` | Redirect halaman root `/` ke `/login`. |
| `tests/Feature/AuthControllerTest.php` | Render login, validasi input, rate limit login 5x, redirect per role, dan logout. |
| `tests/Feature/RoleAndProfileMiddlewareTest.php` | Pemblokiran lintas role (`403`), redirect belum login, serta interceptor `CheckProfileComplete`. |
| `tests/Feature/AdminDashboardAndLayoutTest.php` | Statistik dashboard Admin, elemen sidebar/toggle ERP, dan proteksi XSS di Blade. |
| `tests/Feature/AdminMasterDataCrudTest.php` | CRUD Prodi, Matkul, Dosen, Mahasiswa, transaksi akun user, dan proteksi hapus data berelasi. |
| `tests/Feature/AdminSchedulingAndKeuanganTest.php` | Single-active Periode Akademik, CRUD Kelas, kelola Peserta Kelas (scoped), dan CRUD/toggle Keuangan. |
| `tests/Feature/DosenModuleTest.php` | Interceptor profil tanpa sidebar, upload foto, filter jadwal mengajar, tab JS `#tab-peserta`/`#tab-form-nilai`, isolasi kelas antar dosen (`404`), dan simpan nilai transaksional. |
| `tests/Feature/MahasiswaModuleTest.php` | Interceptor profil mahasiswa, dashboard IPK/SKS/IPS, filter jadwal periode aktif, filter KHS, dan isolasi tagihan mahasiswa. |
