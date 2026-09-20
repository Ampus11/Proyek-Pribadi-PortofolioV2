-- Database Schema for Portfolio Website
-- Compatible with MySQL 8.x & MariaDB

CREATE DATABASE IF NOT EXISTS `db_portofolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_portofolio`;

-- 1. Table: users (Admin accounts)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user: username 'admin', password 'admin123'
INSERT INTO `users` (`id`, `username`, `password`, `full_name`) VALUES
(1, 'admin', '$2y$10$BKh9eAMVMQ/jFNaFYI5Mm.kbnYV7CBxnudx5lxAtVeT9r52idtFNa', 'Rizky Pratama')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 2. Table: profile_settings
CREATE TABLE IF NOT EXISTS `profile_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profile_settings` (`setting_key`, `setting_value`) VALUES
('full_name', 'Rizky Pratama'),
('job_title', 'Full Stack Web Developer & UI/UX Enthusiast'),
('hero_greeting', 'Halo, Saya Rizky 👋'),
('bio_summary', 'Pengembang web yang berdedikasi menciptakan aplikasi web modern, cepat, responsif, dan user-friendly menggunakan PHP, MySQL, JavaScript, dan teknologi modern lainnya.'),
('about_description', 'Saya adalah seorang Full Stack Web Developer dengan pengalaman dalam merancang dan mengembangkan website dari nol hingga production. Berfokus pada arsitektur kode yang rapi (clean code), keamanan data, performa tinggi, dan antarmuka pengguna yang memikat.'),
('email', 'rizky.pratama@example.com'),
('phone', '+62 812-3456-7890'),
('location', 'Jakarta, Indonesia'),
('years_experience', '3+'),
('completed_projects', '28+'),
('happy_clients', '20+'),
('github_url', 'https://github.com'),
('linkedin_url', 'https://linkedin.com'),
('instagram_url', 'https://instagram.com'),
('resume_url', '#')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

-- 3. Table: skills
CREATE TABLE IF NOT EXISTS `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL, -- 'Frontend', 'Backend', 'Database & Tools'
    `proficiency` INT NOT NULL DEFAULT 85,
    `icon_class` VARCHAR(100) DEFAULT 'fa-code',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skills` (`name`, `category`, `proficiency`, `icon_class`) VALUES
('HTML5 & CSS3', 'Frontend', 95, 'fa-brands fa-html5'),
('JavaScript (ES6+)', 'Frontend', 88, 'fa-brands fa-js'),
('Tailwind CSS & Bootstrap', 'Frontend', 90, 'fa-solid fa-wind'),
('Vue.js / Alpine.js', 'Frontend', 80, 'fa-brands fa-vuejs'),
('PHP (Modern & OOP)', 'Backend', 92, 'fa-brands fa-php'),
('MySQL / MariaDB', 'Backend', 88, 'fa-solid fa-database'),
('RESTful API & Integration', 'Backend', 85, 'fa-solid fa-network-wired'),
('Laravel Framework', 'Backend', 82, 'fa-brands fa-laravel'),
('Git & GitHub', 'Database & Tools', 89, 'fa-brands fa-git-alt'),
('Laragon & Apache/Nginx', 'Database & Tools', 87, 'fa-solid fa-server'),
('Figma UI/UX Design', 'Database & Tools', 78, 'fa-brands fa-figma'),
('Docker Basics', 'Database & Tools', 72, 'fa-brands fa-docker');

-- 4. Table: projects
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `technologies` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `demo_url` VARCHAR(255) DEFAULT '#',
    `github_url` VARCHAR(255) DEFAULT '#',
    `is_featured` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `projects` (`title`, `category`, `description`, `technologies`, `image_url`, `demo_url`, `github_url`, `is_featured`) VALUES
('Sistem Informasi Manajemen Apotek (SIM-Apotek)', 'Web App', 'Aplikasi berbasis web untuk pencatatan stok obat, transaksi kasir POS, laporan penjualan, dan manajemen resep pasien dengan print struk otomatis.', 'PHP, MySQL, Tailwind CSS, Chart.js', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1),
('E-Commerce Fashion Store "GayaKoe"', 'E-Commerce', 'Platform toko online lengkap dengan fitur keranjang belanja, integrasi payment gateway, manajemen pesanan, dan dashboard analitik admin.', 'PHP, MySQL, Bootstrap 5, Midtrans API', 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1),
('Portal Berita & Publikasi Digital "KabarNusantara"', 'Web App', 'Content Management System (CMS) berita dengan editor WYSIWYG, kategori berlapis, komentar pembaca terverifikasi, dan SEO-friendly URLs.', 'PHP Native OOP, MySQL, Tailwind CSS', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1),
('Landing Page Startup FinTech "CuanKu"', 'Landing Page', 'Website profil interaktif dengan animasi modern, simulasi kalkulator pinjaman/investasi, dan form registrasi leads.', 'HTML5, CSS3, JavaScript, PHP Mailer', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1),
('Sistem Presensi Karyawan & Penggajian', 'Web App', 'Sistem absensi berbasis QR Code & Geolocation GPS untuk karyawan serta modul payroll otomatis dengan slip gaji PDF.', 'PHP, MySQL, Leaflet JS, DomPDF', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1),
('Redesign UI/UX Mobile Banking App', 'UI/UX', 'Studi kasus perancangan ulang antarmuka aplikasi perbankan digital untuk meningkatkan kepuasan pengguna dan mempermudah alur transaksi.', 'Figma, Adobe Illustrator, Prototyping', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&auto=format&fit=crop&q=80', '#', 'https://github.com', 1);

-- 5. Table: experiences
CREATE TABLE IF NOT EXISTS `experiences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `organization` VARCHAR(150) NOT NULL,
    `period` VARCHAR(50) NOT NULL,
    `type` ENUM('work', 'education') DEFAULT 'work',
    `description` TEXT,
    `order_num` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `experiences` (`title`, `organization`, `period`, `type`, `description`, `order_num`) VALUES
('Senior Web Developer', 'Tech Innovasi Solusindo', '2024 - Sekarang', 'work', 'Memimpin tim pengembangan sistem ERP berbasis web, mengoptimalkan query database MySQL, dan membangun RESTful API untuk klien enterprise.', 1),
('Junior Web Developer', 'PT Digital Kreasi Nusantara', '2022 - 2024', 'work', 'Membangun aplikasi website dinamis menggunakan PHP & MySQL, mengintegrasikan pihak ketiga (payment gateway & ekspedisi), serta pemeliharaan server.', 2),
('Freelance Web Developer', 'Self-Employed', '2021 - 2022', 'work', 'Mengerjakan lebih dari 15+ proyek website UMKM, company profile, dan aplikasi katalog produk untuk berbagai klien lokal.', 3),
('S1 Teknik Informatika', 'Universitas Teknologi Nasional', '2019 - 2023', 'education', 'Lulus dengan predikat Cum Laude (IPK 3.82). Fokus penelitian pada Optimasi Basis Data Relasional dan Rekayasa Perangkat Lunak Web.', 4);

-- 6. Table: testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(100) NOT NULL,
    `company` VARCHAR(100) NOT NULL,
    `message` TEXT NOT NULL,
    `rating` INT DEFAULT 5,
    `avatar_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimonials` (`name`, `role`, `company`, `message`, `rating`, `avatar_url`) VALUES
('Budi Santoso', 'CEO & Founder', 'PT Sentosa Abadi Jaya', 'Rizky sangat profesional dan cepat tanggap. Website manajemen apotek yang dibuat sangat membantu operasional bisnis kami meningkat hingga 40%.', 5, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80'),
('Citra Dewi', 'Marketing Director', 'Nusantara Retail Group', 'Hasil kerja yang luar biasa! Desain websitenya sangat modern, responsif di HP, dan kecepatan load-nya luar biasa cepat.', 5, 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80'),
('Ahmad Fauzi', 'Product Lead', 'TechStartup Indonesia', 'Kemampuan problem solving Rizky di bidang backend PHP & arsitektur MySQL sangat memuaskan. Sangat merekomendasikan Rizky untuk proyek web apapun!', 5, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80');

-- 7. Table: messages
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `messages` (`name`, `email`, `subject`, `message`, `is_read`) VALUES
('Hendro Wijaya', 'hendro@example.com', 'Penawaran Kerjasama Pembuatan Website', 'Halo Rizky, kami tertarik dengan portofolio Anda dan ingin mendiskusikan pembuatan website company profile untuk perusahaan kami.', 0);
