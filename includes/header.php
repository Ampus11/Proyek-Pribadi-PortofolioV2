<?php
/**
 * Header Template - Modern Minimalist & Human Crafted
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$site_name = get_setting($pdo, 'full_name', 'Rizky Pratama');
$site_title = get_setting($pdo, 'job_title', 'Full Stack Web Developer');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($site_name) ?> — <?= sanitize($site_title) ?></title>
    <meta name="description" content="Portofolio resmi <?= sanitize($site_name) ?>. Spesialis Full-Stack Web Development, arsitektur backend PHP & MySQL, dan antarmuka web modern.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            base: '#090a0f',
                            surface: '#11131a',
                            card: '#151822',
                            border: 'rgba(255, 255, 255, 0.08)'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-[#090a0f] text-zinc-100 font-sans antialiased selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col relative overflow-x-hidden">

    <!-- Top Canvas Lighting -->
    <div class="canvas-grid fixed inset-0 pointer-events-none z-0"></div>

    <!-- Navigation Bar -->
    <header class="fixed top-0 left-0 right-0 z-50 craft-nav transition-all duration-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-3.5">
                <!-- Logo / Brand -->
                <a href="#home" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-white/15 flex items-center justify-center text-white font-mono font-bold text-sm group-hover:border-indigo-400/50 group-hover:bg-zinc-700/60 transition">
                        <?= strtoupper(substr($site_name, 0, 1)) ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-sm tracking-tight text-white group-hover:text-zinc-200 transition">
                            <?= sanitize($site_name) ?>
                        </span>
                        <span class="text-[11px] text-zinc-500 font-mono -mt-0.5">
                            developer
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-xs font-medium text-zinc-400">
                    <a href="#home" class="nav-link active">Beranda</a>
                    <a href="#about" class="nav-link">Tentang</a>
                    <a href="#skills" class="nav-link">Stack</a>
                    <a href="#projects" class="nav-link">Proyek</a>
                    <a href="#experience" class="nav-link">Perjalanan</a>
                    <a href="#contact" class="nav-link">Kontak</a>
                </nav>

                <!-- Right Action Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if (is_admin_logged_in()): ?>
                        <a href="admin/index.php" class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-medium rounded-lg hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-gauge text-[11px]"></i> Admin Panel
                        </a>
                    <?php else: ?>
                        <a href="admin/login.php" class="text-zinc-400 hover:text-zinc-200 text-xs flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-white/5 hover:border-white/10 hover:bg-white/5 transition" title="Login Admin">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>
                    
                    <a href="#contact" class="px-4 py-2 rounded-lg bg-white text-zinc-950 hover:bg-zinc-200 text-xs font-semibold shadow-sm transition duration-150">
                        Hubungi Saya
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button id="mobileMenuBtn" type="button" class="p-2 text-zinc-400 hover:text-white bg-zinc-900 border border-white/10 rounded-lg focus:outline-none" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-[#0e1017] border-b border-white/10 px-5 pt-3 pb-5 space-y-2">
            <a href="#home" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Beranda</a>
            <a href="#about" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Tentang</a>
            <a href="#skills" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Tech Stack</a>
            <a href="#projects" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Proyek Pilihan</a>
            <a href="#experience" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Pengalaman</a>
            <a href="#contact" class="mobile-nav-link block px-3 py-2 text-zinc-300 hover:text-white hover:bg-white/5 rounded-lg text-xs font-medium">Hubungi</a>
            
            <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                <a href="admin/login.php" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1.5 py-1">
                    <i class="fa-solid fa-lock text-[10px]"></i> Login Admin
                </a>
                <a href="#contact" class="mobile-nav-link text-xs px-3.5 py-1.5 bg-white text-zinc-950 hover:bg-zinc-200 rounded-lg font-semibold">
                    Kirim Pesan
                </a>
            </div>
        </div>
    </header>
