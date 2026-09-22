<?php
/**
 * Admin Panel Header & Sidebar Layout
 * Modern Minimalist Dark Aesthetic Matching Frontend Portfolio
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

// Count unread messages for notification badge
$unread_stmt = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
$unread_count = (int)$unread_stmt->fetchColumn();

// Current active page script name
$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['admin_full_name'] ?? 'Administrator';
$site_name = get_setting($pdo, 'full_name', 'Rizky Pratama');
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#090a0f] scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — <?= sanitize($site_name) ?></title>
    
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-full font-sans text-zinc-100 flex flex-col md:flex-row bg-[#090a0f] antialiased selection:bg-indigo-500 selection:text-white relative">

    <!-- Top Canvas Lighting Background -->
    <div class="canvas-grid fixed inset-0 pointer-events-none z-0"></div>

    <!-- Mobile Top Header Bar -->
    <div class="md:hidden bg-[#0e1017]/90 backdrop-blur-md border-b border-white/10 p-4 flex items-center justify-between relative z-40">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-white/15 flex items-center justify-center text-white font-mono font-bold text-sm">
                <?= strtoupper(substr($site_name, 0, 1)) ?>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-white text-xs tracking-tight"><?= sanitize($site_name) ?></span>
                <span class="text-[10px] text-zinc-500 font-mono -mt-0.5">Admin Panel</span>
            </div>
        </div>
        <button id="adminMobileToggle" class="text-zinc-400 hover:text-white p-2 bg-zinc-900 rounded-lg border border-white/10 focus:outline-none" aria-label="Toggle Navigation">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>
    </div>

    <!-- Admin Sidebar -->
    <aside id="adminSidebar" class="hidden md:flex flex-col w-64 bg-[#0d0f17]/95 backdrop-blur-xl border-r border-white/10 p-4 min-h-screen relative z-30 flex-shrink-0">
        <!-- Brand Logo -->
        <div class="flex items-center gap-3 px-2 py-3 mb-4 border-b border-white/10">
            <div class="w-9 h-9 rounded-lg bg-zinc-800 border border-white/15 flex items-center justify-center text-white font-mono font-bold text-sm shadow-sm">
                <?= strtoupper(substr($site_name, 0, 1)) ?>
            </div>
            <div class="flex flex-col overflow-hidden">
                <h1 class="font-bold text-xs text-white tracking-tight truncate"><?= sanitize($site_name) ?></h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="status-indicator">
                        <span class="status-glow"></span>
                        <span class="status-dot"></span>
                    </span>
                    <span class="text-[10px] text-zinc-400 font-mono">admin mode</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu Items -->
        <nav class="flex-1 space-y-1 text-xs font-medium">
            <a href="index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition border <?= $current_page === 'index.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <i class="fa-solid fa-gauge-high w-4 text-center <?= $current_page === 'index.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                <span>Dashboard</span>
            </a>

            <a href="projects.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition border <?= $current_page === 'projects.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <i class="fa-solid fa-layer-group w-4 text-center <?= $current_page === 'projects.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                <span>Kelola Proyek</span>
            </a>

            <a href="skills.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition border <?= $current_page === 'skills.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <i class="fa-solid fa-code w-4 text-center <?= $current_page === 'skills.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                <span>Kelola Keahlian</span>
            </a>

            <a href="testimonials.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition border <?= $current_page === 'testimonials.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <i class="fa-solid fa-comments w-4 text-center <?= $current_page === 'testimonials.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                <span>Testimoni Klien</span>
            </a>

            <a href="messages.php" class="flex items-center justify-between px-3 py-2 rounded-xl transition border <?= $current_page === 'messages.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-inbox w-4 text-center <?= $current_page === 'messages.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                    <span>Pesan Masuk</span>
                </div>
                <?php if ($unread_count > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-mono text-[10px] font-bold">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="settings.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition border <?= $current_page === 'settings.php' ? 'bg-white/10 text-white border-white/15 font-semibold' : 'text-zinc-400 hover:text-white hover:bg-white/5 border-transparent' ?>">
                <i class="fa-solid fa-user-gear w-4 text-center <?= $current_page === 'settings.php' ? 'text-indigo-400' : 'text-zinc-400' ?>"></i>
                <span>Pengaturan Profil</span>
            </a>
        </nav>

        <!-- Bottom User & Website Links -->
        <div class="pt-4 border-t border-white/10 space-y-1.5">
            <a href="../index.php" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-zinc-400 hover:text-white hover:bg-white/5 border border-white/5 hover:border-white/10 text-xs transition">
                <span class="flex items-center gap-2"><i class="fa-solid fa-globe text-[11px]"></i> Lihat Website</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-zinc-500"></i>
            </a>

            <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="flex items-center gap-2 px-3 py-2 rounded-xl text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 text-xs transition font-medium">
                <i class="fa-solid fa-right-from-bracket text-[11px]"></i> Keluar (Logout)
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto relative z-10">
        <!-- Top Navigation Header -->
        <header class="bg-[#090a0f]/80 border-b border-white/10 px-6 py-3.5 flex items-center justify-between backdrop-blur-md sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-white tracking-tight">Panel Pengelolaan Portofolio</h2>
                <p class="text-[11px] text-zinc-400">Pembaruan konten dan data terintegrasi real-time</p>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <a href="../index.php" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white text-xs font-medium border border-white/10 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Live Web
                </a>
                
                <div class="flex items-center gap-2.5 pl-3 sm:pl-4 border-l border-white/10">
                    <div class="w-7 h-7 rounded-lg bg-zinc-800 border border-white/15 flex items-center justify-center text-xs font-mono font-bold text-white">
                        <?= strtoupper(substr($admin_name, 0, 1)) ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-white leading-tight"><?= sanitize($admin_name) ?></div>
                        <div class="text-[10px] text-zinc-500 font-mono">Super Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content Body Container -->
        <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto">
