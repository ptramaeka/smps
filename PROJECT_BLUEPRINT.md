Berikut terjemahan lengkap ke Bahasa Indonesia:

---

# Project Blueprint

## 0. Aturan Dokumentasi
Setiap perubahan, penambahan, pengurangan, atau aksi apa pun dalam proyek ini wajib didokumentasikan di dalam file markdown.
- Utamakan `PROJECT_BLUEPRINT.md` sebagai sumber utama dokumentasi arsitektur dan perencanaan.
- Tambahkan ringkasan perubahan setiap kali melakukan modifikasi.
- AI dan alat otomatis harus membaca `PROJECT_RULES.md` agar memahami dan mematuhi aturan ini.

## 1. Gambaran Proyek

### Tujuan Sistem

Sistem manajemen disiplin sekolah untuk mencatat pelanggaran siswa, mengelola data guru dan siswa, serta menghasilkan laporan dan dokumen yang dapat dicetak.

### Masalah yang Diselesaikan

* Memusatkan data pelanggaran siswa
* Mengotomatisasi pembuatan laporan seperti surat panggilan, perjanjian, dan surat pindah
* Menyediakan akses berbasis peran untuk staf dan guru
* Melacak akumulasi poin pelanggaran dan rekapitulasi

### Pengguna Utama

* Admin / staf sekolah
* Guru (`guru`)
* Siswa (`siswa`)

---

## 2. Arsitektur Saat Ini

### Teknologi (Stack)

* Backend PHP prosedural
* Database MySQL menggunakan `mysqli`
* Halaman HTML yang dirender di server
* CSS statis di `assets/css/style.css`
* Autentikasi berbasis cookie

### Komponen Inti

* `config/config.php`: koneksi database dan konstanta `BASE_URL`
* `login.php` + `process/login_process.php`: autentikasi
* `includes/header.php`: layout dan pembatasan akses (role guard)
* `pages/dashboard.php`: dashboard KPI
* `pages/*`: halaman CRUD dan laporan
* `process/*`: handler form untuk insert/update/delete

### Alur Data

1. Pengguna mengakses `login.php`
2. Kredensial dikirim ke `process/login_process.php`
3. Sistem memvalidasi terhadap tabel `guru` atau `siswa`
4. Cookie diset untuk `nama`, `username`, `role`
5. Pengguna diarahkan ke `pages/dashboard.php`
6. Navigasi membuka halaman di `pages/`
7. Form dikirim ke handler di `process/*`
8. Handler memperbarui database lalu redirect kembali
9. Halaman laporan menampilkan data hasil join

---

## 3. Arsitektur yang Direkomendasikan

### Lapisan Utama

* Frontend: template reusable, CSS, navigasi
* Backend: controller, service, layer akses database
* Database: skema relasional yang ternormalisasi
* Keamanan: autentikasi berbasis session, RBAC, validasi input

### Struktur Folder yang Disarankan

* `config/`

  * `config.php`
* `public/`

  * `index.php`
  * `login.php`
  * `logout.php`
* `src/`

  * `controllers/`
  * `models/`
  * `views/`
  * `services/`
  * `middleware/`
* `pages/` (legacy atau template view)
* `assets/`
* `templates/`
* `tests/`

---

## 4. Rincian Fitur

### Fitur Prioritas Tinggi

1. Autentikasi

   * Login/logout yang aman
   * Pengenalan role (`admin`, `guru`, `siswa`)
   * Redirect ke dashboard

2. Dashboard

   * Total siswa, guru, pelanggaran, poin
   * Aktivitas pelanggaran terbaru

3. Manajemen Siswa

   * Tambah/edit/hapus data siswa
   * Data orang tua/wali
   * Penempatan kelas

4. Input Pelanggaran

   * Mencatat pelanggaran siswa
   * Mengaitkan jenis pelanggaran dan poin

5. Laporan & Cetak

   * Riwayat pelanggaran
   * Surat panggilan orang tua
   * Surat perjanjian
   * Surat pindah
   * Rekapitulasi

### Fitur Prioritas Menengah

6. Manajemen Guru

   * Mengelola akun dan peran guru

7. Manajemen Jenis Pelanggaran

   * Menentukan kategori dan poin pelanggaran

8. Manajemen Kelas

   * Mengelola kelas, program, dan tingkat

### Fitur Prioritas Rendah

9. Pembuatan surat pindah
10. Pembuatan dokumen perjanjian orang tua

---

## 5. User Stories

* Sebagai admin, saya ingin login dengan aman agar bisa mengelola data disiplin.
* Sebagai guru, saya ingin mencatat pelanggaran siswa agar sekolah dapat memantau perilaku.
* Sebagai admin, saya ingin melihat ringkasan pelanggaran siswa untuk mengidentifikasi siswa berisiko.
* Sebagai staf, saya ingin menambah atau memperbarui data guru agar penugasan tetap akurat.
* Sebagai admin, saya ingin menambahkan kategori pelanggaran agar perhitungan poin akurat.
* Sebagai pengguna, saya ingin mencetak surat panggilan orang tua dengan cepat.
* Sebagai staf, saya ingin menghapus data siswa lama dengan aman agar database tetap bersih.
* Sebagai admin, saya ingin sistem berbasis role agar hanya pengguna tertentu yang bisa mengakses fitur sensitif.

---

## 6. Desain Database

### Tabel

* `guru`
* `siswa`
* `ortu_wali`
* `kelas`
* `tingkat`
* `program_keahlian`
* `jenis_pelanggaran`
* `pelanggaran_siswa`
* `perjanjian_siswa`
* `perjanjian_orang_tua`
* `surat_keluar`
* `surat_pindah`

(Struktur kolom tetap sama seperti versi asli)

### Relasi

* `siswa.id_ortu_wali` → `ortu_wali.id_ortu_wali`
* `siswa.id_kelas` → `kelas.id_kelas`
* `kelas.id_tingkat` → `tingkat.id_tingkat`
* `kelas.id_program_keahlian` → `program_keahlian.id_program_keahlian`
* `kelas.kode_guru` → `guru.kode_guru`
* `pelanggaran_siswa.nis` → `siswa.nis`
* `pelanggaran_siswa.id_jenis_pelanggaran` → `jenis_pelanggaran.id_jenis_pelanggaran`
* `perjanjian_*` → `pelanggaran_siswa.id_pelanggaran_siswa`
* `surat_keluar.nis` → `siswa.nis`

### Optimasi

* Tambahkan index pada kolom yang sering difilter
* Gunakan foreign key dengan InnoDB
* Normalisasi data untuk menghindari duplikasi

---

## 7. Desain API

### Endpoint yang Disarankan

* `POST /auth/login` → autentikasi user

* `POST /auth/logout` → logout

* `GET /dashboard` → data ringkasan

* `GET /teachers` → daftar guru

* `POST /teachers` → tambah guru

* `PUT /teachers/{kode_guru}` → update guru

* `DELETE /teachers/{kode_guru}` → hapus/nonaktifkan

* `GET /students` → daftar siswa

* `POST /students` → tambah siswa

* `PUT /students/{nis}` → update siswa

* `DELETE /students/{nis}` → hapus siswa

* `GET /violation-types` → daftar jenis pelanggaran

* `POST /violation-types` → tambah

* `PUT /violation-types/{id}` → update

* `DELETE /violation-types/{id}` → hapus

* `GET /violations` → riwayat pelanggaran

* `POST /violations` → tambah pelanggaran

* Endpoint laporan:

  * `/reports/violations`
  * `/reports/parent-summons`
  * `/reports/agreements`
  * `/reports/transfers`
  * `/reports/recap`

---

## 8. Analisis Kode

### Masalah yang Ditemukan

* Rentan SQL Injection (query langsung)
* Autentikasi berbasis cookie tidak aman
* Path/URL tidak konsisten
* Logika tampilan dan bisnis tercampur
* Tidak ada CSRF dan validasi input
* Banyak kode berulang
* Redirect menggunakan path hardcoded

### Rekomendasi Perbaikan

* Sentralisasi akses database dan autentikasi
* Gunakan prepared statements
* Pisahkan template layout
* Gunakan middleware RBAC berbasis session
* Validasi dan sanitasi input
* Gunakan service untuk logika reusable

---

## 9. Rencana Implementasi

### Fase 1: Setup

1. Standarisasi `config.php`
2. Buat wrapper database
3. Buat sistem auth/session
4. Sentralisasi template

### Fase 2: Fitur Inti

1. Login/logout aman
2. Implementasi RBAC
3. Refactor CRUD ke controller/service
4. Pertahankan fungsi lama

### Fase 3: Peningkatan

1. Tambah fitur pencarian & pagination
2. Standarisasi laporan
3. UI konsisten
4. Fitur profil & reset password

### Fase 4: Optimasi

1. Tambah index & foreign key
2. Cache dashboard jika perlu
3. Optimasi query laporan
4. Logging error

### Fase 5: Deployment

1. Gunakan environment variable
2. Setup server (Apache/Nginx, PHP 8.x, MySQL)
3. Aktifkan HTTPS
4. Dokumentasi deployment

---

## 10. Analisis Risiko

### Risiko

* SQL injection & kebocoran data
* Bypass role karena autentikasi lemah
* Redirect error akibat URL hardcoded
* Inkonsistensi data saat delete
* Performa buruk pada laporan

### Mitigasi

* Gunakan prepared statements
* Terapkan RBAC berbasis session
* Gunakan `BASE_URL`
* Gunakan soft delete / cascade
* Tambahkan index & pagination

---

## 11. Strategi Peningkatan

### Keamanan

* Gunakan session PHP
* Cookie `HttpOnly`, `Secure`, `SameSite`
* Tambahkan CSRF protection
* Sembunyikan error database

### Skalabilitas

* Normalisasi database
* Pagination
* Modularisasi kode

### UI/UX

* Desain konsisten
* Responsif mobile
* Notifikasi sukses/error
* Navigasi jelas

### Performa

* Optimasi query
* Kurangi query berulang
* Tambahkan index
* Gunakan caching untuk dashboard jika perlu

---

Kalau kamu mau, saya bisa lanjut bantu:

* menyederhanakan blueprint ini jadi roadmap coding harian
* atau langsung ubah jadi struktur project siap pakai di PHP (semi-Laravel style tanpa framework)

---

## Catatan Perubahan

### 2026-04-06
Ringkasan: Menambahkan redirect awal ke halaman login jika user belum login.
Alasan: Agar akses pertama dari browser selalu diarahkan ke login saat belum terautentikasi.
File diubah:
* `index.php`
