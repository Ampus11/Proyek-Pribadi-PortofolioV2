<?php
/**
 * Main Portfolio Single Page Application
 * PHP & MySQL - Clean, Human-Crafted Architecture
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Handle Contact Form Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Permintaan tidak valid (CSRF token mismatch). Silakan coba lagi.');
        header("Location: index.php#contact");
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        set_flash_message('warning', 'Mohon lengkapi semua kolom formulir kontak!');
        header("Location: index.php#contact");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message('warning', 'Format alamat email tidak valid!');
        header("Location: index.php#contact");
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            set_flash_message('success', 'Terima kasih! Pesan Anda telah terkirim dan tersimpan aman. Saya akan segera menghubungi Anda kembali.');
        } catch (PDOException $e) {
            set_flash_message('danger', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    } else {
        // Fallback for demonstration when deployed without DB
        set_flash_message('success', 'Terima kasih! Pesan Anda telah diterima (Demo Mode).');
    }

    header("Location: index.php#contact");
    exit;
}

// Fetch Profile Settings with sensible fallbacks
$full_name = get_setting($pdo, 'full_name', 'Rizky Pratama');
$job_title = get_setting($pdo, 'job_title', 'Full-Stack Web Developer & Backend Engineer');
$hero_greeting = get_setting($pdo, 'hero_greeting', 'Rizky Pratama');
$bio_summary = get_setting($pdo, 'bio_summary', 'Membangun aplikasi web dan sistem manajemen bisnis berbasis PHP & MySQL yang aman, terstruktur rapi, dan berkinerja tinggi.');
$about_description = get_setting($pdo, 'about_description', 'Saya adalah Full-Stack Web Developer yang mengutamakan arsitektur kode bersih, skema basis data ternormalisasi, dan antarmuka pengguna yang fungsional tanpa kompleksitas berlebih.');
$email = get_setting($pdo, 'email', 'rizky.pratama@example.com');
$phone = get_setting($pdo, 'phone', '+62 812-3456-7890');
$location = get_setting($pdo, 'location', 'Jakarta, Indonesia');
$years_experience = get_setting($pdo, 'years_experience', '3+');
$completed_projects = get_setting($pdo, 'completed_projects', '28+');
$happy_clients = get_setting($pdo, 'happy_clients', '20+');
$github_url = get_setting($pdo, 'github_url', 'https://github.com');
$linkedin_url = get_setting($pdo, 'linkedin_url', 'https://linkedin.com');
$resume_url = get_setting($pdo, 'resume_url', '#');

// Fetch Skills or use curated defaults
$skills_by_cat = [];
if ($pdo) {
    try {
        $skills_stmt = $pdo->query("SELECT * FROM skills ORDER BY category ASC, id ASC");
        $skills = $skills_stmt->fetchAll();
        foreach ($skills as $skill) {
            $skills_by_cat[$skill['category']][] = $skill;
        }
    } catch (Exception $e) {
        $skills_by_cat = [];
    }
}

if (empty($skills_by_cat)) {
    $skills_by_cat = [
        'Backend & Arsitektur' => [
            ['name' => 'PHP 8.x (OOP & MVC)', 'icon_class' => 'fa-brands fa-php', 'note' => 'Clean Code, Arsitektur Modular'],
            ['name' => 'MySQL / MariaDB', 'icon_class' => 'fa-solid fa-database', 'note' => 'Indexing, Optimasi Query, Relasi'],
            ['name' => 'PDO & Web Security', 'icon_class' => 'fa-solid fa-shield-halved', 'note' => 'Proteksi SQLi, CSRF, XSS, BCRYPT'],
            ['name' => 'RESTful API & Integration', 'icon_class' => 'fa-solid fa-network-wired', 'note' => 'Payment Gateway, Webhooks, JSON'],
            ['name' => 'Laravel Framework', 'icon_class' => 'fa-brands fa-laravel', 'note' => 'Eloquent ORM, Routing, Blade']
        ],
        'Frontend Development' => [
            ['name' => 'JavaScript (ES6+)', 'icon_class' => 'fa-brands fa-js', 'note' => 'DOM Manipulation, Async/Await'],
            ['name' => 'Tailwind CSS', 'icon_class' => 'fa-solid fa-wind', 'note' => 'Utility-First, Responsive Design'],
            ['name' => 'HTML5 & CSS3', 'icon_class' => 'fa-brands fa-html5', 'note' => 'Aksesibilitas, Semantik Web'],
            ['name' => 'Vue.js / Alpine.js', 'icon_class' => 'fa-brands fa-vuejs', 'note' => 'Reactivity, Komponen Interaktif']
        ],
        'Tools & Workflow' => [
            ['name' => 'Git & GitHub', 'icon_class' => 'fa-brands fa-git-alt', 'note' => 'Version Control, Branching, PR'],
            ['name' => 'Laragon & Apache/Nginx', 'icon_class' => 'fa-solid fa-server', 'note' => 'Konfigurasi Virtual Host, SSL'],
            ['name' => 'Docker Basics', 'icon_class' => 'fa-brands fa-docker', 'note' => 'Containerization Environment'],
            ['name' => 'Figma UI/UX Design', 'icon_class' => 'fa-brands fa-figma', 'note' => 'Wireframing & Prototyping']
        ]
    ];
}

// Fetch Projects or fallback
$projects = [];
if ($pdo) {
    try {
        $projects_stmt = $pdo->query("SELECT * FROM projects ORDER BY is_featured DESC, id DESC");
        $projects = $projects_stmt->fetchAll();
    } catch (Exception $e) {
        $projects = [];
    }
}

if (empty($projects)) {
    $projects = [
        [
            'id' => 1,
            'title' => 'Sistem Informasi Manajemen Apotek (SIM-Apotek)',
            'category' => 'Web App',
            'description' => 'Aplikasi kasir POS apotek dengan manajemen stok obat otomatis, peringatan kedaluwarsa batch obat, dan cetak struk penjualan termal.',
            'technologies' => 'PHP, MySQL, Tailwind CSS, Chart.js',
            'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ],
        [
            'id' => 2,
            'title' => 'E-Commerce Fashion Store "GayaKoe"',
            'category' => 'E-Commerce',
            'description' => 'Platform toko online dengan keranjang belanja interaktif, kalkulasi ongkos kirim ekspedisi otomatis, dan integrasi payment gateway Midtrans.',
            'technologies' => 'PHP, MySQL, Bootstrap 5, Midtrans API',
            'image_url' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ],
        [
            'id' => 3,
            'title' => 'Portal Berita & Publikasi "KabarNusantara"',
            'category' => 'Web App',
            'description' => 'Content Management System (CMS) publikasi digital dengan manajemen kategori artikel berlapis, sistem komentar terverifikasi, dan SEO Friendly URLs.',
            'technologies' => 'PHP Native OOP, MySQL, Tailwind CSS',
            'image_url' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ],
        [
            'id' => 4,
            'title' => 'Landing Page Startup FinTech "CuanKu"',
            'category' => 'Landing Page',
            'description' => 'Halaman konversi tinggi untuk startup finansial dilengkapi simulasi kalkulator bunga pinjaman/investasi dan form pendaftaran nasabah.',
            'technologies' => 'HTML5, CSS3, JavaScript, PHP Mailer',
            'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ],
        [
            'id' => 5,
            'title' => 'Sistem Presensi Karyawan & Payroll',
            'category' => 'Web App',
            'description' => 'Aplikasi absensi online berbasis QR Code dan validasi koordinat GPS Geolocation, dilengkapi modul penggajian otomatis dan ekspor PDF.',
            'technologies' => 'PHP, MySQL, Leaflet JS, DomPDF',
            'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ],
        [
            'id' => 6,
            'title' => 'Redesign Antarmuka Mobile Banking App',
            'category' => 'UI/UX',
            'description' => 'Riset dan perancangan antarmuka aplikasi transaksi perbankan digital untuk menyederhanakan alur transfer saldo dan pembayaran tagihan.',
            'technologies' => 'Figma, Design System, Prototyping',
            'image_url' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&auto=format&fit=crop&q=80',
            'demo_url' => '#',
            'github_url' => 'https://github.com'
        ]
    ];
}

$project_categories = array_unique(array_column($projects, 'category'));

// Fetch Experiences or fallback
$work_exp = [];
$edu_exp = [];
if ($pdo) {
    try {
        $exp_stmt = $pdo->query("SELECT * FROM experiences ORDER BY order_num ASC, id ASC");
        $all_exp = $exp_stmt->fetchAll();
        $work_exp = array_filter($all_exp, fn($e) => $e['type'] === 'work');
        $edu_exp = array_filter($all_exp, fn($e) => $e['type'] === 'education');
    } catch (Exception $e) {
        $work_exp = [];
        $edu_exp = [];
    }
}

if (empty($work_exp)) {
    $work_exp = [
        [
            'title' => 'Senior Web Developer',
            'organization' => 'Tech Innovasi Solusindo',
            'period' => '2024 — Sekarang',
            'description' => 'Merancang arsitektur sistem ERP pergudangan, memimpin migrasi skema database relasional, dan mengoptimalkan performa query transaksi berskala besar.'
        ],
        [
            'title' => 'Junior Web Developer',
            'organization' => 'PT Digital Kreasi Nusantara',
            'period' => '2022 — 2024',
            'description' => 'Mengembangkan modul e-commerce, integrasi webhook payment gateway, dan pemeliharaan fungsionalitas backend berbasis PHP & MySQL.'
        ],
        [
            'title' => 'Freelance Web Developer',
            'organization' => 'Independent / Freelance',
            'period' => '2021 — 2022',
            'description' => 'Membangun lebih dari 15+ website profil bisnis UMKM, sistem katalog produk, dan aplikasi form leads untuk berbagai klien lokal.'
        ]
    ];
}

if (empty($edu_exp)) {
    $edu_exp = [
        [
            'title' => 'S1 Teknik Informatika',
            'organization' => 'Universitas Teknologi Nasional',
            'period' => '2019 — 2023',
            'description' => 'Lulus dengan predikat Cum Laude (IPK 3.82). Fokus penelitian pada Optimasi Basis Data Relasional dan Rekayasa Perangkat Lunak Web.'
        ]
    ];
}

// Fetch Testimonials or fallback
$testimonials = [];
if ($pdo) {
    try {
        $testi_stmt = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC LIMIT 6");
        $testimonials = $testi_stmt->fetchAll();
    } catch (Exception $e) {
        $testimonials = [];
    }
}

if (empty($testimonials)) {
    $testimonials = [
        [
            'name' => 'Budi Santoso',
            'role' => 'CEO & Founder',
            'company' => 'PT Sentosa Abadi Jaya',
            'message' => 'Rizky sangat profesional dalam memahami proses bisnis kami. Aplikasi apotek yang dibangun stabil, cepat, dan membuat proses kasir jauh lebih akurat.',
            'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80'
        ],
        [
            'name' => 'Citra Dewi',
            'role' => 'Marketing Director',
            'company' => 'Nusantara Retail Group',
            'message' => 'Pekerjaan yang rapi dan komunikasi yang transparan. Website e-commerce kami selesai tepat waktu dengan tampilan responsif yang mudah dipakai pelanggan.',
            'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=120&auto=format&fit=crop&q=80'
        ],
        [
            'name' => 'Ahmad Fauzi',
            'role' => 'Product Lead',
            'company' => 'TechStartup Indonesia',
            'message' => 'Kemampuan problem solving Rizky di bidang backend PHP dan query database sangat teruji. Rekan diskusi yang solutif untuk arsitektur aplikasi.',
            'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80'
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

    <main class="flex-grow pt-24">
        <!-- ==================== 1. HERO SECTION ==================== -->
        <section id="home" class="py-16 sm:py-24 relative overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        <!-- Understated Status Badge -->
                        <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-zinc-900 border border-white/10 text-xs text-zinc-300 font-medium">
                            <span class="status-indicator">
                                <span class="status-glow"></span>
                                <span class="status-dot"></span>
                            </span>
                            <span>Tersedia untuk proyek freelance &amp; kerja sama tim</span>
                        </div>

                        <!-- Main Headline -->
                        <div class="space-y-3">
                            <h1 class="text-3xl sm:text-5xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.18]">
                                Merancang &amp; Membangun Sistem Web yang Andal, Aman, dan Efisien.
                            </h1>
                            <p class="text-zinc-400 text-sm sm:text-base leading-relaxed max-w-xl font-normal">
                                Saya <strong class="text-zinc-200"><?= sanitize($full_name) ?></strong>, seorang Full-Stack Web Developer berbasis di <?= sanitize($location) ?>. Berfokus pada pengembangan backend <span class="text-zinc-200 font-medium">PHP &amp; MySQL</span> yang kokoh, arsitektur database terstruktur, dan antarmuka web yang intuitif.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="#projects" class="px-5 py-2.5 rounded-lg bg-white text-zinc-950 hover:bg-zinc-200 text-xs font-semibold shadow-sm transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-[11px]"></i> Jelajahi Karya Saya
                            </a>
                            <button type="button" onclick="copyEmailToClipboard('<?= sanitize($email) ?>')" class="px-4 py-2.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-white/10 text-xs font-medium transition inline-flex items-center gap-2" title="Salin Email">
                                <i class="fa-regular fa-copy text-[11px]"></i> Salin Email
                            </button>
                            <a href="#contact" class="px-4 py-2.5 rounded-lg text-zinc-400 hover:text-white text-xs font-medium transition inline-flex items-center gap-1.5">
                                Hubungi Langsung <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- Developer Metrics -->
                        <div class="grid grid-cols-3 gap-6 pt-6 border-t border-white/10 max-w-lg">
                            <div>
                                <div class="text-2xl font-bold text-white font-mono"><?= sanitize($years_experience) ?></div>
                                <div class="text-[11px] text-zinc-500 uppercase tracking-wider font-mono mt-0.5">Tahun Pengalaman</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-white font-mono"><?= sanitize($completed_projects) ?></div>
                                <div class="text-[11px] text-zinc-500 uppercase tracking-wider font-mono mt-0.5">Proyek Selesai</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-white font-mono"><?= sanitize($happy_clients) ?></div>
                                <div class="text-[11px] text-zinc-500 uppercase tracking-wider font-mono mt-0.5">Klien &amp; Mitra</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Visual: Developer Architecture Card (No Tacky AI Blobs) -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="w-full max-w-md craft-card rounded-xl p-5 shadow-2xl relative">
                            <!-- Window Titlebar -->
                            <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-zinc-700/80 inline-block"></span>
                                    <span class="w-3 h-3 rounded-full bg-zinc-700/80 inline-block"></span>
                                    <span class="w-3 h-3 rounded-full bg-zinc-700/80 inline-block"></span>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-code text-[10px] text-indigo-400"></i> developer-profile.php
                                </div>
                                <div class="w-3"></div>
                            </div>

                            <!-- Code / Spec Snippet -->
                            <div class="font-mono text-xs text-zinc-300 space-y-2.5 leading-relaxed bg-[#0b0c12] p-4 rounded-lg border border-white/5">
                                <div>
                                    <span class="text-purple-400">class</span> <span class="text-yellow-300">SoftwareEngineer</span> {
                                </div>
                                <div class="pl-4 space-y-1 text-zinc-400 text-[11px]">
                                    <div><span class="text-indigo-400">public string</span> <span class="text-zinc-200">$name</span> = <span class="text-emerald-300">'<?= sanitize($full_name) ?>'</span>;</div>
                                    <div><span class="text-indigo-400">public string</span> <span class="text-zinc-200">$role</span> = <span class="text-emerald-300">'Full-Stack Web Dev'</span>;</div>
                                    <div><span class="text-indigo-400">public string</span> <span class="text-zinc-200">$base</span> = <span class="text-emerald-300">'<?= sanitize($location) ?>'</span>;</div>
                                </div>
                                <div class="pl-4 pt-1 text-[11px]">
                                    <div class="text-zinc-500">// Stack Inti Pengembangan:</div>
                                    <div class="text-zinc-300">
                                        <span class="text-indigo-400">public array</span> <span class="text-zinc-200">$coreStack</span> = [
                                    </div>
                                    <div class="pl-4 text-emerald-300 text-[10px] space-y-0.5">
                                        <div>'PHP 8.3 OOP &amp; MVC',</div>
                                        <div>'MySQL Relational Schema',</div>
                                        <div>'Tailwind CSS &amp; JavaScript',</div>
                                        <div>'REST API &amp; Security Best Practices'</div>
                                    </div>
                                    <div class="text-zinc-300">];</div>
                                </div>
                                <div>}</div>
                            </div>

                            <!-- Highlights Footer -->
                            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-white/10 text-xs">
                                <div class="p-2.5 rounded-lg bg-white/[0.03] border border-white/5">
                                    <div class="text-[10px] font-mono text-zinc-500 uppercase">Fokus Arsitektur</div>
                                    <div class="text-xs font-semibold text-zinc-200 mt-0.5">Clean Code &amp; PDO</div>
                                </div>
                                <div class="p-2.5 rounded-lg bg-white/[0.03] border border-white/5">
                                    <div class="text-[10px] font-mono text-zinc-500 uppercase">Integritas Data</div>
                                    <div class="text-xs font-semibold text-zinc-200 mt-0.5">Prepared Statements</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ==================== 2. ABOUT SECTION ==================== -->
        <section id="about" class="py-20 bg-[#0c0e15] border-y border-white/5 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="max-w-2xl mb-12">
                    <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">01. Tentang &amp; Pendekatan</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                        Membangun Perangkat Lunak dengan Fondasi yang Tepat.
                    </h2>
                    <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                        Pendekatan saya bertumpu pada kemudahan pemeliharaan kode jangka panjang, pemodelan data relasional yang kokoh, dan pengalaman pengguna yang cepat.
                    </p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <!-- Bio Narrative Card -->
                    <div class="md:col-span-7 craft-card rounded-xl p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-xs font-mono text-zinc-400">
                                <i class="fa-solid fa-terminal text-indigo-400"></i> ringkasan-profil.md
                            </div>
                            <p class="text-zinc-300 text-xs sm:text-sm leading-relaxed">
                                <?= nl2br(sanitize($about_description)) ?>
                            </p>
                            <p class="text-zinc-400 text-xs leading-relaxed">
                                Saya terbiasa mengelola siklus pengembangan perangkat lunak dari tahap analisis kebutuhan pengguna, perancangan diagram relasi entitas (ERD), pembuatan backend API, hingga integrasi antarmuka web yang responsif.
                            </p>
                        </div>

                        <!-- Direct Contact Pills -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-6 mt-6 border-t border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-300 text-xs">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-mono text-zinc-500 uppercase">Domisili</div>
                                    <div class="text-xs font-medium text-zinc-200"><?= sanitize($location) ?></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-300 text-xs">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-mono text-zinc-500 uppercase">Waktu Respon</div>
                                    <div class="text-xs font-medium text-emerald-400">&lt; 24 Jam Kerja</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Engineering Pillars -->
                    <div class="md:col-span-5 space-y-4">
                        <div class="craft-card rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-7 h-7 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Keamanan &amp; Integritas Data</h3>
                            </div>
                            <p class="text-zinc-400 text-xs leading-relaxed">
                                Menggunakan PDO Prepared Statements untuk mencegah SQL Injection, validasi token CSRF, sanitasi output XSS, dan enkripsi password BCRYPT.
                            </p>
                        </div>

                        <div class="craft-card rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-7 h-7 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-database"></i>
                                </div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Arsitektur Basis Data</h3>
                            </div>
                            <p class="text-zinc-400 text-xs leading-relaxed">
                                Perancangan skema relasional MySQL yang ternormalisasi, penataan foreign key, dan indeks query untuk meminimalkan beban komputasi server.
                            </p>
                        </div>

                        <div class="craft-card rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-7 h-7 rounded-md bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Performa &amp; Kode Teruji</h3>
                            </div>
                            <p class="text-zinc-400 text-xs leading-relaxed">
                                Struktur kode modular yang mudah dibaca sesama pengembang, tanpa bloated assets, menghasilkan waktu muat halaman yang kilat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== 3. TECH STACK SECTION (NO FAKE PERCENTAGES) ==================== -->
        <section id="skills" class="py-20 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="max-w-2xl mb-12">
                    <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">02. Stack Teknologi</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                        Alat &amp; Bahasa yang Digunakan dalam Produksi.
                    </h2>
                    <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                        Teknologi yang saya gunakan dipilih berdasarkan kebutuhan stabilitas sistem, kemudahan deployment, dan efisiensi eksekusi.
                    </p>
                </div>

                <!-- Categorized Tech Stack Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($skills_by_cat as $category => $cat_skills): ?>
                    <div class="craft-card rounded-xl p-6 flex flex-col justify-between">
                        <div>
                            <!-- Category Header -->
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-white/10">
                                <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 text-zinc-300 flex items-center justify-center text-xs">
                                    <?php if (stripos($category, 'Frontend') !== false): ?>
                                        <i class="fa-solid fa-palette text-sky-400"></i>
                                    <?php elseif (stripos($category, 'Backend') !== false): ?>
                                        <i class="fa-solid fa-server text-indigo-400"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-wrench text-emerald-400"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-sm"><?= sanitize($category) ?></h3>
                                    <div class="text-[10px] font-mono text-zinc-500"><?= count($cat_skills) ?> Keahlian Utama</div>
                                </div>
                            </div>

                            <!-- Skills List -->
                            <div class="space-y-3">
                                <?php foreach ($cat_skills as $skill): ?>
                                <div class="p-3 rounded-lg bg-white/[0.02] border border-white/5 hover:border-white/15 transition">
                                    <div class="flex items-center gap-2.5">
                                        <i class="<?= sanitize($skill['icon_class'] ?? 'fa-solid fa-check') ?> text-xs text-zinc-400 w-4 text-center"></i>
                                        <span class="text-xs font-semibold text-zinc-200"><?= sanitize($skill['name']) ?></span>
                                    </div>
                                    <?php if (!empty($skill['note'])): ?>
                                        <div class="text-[11px] text-zinc-500 font-mono pl-6 mt-1"><?= sanitize($skill['note']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================== 4. PROJECTS SECTION ==================== -->
        <section id="projects" class="py-20 bg-[#0c0e15] border-y border-white/5 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                    <div class="max-w-2xl">
                        <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">03. Portofolio Proyek</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                            Karya &amp; Implementasi Sistem Terpilih.
                        </h2>
                        <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                            Studi kasus aplikasi web nyata yang dibangun dengan pertimbangan arsitektur dan kebutuhan klien.
                        </p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" class="filter-btn active px-3.5 py-1.5 rounded-lg text-xs font-medium cursor-pointer" data-filter="all">
                            Semua (<?= count($projects) ?>)
                        </button>
                        <?php foreach ($project_categories as $cat): ?>
                        <button type="button" class="filter-btn px-3.5 py-1.5 rounded-lg text-xs font-medium cursor-pointer" data-filter="<?= sanitize($cat) ?>">
                            <?= sanitize($cat) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Projects Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($projects)): ?>
                        <div class="col-span-full text-center py-16 text-zinc-500 text-xs font-mono">
                            Belum ada proyek yang ditampilkan.
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                        <div class="project-card craft-card rounded-xl overflow-hidden flex flex-col justify-between group" data-category="<?= sanitize($project['category']) ?>">
                            <div>
                                <!-- Window Frame Header -->
                                <div class="px-4 py-2.5 bg-zinc-900/90 border-b border-white/10 flex items-center justify-between text-[11px] font-mono text-zinc-500">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-700 inline-block"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-700 inline-block"></span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-700 inline-block"></span>
                                    </div>
                                    <span class="text-[10px] uppercase tracking-wider text-zinc-400"><?= sanitize($project['category']) ?></span>
                                </div>

                                <!-- Image Container -->
                                <div class="relative aspect-video overflow-hidden bg-zinc-950">
                                    <img src="<?= sanitize($project['image_url']) ?>" alt="<?= sanitize($project['title']) ?>" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-300" loading="lazy">
                                </div>

                                <!-- Body -->
                                <div class="p-5 space-y-3">
                                    <h3 class="font-bold text-sm text-white leading-snug group-hover:text-indigo-400 transition">
                                        <?= sanitize($project['title']) ?>
                                    </h3>
                                    <p class="text-zinc-400 text-xs line-clamp-3 leading-relaxed">
                                        <?= sanitize($project['description']) ?>
                                    </p>

                                    <!-- Tech tags -->
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        <?php 
                                            $techs = explode(',', $project['technologies']);
                                            foreach ($techs as $tech): 
                                                $tech = trim($tech);
                                                if ($tech):
                                        ?>
                                        <span class="tech-pill">
                                            <?= sanitize($tech) ?>
                                        </span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Links -->
                            <div class="px-5 py-3 border-t border-white/10 bg-zinc-950/40 flex items-center justify-between text-xs">
                                <?php if (!empty($project['github_url']) && $project['github_url'] !== '#'): ?>
                                    <a href="<?= sanitize($project['github_url']) ?>" target="_blank" rel="noopener" class="text-zinc-400 hover:text-white flex items-center gap-1.5 transition font-medium">
                                        <i class="fa-brands fa-github text-xs"></i> Repository
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-zinc-600 font-mono"><i class="fa-solid fa-lock text-[10px]"></i> Private Repo</span>
                                <?php endif; ?>

                                <?php if (!empty($project['demo_url']) && $project['demo_url'] !== '#'): ?>
                                    <a href="<?= sanitize($project['demo_url']) ?>" target="_blank" rel="noopener" class="text-indigo-400 hover:text-indigo-300 flex items-center gap-1 font-semibold transition">
                                        Live Preview <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-zinc-500 font-mono">Case Study</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- ==================== 5. EXPERIENCE & EDUCATION ==================== -->
        <section id="experience" class="py-20 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="max-w-2xl mb-12">
                    <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">04. Rekam Jejak</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                        Pengalaman Kerja &amp; Pendidikan.
                    </h2>
                    <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                        Latar belakang profesional dalam pengembangan sistem dan rekayasa perangkat lunak.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Work Experiences Column -->
                    <div class="lg:col-span-7 craft-card rounded-xl p-6 sm:p-8">
                        <div class="flex items-center gap-2.5 mb-6 pb-4 border-b border-white/10">
                            <i class="fa-solid fa-briefcase text-xs text-indigo-400"></i>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Pengalaman Profesional</h3>
                        </div>

                        <div class="space-y-6">
                            <?php foreach ($work_exp as $exp): ?>
                            <div class="pb-6 border-b border-white/5 last:border-0 last:pb-0 space-y-1.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="text-sm font-bold text-white"><?= sanitize($exp['title']) ?></h4>
                                    <span class="text-[11px] font-mono text-zinc-400 bg-zinc-900 px-2 py-0.5 rounded border border-white/10">
                                        <?= sanitize($exp['period']) ?>
                                    </span>
                                </div>
                                <div class="text-xs font-medium text-indigo-400">
                                    <?= sanitize($exp['organization']) ?>
                                </div>
                                <p class="text-xs text-zinc-400 leading-relaxed pt-1">
                                    <?= sanitize($exp['description']) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Education Column -->
                    <div class="lg:col-span-5 craft-card rounded-xl p-6 sm:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2.5 mb-6 pb-4 border-b border-white/10">
                                <i class="fa-solid fa-graduation-cap text-xs text-emerald-400"></i>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Pendidikan Formal</h3>
                            </div>

                            <div class="space-y-6">
                                <?php foreach ($edu_exp as $exp): ?>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-sm font-bold text-white"><?= sanitize($exp['title']) ?></h4>
                                        <span class="text-[11px] font-mono text-zinc-400 bg-zinc-900 px-2 py-0.5 rounded border border-white/10">
                                            <?= sanitize($exp['period']) ?>
                                        </span>
                                    </div>
                                    <div class="text-xs font-medium text-emerald-400">
                                        <?= sanitize($exp['organization']) ?>
                                    </div>
                                    <p class="text-xs text-zinc-400 leading-relaxed pt-1">
                                        <?= sanitize($exp['description']) ?>
                                    </p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Mini Credo Box -->
                        <div class="mt-8 p-4 rounded-lg bg-white/[0.02] border border-white/5 text-xs text-zinc-400 leading-relaxed">
                            <div class="font-semibold text-zinc-300 mb-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-award text-amber-400 text-xs"></i> Komitmen Kualitas
                            </div>
                            Menjaga disiplin dokumentasi kode, pengelolaan versi Git yang teratur, dan standar kode yang mematuhi PSR (PHP Standard Recommendations).
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== 6. TESTIMONIALS ==================== -->
        <section id="testimonials" class="py-20 bg-[#0c0e15] border-y border-white/5 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="max-w-2xl mb-12">
                    <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">05. Ulasan Kolaborasi</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                        Feedback Rekan Kerja &amp; Klien.
                    </h2>
                    <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                        Pendapat langsung dari orang-orang yang pernah berkolaborasi dalam proyek perangkat lunak.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($testimonials as $testi): ?>
                    <div class="craft-card rounded-xl p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="text-zinc-500 font-mono text-sm">
                                <i class="fa-solid fa-quote-left text-zinc-600"></i>
                            </div>
                            <p class="text-xs text-zinc-300 leading-relaxed">
                                "<?= sanitize($testi['message']) ?>"
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-5 mt-5 border-t border-white/10">
                            <img src="<?= sanitize($testi['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80') ?>" alt="<?= sanitize($testi['name']) ?>" class="w-9 h-9 rounded-full object-cover border border-white/10" loading="lazy">
                            <div>
                                <div class="text-xs font-bold text-white"><?= sanitize($testi['name']) ?></div>
                                <div class="text-[10px] text-zinc-500 font-mono"><?= sanitize($testi['role']) ?> &bull; <?= sanitize($testi['company']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ==================== 7. CONTACT SECTION ==================== -->
        <section id="contact" class="py-20 relative">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="max-w-2xl mb-12">
                    <span class="text-xs font-mono font-semibold text-indigo-400 uppercase tracking-widest">06. Hubungi Saya</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1.5">
                        Mulai Diskusi atau Konsultasi Proyek.
                    </h2>
                    <p class="text-zinc-400 text-xs sm:text-sm mt-2 leading-relaxed">
                        Silakan hubungi saya melalui formulir di bawah ini atau saluran kontak langsung untuk mendiskusikan peluang proyek Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Info Cards -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="craft-card rounded-xl p-6 space-y-4">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Saluran Komunikasi Langsung</h3>
                            <p class="text-xs text-zinc-400 leading-relaxed">
                                Saya selalu terbuka untuk konsultasi arsitektur sistem web, perancangan database, maupun kerja sama tim pengembang.
                            </p>

                            <div class="space-y-3 pt-2">
                                <!-- Email Card with 1-click copy -->
                                <div class="p-3.5 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-between">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-8 h-8 rounded-md bg-white/5 text-zinc-300 flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[10px] font-mono text-zinc-500 uppercase">Alamat Email</div>
                                            <div class="text-xs font-medium text-zinc-200 truncate"><?= sanitize($email) ?></div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="copyEmailToClipboard('<?= sanitize($email) ?>')" class="text-xs px-2.5 py-1.5 rounded bg-white/10 hover:bg-white/20 text-zinc-200 font-mono transition" title="Salin Email">
                                        Copy
                                    </button>
                                </div>

                                <!-- WhatsApp Card -->
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $phone) ?>" target="_blank" rel="noopener" class="p-3.5 rounded-lg bg-zinc-900 border border-white/10 hover:border-emerald-500/40 transition flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-md bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-mono text-zinc-500 uppercase">WhatsApp / Chat</div>
                                            <div class="text-xs font-medium text-zinc-200"><?= sanitize($phone) ?></div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs text-zinc-500 group-hover:text-emerald-400 transition pr-1"></i>
                                </a>

                                <!-- Location Card -->
                                <div class="p-3.5 rounded-lg bg-zinc-900 border border-white/10 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-md bg-white/5 text-zinc-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-mono text-zinc-500 uppercase">Lokasi Kerja</div>
                                        <div class="text-xs font-medium text-zinc-200"><?= sanitize($location) ?> (WIB)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Form Column -->
                    <div class="lg:col-span-7">
                        <div class="craft-card rounded-xl p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Formulir Pesan</h3>
                                    <p class="text-xs text-zinc-400 mt-0.5">Pesan akan tersimpan aman dan terkirim ke kotak masuk.</p>
                                </div>
                                <span class="text-[11px] font-mono text-zinc-500">Form Kontak</span>
                            </div>

                            <form id="contactForm" action="index.php#contact" method="POST" class="space-y-4">
                                <input type="hidden" name="action" value="send_message">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-xs font-medium text-zinc-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                                        <input type="text" id="name" name="name" required placeholder="Nama Anda" class="w-full px-3.5 py-2.5 bg-zinc-900/90 border border-white/10 rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-xs font-medium text-zinc-300 mb-1.5">Alamat Email <span class="text-rose-400">*</span></label>
                                        <input type="email" id="email" name="email" required placeholder="nama@email.com" class="w-full px-3.5 py-2.5 bg-zinc-900/90 border border-white/10 rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition">
                                    </div>
                                </div>

                                <div>
                                    <label for="subject" class="block text-xs font-medium text-zinc-300 mb-1.5">Subjek / Keperluan <span class="text-rose-400">*</span></label>
                                    <input type="text" id="subject" name="subject" required placeholder="Contoh: Konsultasi Sistem Aplikasi POS Kasir" class="w-full px-3.5 py-2.5 bg-zinc-900/90 border border-white/10 rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition">
                                </div>

                                <div>
                                    <label for="message" class="block text-xs font-medium text-zinc-300 mb-1.5">Pesan Anda <span class="text-rose-400">*</span></label>
                                    <textarea id="message" name="message" rows="4" required placeholder="Jelaskan kebutuhan proyek, target waktu, atau pertanyaan yang ingin Anda sampaikan..." class="w-full px-3.5 py-2.5 bg-zinc-900/90 border border-white/10 rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                                </div>

                                <button type="submit" class="w-full py-2.5 px-5 rounded-lg bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs transition flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                    <i class="fa-regular fa-paper-plane text-[11px]"></i> Kirim Pesan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Toast Notification for One-Click Copy -->
    <div id="toastNotification" class="px-4 py-2.5 rounded-lg bg-zinc-900 border border-white/20 text-white text-xs font-mono shadow-2xl flex items-center gap-2">
        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
        <span id="toastText">Email disalin ke clipboard!</span>
    </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
