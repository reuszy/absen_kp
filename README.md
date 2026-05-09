# Sistem Presensi / Absensi (Magang UGJ)

Sistem ini adalah aplikasi berbasis Laravel untuk mengelola presensi/absensi staf. Aplikasi ini terintegrasi langsung dengan mesin fingerprint (ZKTeco) untuk menarik log absensi secara otomatis maupun manual, menghitung keterlambatan, dan mengkalkulasi uang transport berdasarkan data kehadiran.

## 🚀 Fitur Utama
- **Dashboard & Log Kehadiran**: Menampilkan log absensi real-time.
- **Rekapitulasi (Recap)**: Menampilkan rekap harian, mingguan, dan bulanan dengan perhitungan otomatis untuk keterlambatan dan uang transport.
- **Export PDF**: Mengunduh laporan rekapitulasi kehadiran berdasarkan filter rentang tanggal, unit kerja (fakultas), dan tipe laporan.
- **Manajemen Staf**: CRUD data staf dan penjadwalan shift kerja.
- **Integrasi Mesin Absensi (ZKTeco)**: Terhubung ke mesin fingerprint via IP/VPN untuk menarik data kehadiran.

---

## 🛠️ Tech Stack & Libraries
Project ini dibangun dengan **Laravel 12.x** dan **PHP 8.2+**. Beberapa library utama yang digunakan meliputi:

### Backend (PHP/Laravel)
- **`jmrashed/zkteco`**: Digunakan untuk berkomunikasi dan menarik data log absensi dari mesin fingerprint ZKTeco melalui jaringan (IP/VPN & Port).
- **`barryvdh/laravel-dompdf`**: Digunakan untuk mengenerate export file PDF pada fitur Rekapitulasi.
- **`laravel/sanctum`**: Untuk autentikasi API (jika sewaktu-waktu dikembangkan untuk mobile/endpoint lain).

### Frontend
- **Vite & TailwindCSS v4**: Digunakan sebagai bundler dan framework CSS untuk mendesain UI/UX (Blade).
- **Axios**: HTTP Client untuk memanggil API secara asynchronous.

---

## 📂 Struktur File Penting
Berikut adalah letak file-file penting jika ingin melakukan modifikasi logika bisnis:

- **`app/Http/Controllers/AttendanceController.php`**
  Menangani logika untuk halaman Dashboard, Log Absensi, Rekapitulasi, dan Export PDF Laporan.
- **`app/Http/Controllers/Api/ScanController.php`**
  Menangani logika *core* penerimaan data scan. Di sini terdapat kalkulasi apakah staf terlambat, apakah berhak mendapat uang transport, dan pencatatan jam masuk & pulang.
- **`app/Http/Controllers/StafController.php`**
  Menangani master data staf.
- **`app/Console/Commands/TarikAbsen.php`**
  Command Line Interface (CLI) khusus yang dibuat untuk menarik data langsung dari mesin ZKTeco.
- **`app/Models/`**
  Terdapat model-model utama seperti `DailyAttendance` (Absensi Harian), `AttendanceLog` (Log Mentah), `Staff`, `FingerprintDevice`, `Faculty`, dan `WorkShift`.
- **`routes/web.php` & `routes/api.php`**
  Semua routing web dan API didaftarkan di sini.

---

## 💻 Cara Install & Penggunaan

1. **Clone & Install Dependencies**
   ```bash
   composer install
   npm install
   ```
2. **Setup Environment**
   Copy file `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```
   Lalu sesuaikan konfigurasi Database (`DB_DATABASE`, `DB_USERNAME`, dll).
3. **Generate Key & Migrate**
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
4. **Jalankan Aplikasi**
   Untuk menjalankan server backend & frontend (secara bersamaan jika disetting di composer scripts):
   ```bash
   composer run dev
   # Atau bisa dijalankan terpisah:
   # php artisan serve
   # npm run dev
   ```

---

## 🔄 Cara Get Data Absen (Logika Mesin Absensi)

Terdapat 2 cara utama data absensi masuk ke dalam database:

### 1. Via Command Line (Penarikan Langsung dari Mesin)
Anda dapat menarik data dari semua mesin fingerprint yang terdaftar di tabel `fingerprint_devices` dengan command berikut:
```bash
php artisan absen:tarik
```
**Atau untuk mesin spesifik:**
```bash
php artisan absen:tarik {device_id}
```
**Cara Kerjanya:**
1. Script `TarikAbsen.php` akan melakukan koneksi ke mesin (via IP VPN).
2. Script membaca log kehadiran mentah dari mesin.
3. Log tersebut akan diproses dan diteruskan secara otomatis ke `ScanController@store` agar diolah jam masuk, jam pulang, dan uang transportnya.

> **Rekomendasi:** Untuk ke depannya, tambahkan command `absen:tarik` ini ke dalam Cronjob server atau Laravel Scheduler (`routes/console.php`) agar sistem menarik data secara otomatis setiap 5 atau 10 menit tanpa harus dijalankan manual.

### 2. Via Endpoint API (`POST /api/scan`)
Jika mesin absensi mendukung fitur *Push Data* (ADMS) atau Anda menggunakan alat/script pihak ketiga untuk mengirim data, Anda bisa menembak API ini.

**Endpoint:** `POST /api/scan`  
**Payload (Form-Data / JSON):**
- `machine_id` (Wajib): ID Staf yang terdaftar di mesin (dan direlasi ke tabel `staff`).
- `scan_time` (Opsional): Waktu scan dengan format `Y-m-d H:i:s`. Jika kosong, akan menggunakan waktu sekarang.
- `verify_mode` (Opsional): Tipe verifikasi (misal: 1 untuk Fingerprint, dll).

**Cara Kerjanya:**
Endpoint ini akan mendeteksi apakah hari ini staf sudah absen masuk. 
- Jika belum: Akan dicatat sebagai jam masuk dan status (Tepat Waktu/Terlambat).
- Jika sudah masuk tapi belum pulang: Akan dicatat sebagai jam pulang, dan sistem akan mengkalkulasi apakah staf mendapat Uang Transport (jika pulang lebih dari batas yang ditentukan dan tidak terlambat saat masuk).

---

## 📝 Catatan Penting Untuk Pengembangan Selanjutnya
1. **Cronjob/Scheduler**: Segera setup Scheduler agar penarikan absen berjalan di belakang layar.
2. **Keamanan API**: Saat ini route `/api/scan` dapat diakses secara publik. Disarankan untuk menambahkan middleware token auth (misalnya bearer token statis atau Sanctum) jika API ini akan di-hit dari luar jaringan yang tidak terpercaya.
3. **Optimasi Rekap**: Proses query di `AttendanceController@rekap` bisa menjadi berat seiring bertambahnya data karena melooping dan memfilter array dalam PHP. Pertimbangkan untuk memindahkannya ke query database langsung (aggregasi SQL) untuk laporan yang sangat besar.
