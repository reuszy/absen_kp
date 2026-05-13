# Sistem Presensi / Absensi (Magang UGJ)

Sistem ini adalah aplikasi berbasis Laravel untuk mengelola presensi/absensi staf. Aplikasi ini terintegrasi langsung dengan mesin fingerprint (ZKTeco) untuk menarik log absensi secara otomatis maupun manual, menghitung keterlambatan, dan mengkalkulasi uang transport berdasarkan data kehadiran.

## 🚀 Fitur Utama
- **Dashboard & Log Kehadiran**: Menampilkan log absensi real-time beserta statistik harian.
- **Rekapitulasi (Recap)**: Menampilkan rekap harian, mingguan, dan bulanan dengan perhitungan otomatis untuk keterlambatan dan uang transport.
- **Export PDF & Excel**: Mengunduh laporan rekapitulasi kehadiran berdasarkan filter rentang tanggal, unit kerja (fakultas), dan tipe laporan.
- **Manajemen Staf & Izin**: CRUD data staf, shift kerja, dan pencatatan Sakit/Izin/Cuti/Dinas Luar.
- **Integrasi Mesin Absensi (ZKTeco)**: Terhubung ke mesin fingerprint via IP lokal atau VPN untuk menarik data kehadiran.

---

## 🛠️ Tech Stack & Libraries
Project ini dibangun dengan **Laravel 12.x** dan **PHP 8.2+**. Beberapa library utama yang digunakan meliputi:

### Backend (PHP/Laravel)
- **`jmrashed/zkteco`**: Digunakan untuk berkomunikasi dan menarik data log absensi dari mesin fingerprint ZKTeco melalui jaringan (IP/VPN & Port 4370).
- **`barryvdh/laravel-dompdf`**: Digunakan untuk mengenerate export file PDF pada fitur Rekapitulasi.
- **`maatwebsite/excel`**: Digunakan untuk export laporan ke format Excel.
- **`laravel/sanctum`**: Untuk autentikasi API.

### Frontend
- **Vite & TailwindCSS v4**: Digunakan sebagai bundler dan framework CSS untuk mendesain UI/UX (Blade).
- **Axios**: HTTP Client untuk memanggil API secara asynchronous.

---

## 📂 Struktur File Penting
Berikut adalah letak file-file penting jika ingin melakukan modifikasi logika bisnis:

- **`app/Http/Controllers/AttendanceController.php`** — Dashboard, Log Absensi, Rekapitulasi, dan Export Laporan.
- **`app/Http/Controllers/Api/ScanController.php`** — Logika inti penerimaan data scan: kalkulasi terlambat, uang transport, jam masuk & pulang.
- **`app/Http/Controllers/StafController.php`** — Master data staf.
- **`app/Console/Commands/TarikAbsen.php`** — CLI untuk menarik data langsung dari mesin ZKTeco.
- **`app/Models/`** — Model utama: `DailyAttendance`, `AttendanceLog`, `Staff`, `FingerprintDevice`, `Faculty`, `WorkShift`.
- **`routes/web.php` & `routes/api.php`** — Semua routing web dan API.
- **`routes/console.php`** — Konfigurasi Laravel Scheduler untuk auto-sync.

---

## 💻 Instalasi

### 1. Clone & Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
```
Edit file `.env` sesuai kebutuhan, khususnya untuk database:
```env
APP_NAME="Sistem Presensi UGJ"
APP_URL=http://localhost:8000

# Untuk produksi, gunakan MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi_ugj
DB_USERNAME=root
DB_PASSWORD=

# Untuk development, bisa gunakan SQLite (default):
# DB_CONNECTION=sqlite
```

### 3. Generate Key & Migrate
```bash
php artisan key:generate
php artisan migrate
```

### 4. Build Frontend & Jalankan Aplikasi
```bash
npm run build       # Untuk produksi

composer run dev    # Untuk development (jalankan semua service sekaligus)
# Atau satu per satu:
# php artisan serve
# npm run dev
# php artisan queue:listen --tries=1
```

---

## ⚙️ Setup Awal Setelah Instalasi

### Langkah 1 — Buat Akun Admin
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name'      => 'Administrator',
    'email'     => 'admin@ugj.ac.id',
    'password'  => bcrypt('password_anda'),
    'role'      => 'admin',
    'is_active' => true,
]);
```

### Langkah 2 — Konfigurasi Shift Kerja
Login sebagai admin → buka **Pengaturan** (`/settings`) → atur:
- Jam masuk dan jam pulang shift
- Nominal uang transport harian

### Langkah 3 — Tambah Fakultas/Unit Kerja
Buka **Pengaturan** → bagian Fakultas → tambahkan nama-nama unit/fakultas.

### Langkah 4 — Daftarkan Staf
Buka menu **Staf** (`/staf`) → klik **Tambah Staf** → isi:
- **NIP** (otomatis menjadi Machine ID — harus sama dengan User ID di mesin fingerprint)
- Nama, Jabatan, Fakultas, Shift Kerja

> **Penting:** NIP staf di sistem harus sama persis dengan ID pengguna (User ID) yang sudah di-*enroll* di mesin ZKTeco. Inilah yang menghubungkan data scan dari mesin ke data staf di sistem.

### Langkah 5 — Daftarkan Mesin Fingerprint
Buka **Pengaturan** → bagian Perangkat Fingerprint → tambah perangkat dengan isian:

| Field | Keterangan |
|-------|------------|
| Nama Lokasi | Label mesin, misal "Gedung Rektorat" |
| IP | Alamat IP lokal mesin (jika server dan mesin satu jaringan) |
| VPN | Alamat IP VPN mesin (untuk akses dari luar jaringan lokal) |
| Port | Port komunikasi ZKTeco — default **4370** |

> Jika mesin dan server berada di jaringan lokal yang sama, isi kolom **IP** dan **VPN** dengan alamat IP yang sama. Sistem menggunakan kolom **VPN** saat koneksi.

---

## 🔄 Cara Menarik Data Absen dari Mesin Fingerprint

### Cara 1 — Via Command Line (Tarik Manual)

```bash
# Tarik dari semua mesin yang terdaftar
php artisan absen:tarik

# Tarik dari mesin tertentu (gunakan ID dari tabel fingerprint_devices)
php artisan absen:tarik 1
```

**Cara kerjanya:**
1. Sistem konek ke mesin via alamat VPN dan port yang terdaftar di database.
2. Membaca semua log kehadiran dari memori mesin.
3. Log diurutkan berdasarkan timestamp, lalu diproses satu per satu:
   - **Scan pertama** dalam sehari → dicatat sebagai jam masuk, status dihitung (Hadir/Terlambat).
   - **Scan kedua** dalam sehari → dicatat sebagai jam pulang, uang transport dikalkulasi.
4. Log yang sudah ada di database tidak akan diproses ulang (cek duplikat).

> **Catatan Filter Tanggal:** Command ini memiliki filter tanggal hardcoded di `app/Console/Commands/TarikAbsen.php` baris 22–23. Ubah variabel `$filterStartDate` dan `$filterEndDate` sesuai periode data yang ingin ditarik.

**Contoh output sukses:**
```
Memproses: Gedung Rektorat (192.168.1.100)
 100/100 [============================] 100%
Semua Proses Selesai!
```

**Contoh output gagal koneksi:**
```
Gagal konek ke Gedung Rektorat. Skip.
```

---

### Cara 2 — Via Scheduler Otomatis (Auto-Sync)

Untuk menarik data secara otomatis setiap 5 menit tanpa harus menjalankan command manual:

**Langkah 1:** Aktifkan auto-sync di `routes/console.php`:
```php
$autoSyncEnabled = true;  // Ubah dari false ke true
```

**Langkah 2:** Tambahkan cron job di server (Linux/macOS):
```bash
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

**Untuk Windows**, buat Scheduled Task yang menjalankan:
```
php C:\path\ke\project\artisan schedule:run
```
setiap 1 menit.

---

### Cara 3 — Via Endpoint API (Push dari Mesin)

Jika mesin mendukung mode Push (ADMS) atau menggunakan script pihak ketiga:

**Endpoint:** `POST /api/scan`

**Payload JSON:**
```json
{
  "machine_id": "123456",
  "scan_time": "2026-05-13 08:30:00",
  "verify_mode": 1
}
```

| Parameter | Keterangan |
|-----------|------------|
| `machine_id` | ID pengguna di mesin fingerprint (= NIP staf) — wajib |
| `scan_time` | Waktu scan format `Y-m-d H:i:s` — opsional, default waktu sekarang |
| `verify_mode` | Tipe verifikasi: 1=Fingerprint — opsional |

**Response:**
- Scan pertama (masuk): `{"status": "IN", ...}`
- Scan kedua (pulang): `{"status": "OUT", ...}`
- Staf tidak ditemukan: `404`

---

## 🔌 Koneksi VPN ke Mesin ZKTeco

Jika mesin fingerprint berada di jaringan berbeda (gedung terpisah, koneksi remote):

1. Pastikan server dan mesin terhubung ke jaringan VPN yang sama.
2. Dapatkan alamat IP VPN mesin dari administrator jaringan.
3. Daftarkan IP VPN tersebut di field **VPN** pada data perangkat fingerprint di menu Pengaturan.
4. Port default ZKTeco adalah **4370** (protokol UDP over TCP/IP).
5. Pastikan firewall mengizinkan koneksi masuk ke port 4370 pada mesin.

**Test koneksi:**
```bash
php artisan absen:tarik {device_id}
```
Jika berhasil, terminal akan menampilkan nama lokasi mesin dan progress bar pengunduhan data.

---

## 💰 Logika Uang Transport

Uang transport diberikan **hanya jika semua kondisi berikut terpenuhi**:

| Kondisi | Aturan |
|---------|--------|
| Status kehadiran | Harus "Hadir" (tidak terlambat) |
| Jam pulang | Harus >= jam pulang shift |
| Jam pulang | Harus <= 18:00:00 |

Jika salah satu kondisi tidak terpenuhi, `uang_transport = 0`. Logika ini ada di `app/Http/Controllers/Api/ScanController.php`.

---

## 👥 Hak Akses per Role

| Fitur | Admin | Staff |
|-------|:-----:|:-----:|
| Dashboard | ✓ | ✓ |
| Log absensi | ✓ | ✓ |
| Rekap & ekspor PDF/Excel | ✓ | ✓ |
| Tambah / edit staf | ✓ | ✓ |
| Hapus staf | ✓ | ✗ |
| Manajemen izin/cuti | ✓ | ✓ |
| Pengaturan sistem & shift | ✓ | ✗ |
| Manajemen user | ✓ | ✗ |

---

## 🐛 Troubleshooting

**Gagal konek ke mesin fingerprint**
- Pastikan IP/VPN benar dan mesin dalam keadaan menyala
- Pastikan port 4370 tidak diblokir firewall di mesin maupun di server
- Coba ping ke IP mesin dari server: `ping 192.168.x.x`

**NIP staf tidak terdeteksi saat tarik absen**
- Pastikan NIP staf di sistem sama persis dengan User ID yang di-enroll di mesin ZKTeco
- Cek kolom `machine_id` di tabel `staff`

**Data absensi tidak muncul setelah `absen:tarik`**
- Cek filter tanggal di `app/Console/Commands/TarikAbsen.php` baris 22–23
- Pastikan tanggal log berada dalam rentang `$filterStartDate` dan `$filterEndDate`

**Uang transport bernilai 0 padahal staf hadir**
- Cek apakah staf terlambat (status "Terlambat") — terlambat = tidak dapat transport
- Cek apakah jam pulang sebelum jam akhir shift
- Cek apakah jam pulang sesudah 18:00

---

## 📝 Catatan Penting Untuk Pengembangan Selanjutnya
1. **Keamanan API**: Route `/api/scan` saat ini dapat diakses secara publik tanpa autentikasi. Tambahkan middleware bearer token atau Sanctum sebelum digunakan di jaringan yang tidak terpercaya.
2. **Filter Tanggal TarikAbsen**: Ubah `$filterStartDate` dan `$filterEndDate` di `TarikAbsen.php` menjadi dinamis (misal: ambil dari database atau argumen CLI) agar tidak perlu diubah secara hardcoded setiap periode.
3. **Optimasi Rekap**: Proses di `AttendanceController@rekap` memfilter array besar di PHP. Untuk dataset dengan ribuan staf, pertimbangkan aggregasi SQL (GROUP BY, SUM) untuk performa yang lebih baik.
