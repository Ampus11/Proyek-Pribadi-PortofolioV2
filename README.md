# 🚀 Website Portofolio Pribadi (PHP & MySQL)

Website portofolio pribadi modern, responsif, dinamis, dan aman yang dibangun menggunakan bahasa pemrograman **PHP murni (Native OOP & PDO)** dengan basis data **MySQL**. Dilengkapi dengan antarmuka pengguna bergaya modern (Glassmorphism & Tailwind CSS) serta **Admin Dashboard** lengkap untuk mengelola konten dan pesan masuk.

---

## ✨ Fitur Utama

### 🌐 Frontend (Halaman Publik)
1. **Hero Section Interaktif**: Salam pembuka dinamis, nama, profesi, ringkasan bio, tombol Call-to-Action, metrik statistik (Tahun Pengalaman, Proyek Selesai, Klien Puas), dan tautan sosial media.
2. **Tentang Saya (About Me)**: Deskripsi profil mendalam, pilar kualitas (Keamanan, Responsif, Performa, Kemudahan Kelola), dan informasi kontak langsung.
3. **Keahlian (Skills & Tech Stack)**: Pengelompokan kategori (Frontend, Backend, Database & Tools) dengan indikator persentase kemahiran dan ikon modern.
4. **Portofolio Proyek (Projects)**: Filter kategori dinamis (Web App, E-Commerce, Landing Page, UI/UX, dll.), badge kategori, daftar teknologi, tombol repository GitHub, dan Live Demo.
5. **Perjalanan Karir & Pendidikan (Timeline)**: Riwayat pengalaman kerja dan rekam jejak akademis.
6. **Testimoni Klien**: Ulasan feedback klien dengan foto avatar dan rating bintang 1-5.
7. **Formulir Kontak (Contact Form)**: Form interaktif untuk calon klien mengirim pesan langsung yang tersimpan aman di database MySQL.
8. **Desain Ultra-Modern**: Glassmorphism, Tailwind CSS, dark-mode estetis, animasi transisi halus, dan tombol Back-to-Top otomatis.

### 🛡️ Backend & Admin Panel
1. **Autentikasi Aman**: Login berbasis PHP Session dan enkripsi password menggunakan algoritma **BCRYPT** (`password_hash` & `password_verify`).
2. **Dashboard Statistik**: Rangkuman total proyek, skill, testimoni, dan notifikasi pesan baru belum dibaca.
3. **CRUD Proyek**: Tambah proyek baru, edit, hapus, dan upload gambar thumbnail dari komputer atau via tautan URL.
4. **CRUD Keahlian (Skills)**: Tambah skill baru, ubah kategori, atur tingkat persentase kemahiran (1-100%), dan ikon FontAwesome.
5. **CRUD Testimoni**: Tambah review klien, kelola rating bintang, dan informasi perusahaan.
6. **Kotak Masuk Pesan (Inbox)**: Membaca pesan dari pengunjung web, tandai sudah/belum dibaca, balas email langsung via tombol cepat, dan hapus pesan.
7. **Pengaturan Profil (Settings)**: Mengubah nama lengkap, judul profesi, bio, kontak (Email, WhatsApp, Lokasi), statistik, tautan sosial media, dan ganti password admin.
8. **Keamanan Ekstra**: Proteksi SQL Injection menggunakan PDO Prepared Statements, proteksi XSS (`htmlspecialchars`), dan proteksi CSRF token.

---

## 📂 Struktur Direktori Proyek

```text
C:\laragon\www\portofolio/
├── admin/                     # Panel Administrator
│   ├── admin-header.php       # Layout header & sidebar admin
│   ├── admin-footer.php       # Layout footer & script admin
│   ├── index.php              # Dashboard admin & statistik
│   ├── login.php              # Halaman login administrator
│   ├── logout.php             # Script logout & penghancur session
│   ├── messages.php           # Kelola inbox pesan masuk
│   ├── projects.php           # CRUD portofolio proyek & upload gambar
│   ├── settings.php           # Pengaturan profil & ubah password
│   ├── skills.php             # CRUD keahlian & tech stack
│   └── testimonials.php       # CRUD review & testimoni klien
├── assets/
│   ├── css/
│   │   └── style.css          # Kustom styling glassmorphism & animasi
│   ├── js/
│   │   └── main.js            # Interaktivitas navbar, filter, & scroll
│   └── img/                   # Folder aset gambar lokal
├── config/
│   └── database.php           # Koneksi PDO & Auto-Migration MySQL
├── includes/
│   ├── functions.php          # Helper sanitasi, auth, CSRF, & format tanggal
│   ├── header.php             # Header navigasi publik
│   └── footer.php             # Footer publik & notifikasi SweetAlert2
├── uploads/                   # Folder penyimpanan upload gambar proyek
├── database.sql               # Skema database dan data awal
├── index.php                  # Halaman utama portofolio
├── setup.php                  # Halaman diagnostik & inisialisasi 1-klik
└── README.md                  # Dokumentasi panduan penggunaan
```

---

## 🚀 Cara Menjalankan Website

### Opsi 1: Menggunakan Laragon (Sangat Direkomendasikan)
Karena folder proyek ini sudah berada di `C:\laragon\www\portofolio`, Anda hanya perlu:
1. Buka aplikasi **Laragon** di komputer Anda.
2. Klik tombol **Start All** (untuk menjalankan Apache dan MySQL).
3. Buka browser (Google Chrome, Edge, dll.) dan kunjungi:
   - Website Publik: **`http://localhost/portofolio/`** atau **`http://portofolio.test`**
   - Admin Panel: **`http://localhost/portofolio/admin/login.php`**
   - Diagnostik / Setup: **`http://localhost/portofolio/setup.php`**

*Catatan: Sistem sudah memiliki fitur **Auto-Migration**! Saat Anda pertama kali membuka website, sistem akan otomatis membuat database `db_portofolio` dan seluruh tabel serta data bawaannya jika MySQL Laragon sedang aktif.*

---

### Opsi 2: Menggunakan PHP Built-in Server
Jika Anda ingin menjalankan langsung melalui terminal PowerShell:
```powershell
# Masuk ke folder proyek
cd C:\laragon\www\portofolio

# Jalankan server PHP bawaan Laragon
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" -S localhost:8080
```
Lalu buka browser di **`http://localhost:8080`**.

---

## 🔑 Kredensial Login Administrator

- **URL Login**: `http://localhost/portofolio/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

*(Password dapat diubah kapan saja setelah login melalui menu **Pengaturan Profil**).*

---

## 🛠️ Konfigurasi Database (Jika Ingin Diubah)

Pengaturan koneksi basis data berada di file [config/database.php](config/database.php):
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_portofolio');
```
Jika Anda menggunakan port MySQL khusus atau kata sandi root tertentu, cukup sesuaikan nilai konstanta di atas.
