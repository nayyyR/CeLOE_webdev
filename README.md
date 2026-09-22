# Celoe — Sistem Tiketing

**Nama**: Nayottama Lucky Mustafa  
**Program Studi**: Informatika 

| Folder | Tugas | Teknologi |
|-----------|-------|-----------|
| `laravel/` | **Tugas 1** — Backend & Web (sistem tiketing) | Laravel, Blade, Tailwind CSS, MySQL, MongoDB |
| `vue/` | **Tugas 2** — Frontend antarmuka web interaktif | Vue.js, Vite, Pinia, vue-router, Tailwind CSS |

---

## Tentang Proyek

Pengembangan modul sistem **tiketing** yang menerapkan arsitektur **multi-connection database** (MySQL + MongoDB) serta penerapan otorisasi berbasis peran
(RBAC). Terdapat dua bagian utama:

1. **Tugas 1 — Backend & Web Laravel**: Prototipe modul sistem tiketing berbasis Laravel (Blade) yang mengimplementasikan arsitektur multi-connection database.
2. **Tugas 2 — Frontend Vue.js & Tailwind CSS**: Aplikasi antarmuka web interaktif berbasis Vue.js yang dikembangkan sebagai simulasi/UI dari sistem tiketing.

---

## Tugas 1: Backend & Web Laravel

### Deskripsi

Web tiketing berbasis **Laravel** dengan UI **Blade + Tailwind CSS** dan autentikasi berbasis **session**.

| Data | Database | Keterangan |
|------|----------|------------|
| `users`, `roles`, `divisions`, `permissions`, `tickets` | **MySQL** | Skema dibuat melalui migration |
| `ticket_threads`, `activity_logs` | **MongoDB** | Diakses lewat paket `mongodb/laravel-mongodb`, tidak menggunakan migration |

RBAC dikelola lewat empat peran (**Super Admin**, **Admin**, **Employee**, **User**) dan divisi (**IT**, **akademik**, **general**), dengan middleware kustom `role`, `role.division`, dan `permission`.

### Persyaratan Sistem

- PHP **8.3+** dan Composer
- MySQL (dengan database baru)
- MongoDB (berjalan lokal pada `127.0.0.1:27017`)
- Node.js + npm (untuk membangun aset frontend Blade via Vite)

### Panduan Instalasi

Buka terminal pada direktori `laravel/`:

```bash
cd laravel

# 1. Pasang dependensi PHP
composer install

# 2. Salin file .env contoh
# Windows (PowerShell):
copy .env.example .env
# Linux/macOS:
# cp .env.example .env

# 3. Generate application key
php artisan key:generate
```

### Konfigurasi `.env` (MySQL + MongoDB)

Buka file `.env` dan sesuaikan koneksi **MySQL** untuk tabel relasional:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=celoe
DB_USERNAME=root
DB_PASSWORD=your_password
```

Kemudian aktifkan koneksi **MongoDB** untuk `ticket_threads` dan `activity_logs` dengan membuka bagian variabel MongoDB (hapus tanda `#` di depan baris tersebut):

```env
MONGO_DB_CONNECTION=mongodb
MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE=ticketing
```

> **Catatan penting:**
> - Pastikan layanan **MySQL** dan **MongoDB** sudah berjalan sebelum menjalankan aplikasi.
> - `MONGO_DB_DATABASE` default pada `config/database.php` adalah `ticketing` bila tidak
>   diisi. Koleksi MongoDB dibuat otomatis saat data pertama kali ditulis (tidak ada migration).
> - Skema tabel MySQL dibuat melalui **migration**, bukan dibuat manual di phpMyAdmin/UI.

### Migrasi & Seed Data

```bash
# Buat tabel di MySQL + isi data awal (roles, divisions, user, permission)
php artisan migrate --seed
```

Seeder membuat **akun login bawaan** dengan password `password`:

| Username | Peran | Divisi |
|----------|-------|--------|
| `superadmin` | Super Admin | general |
| `admin` | Admin | general |
| `employee_it` | Employee | IT |
| `employee_akademik` | Employee | akademik |
| `user` | User | general |

### Membangun Aset Frontend (Blade)

```bash
# Pasang dependensi Node untuk pipeline Vite Laravel
npm install

# Build aset untuk produksi (menghasilkan public/build)
npm run build

# ATAU gunakan dev server hot-reload saat pengembangan
npm run dev
```

### Menjalankan Aplikasi

```bash
# Vite + server
composer run dev
```

Akses aplikasi di `http://localhost:8000`.

---

### Alur Kerja Sistem Tiketing Laravel

Sistem tiketing memisahkan akses berdasarkan **peran + divisi** (RBAC). Untuk dapat masuk ke
dashboard tertentu, kombinasi peran–divisi harus valid:

| Peran | Divisi yang Diizinkan |
|-------|-----------------------|
| `User` | `general` |
| `Employee` | selain `general` |
| `Admin` | `general` |
| `Super Admin` | `general` |

Setelah login, pengguna dialihkan otomatis ke dashboard sesuai kombinasi tersebut (logika pada `routes/web.php`).

Siklus hidup tiket (dikendalikan di `app/Services/TicketService.php`):

1. **User membuat tiket** — tiket dibuat berstatus `open` dengan `target_division_id`
   (divisi yang dituju) dan tiket pertama (thread) langsung dibuat berisi deskripsi awal.
2. **Admin/Super Admin menetapkan tiket** (*assign*) — hanya tiket berstatus `open` yang
   dapat ditetapkan, dan hanya ke **Employee di divisi yang sama** dengan target tiket.
   Status berubah menjadi `in_progress`.
3. **Employee menyelesaikan tiket** — Employee yang ditugaskan dapat mengubah tiket dari
   `in_progress` menjadi `resolved`. Perpindahan status lain **ditolak**.
4. **User menutup tiket** — pembuat tiket dapat menutup tiket `resolved` menjadi `closed`,
   atau membuka kembali ke `in_progress`. Kombinasi perpindahan status lain **ditolak**.
5. **Admin/Super Admin** memiliki wewenang penuh untuk mengubah status tiket.

Semua percakapan (thread) dan lampiran (attachment) berjalan lewat model MongoDB
(`ticket_threads`), dan **setiap aksi penting** (buat, tetapkan, ubah status, tambah thread)
tercatat sebagai riwayat pada koleksi MongoDB `activity_logs`.

---

## Tugas 2: Frontend Vue.js & Tailwind CSS

### Deskripsi

Aplikasi antarmuka web interaktif berbasis **Vue.js + Vite** yang berdiri sendiri
(standalone SPA) menggunakan **Pinia** (state management) dan **vue-router**.
Seluruh data yang ditampilkan merupakan **data simulasi** yang didefinisikan di dalam store,
sehingga aplikasi **tidak terhubung ke backend Laravel** dan tidak melakukan panggilan API.

### Persyaratan Sistem

- Node.js **`^22.18.0`** atau **`>=24.12.0`** (dicentumkan pada field `engines` di `package.json`)
- npm

### Panduan Instalasi

Buka terminal pada direktori `vue/`:

```bash
cd vue

# 1. Pasang dependensi
npm install

# 2. Jalankan dev server
npm run dev
```

### Struktur Aplikasi

```
vue/src/
├── main.js                 # Entry point (app + Pinia + router)
├── App.vue                 # Layout utama (AppDashboardLayout)
├── router/index.js         # Definisi route
├── stores/ticketStore.js   # Store Pinia: seluruh data simulasi & logika
├── views/                  # Halaman: Dashboard, Board (tiket), Activity, Users, Profile
├── components/
│   └── layout/             # AppSidebar, AppTopBar, AppDashboardLayout, AppPagination
└── composables/            # usePagination, dll.
```

### Alur Kerja Aplikasi Vue.js

1. **Routing & Layout** — Setiap halaman dirender di dalam `AppDashboardLayout` yang memuat
   `AppSidebar`, `AppTopBar`, dan area konten.
2. **State (Pinia store)** — `ticketStore.js` menyimpan data simulasi: `roles`, `divisions`,
   `users`, `tickets`, `activityLogs`, dan `currentUser`. Seluruh interaksi mengubah state
   reaktif tanpa berkomunikasi dengan server.
3. **Akses berbasis peran** — Store mengekspos nilai komputasi seperti `isAdmin`,
   `canCreateTickets`, dan `canAssignTickets`, serta `visibleTickets` yang menyaring tiket
   berdasarkan peran pengguna saat ini (admin melihat semua, employee melihat tiket yang
   ditugaskan ke dirinya, user melihat tiket yang ia buat).
4. **Halaman Dashboard** — Menampilkan kartu statistik (total, open, in_progress, resolved,
   closed), grafik garis tren tiket 7 hari terakhir, serta grafik status/divisi/prioritas
   menggunakan `chart.js`.
5. **Halaman Board (tiket)** — Daftar tiket dengan **pencarian** (judul/nomor tiket) dan
   **filter** (prioritas, status, divisi), tampilan desktop + mobile, `assign`, pemindahan
   status, serta modal pembuatan tiket.
6. **Halaman Activity** — Riwayat aktivitas tiket dengan tampilan tabel desktop dan list mobile
   yang dilengkapi pagination.
7. **Halaman Users & Profile** — Pengelolaan daftar pengguna (filter peran/divisi) dan
   pembaruan profil/password pengguna saat ini.

---

## Teknologi yang Digunakan

| Teknologi | Penggunaan |
|-----------|------------|
| Laravel 13 / PHP 8.3 | Backend utama (Tugas 1) |
| Blade + Tailwind CSS | UI aplikasi Laravel (Tugas 1) |
| MySQL | Tabel relasional (Tugas 1) |
| MongoDB (`mongodb/laravel-mongodb`) | Thread & activity log (Tugas 1) |
| Pest / PHPUnit | Pengujian (Tugas 1) |
| Vue.js 3 + Vite | Frontend standalone (Tugas 2) |
| Pinia | State management (Tugas 2) |
| vue-router | Routing (Tugas 2) |
| Tailwind CSS 4 | Styling (Tugas 2) |