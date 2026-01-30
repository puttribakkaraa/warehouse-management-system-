
# Warehouse Management System - Integrated Inventory Solution

Sistem manajemen inventaris berbasis web yang dirancang untuk mengotomatisasi pencatatan stok dan pelaporan keuangan operasional secara *real-time*. Proyek ini difokuskan pada transparansi arus barang dan kemudahan akses data stok bagi manajemen gudang.

## 📂 Dokumentasi Visual Sistem

### 1. Dashboard Monitoring & Keuangan

Menampilkan ringkasan total belanja, status pembayaran (Lunas/Pending), dan visualisasi data stok secara otomatis untuk membantu pengambilan keputusan yang cepat.

<img src="public/images/dashboard.jpeg" width="700" alt="Dashboard Utama">

### 2. Manajemen Arus Barang (In & Out)

Antarmuka pencatatan barang masuk per supplier dan pemakaian stok (barang keluar) yang mendetail untuk memastikan integritas data inventaris.

<img src="public/images/barangmasuk.jpeg" width="340" alt="Barang Masuk"> <img src="public/images/barangkeluar.jpeg" width="340" alt="Barang Keluar">

### 3. Monitoring Stok Akhir & Supplier

Tabel stok barang yang diperbarui secara *real-time* dan database supplier yang terintegrasi untuk memudahkan pemesanan kembali barang yang menipis.

<img src="public/images/stokbarang.jpeg" width="700" alt="Monitoring Stok">

### 4. Laporan Operasional Siap Cetak

Sistem pelaporan mingguan dan bulanan yang mencakup rincian transaksi lengkap dan siap diekspor ke format PDF/Excel untuk kebutuhan administrasi formal.

<img src="public/images/laporanbulanan.jpeg" width="700" alt="Laporan Bulanan">

---

## 🚀 Fitur Utama & Solusi Teknis

* **Real-Time Stock Tracking:** Pengurangan dan penambahan stok otomatis yang dikunci secara *back-end* untuk mencegah selisih data.
* **Financial Summary:** Pelacakan tagihan supplier untuk membedakan transaksi yang sudah dibayar (Lunas) dan yang masih tertunda (Pending).
* **Detailed Supplier Transactions:** Dokumentasi lengkap riwayat pengadaan barang dari setiap mitra penyuplai.
* **Multi-Format Export:** Kemampuan cetak laporan operasional ke dalam format PDF dan Excel dengan tata letak yang profesional.

---

## 🛠️ Teknologi (Tech Stack)

* **Framework:** Laravel (PHP).
* **Database:** MySQL (Manajemen relasi Stok, Supplier, dan Transaksi).
* **Frontend:** Bootstrap / AdminLTE Dashboard.
* **Reporting:** DomPDF & Laravel Excel (Maatwebsite).

---

## ⚙️ Instalasi Cepat

```bash
git clone https://github.com/puttribakkaraa/warehouse-management-system.git
cd warehouse-management-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

```

---

© 2026 Putri • WMS Project System


**Catatan:** Pastikan semua nama file gambar di dalam folder `public/images/` sudah sesuai dengan yang tertulis di kode di atas (seperti `dashboard.jpeg`, `stokbarang.jpeg`, dll).

Apakah ada bagian fitur spesifik lainnya yang ingin kamu tambahkan penjelasannya?
