<?php
/**
 * Admin Panel Header & Sidebar Layout
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
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio Manager</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full font-sans text-slate-100 flex flex-col md:flex-row bg-slate-950">

    <!-- Mobile Top Header Bar -->
    <div class="md:hidden bg-slate-900 border-b border-slate-800 p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                A
            </div>
            <span class="font-bold text-white text-sm">Admin Portfolio</span>
        </div>
        <button id="adminMobileToggle" class="text-slate-300 p-2 bg-slate-800 rounded-lg border border-slate-700">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Admin Sidebar -->
    <aside id="adminSidebar" class="hidden md:flex flex-col w-64 bg-slate-900 border-r border-slate-800 p-4 min-h-screen">
        <!-- Brand Logo -->
        <div class="flex items-center gap-3 px-3 py-4 mb-4 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-500/20">
                P
            </div>
            <div>
                <h1 class="font-bold text-sm text-white tracking-tight">Portofolio Admin</h1>
                <p class="text-[11px] text-emerald-400 font-mono"><i class="fa-solid fa-circle text-[7px]"></i> Online</p>
            </div>
        </div>

        <!-- Navigation Menu Items -->
        <nav class="flex-1 space-y-1.5 text-xs font-semibold">
            <a href="index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'index.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-gauge-high w-4 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="projects.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'projects.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-layer-group w-4 text-center"></i>
                <span>Kelola Proyek</span>
            </a>

            <a href="skills.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'skills.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-code w-4 text-center"></i>
                <span>Kelola Keahlian</span>
            </a>

            <a href="testimonials.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'testimonials.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-comments w-4 text-center"></i>
                <span>Testimoni Klien</span>
            </a>

            <a href="messages.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'messages.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-inbox w-4 text-center"></i>
                    <span>Pesan Masuk</span>
                </div>
                <?php if ($unread_count > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white font-mono text-[10px] font-bold">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="settings.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'settings.php' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-user-gear w-4 text-center"></i>
                <span>Pengaturan Profil</span>
            </a>
        </nav>

        <!-- Bottom User & Website Links -->
        <div class="pt-4 border-t border-slate-800 space-y-2">
            <a href="../index.php" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs transition">
                <span class="flex items-center gap-2"><i class="fa-solid fa-globe"></i> Lihat Website</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>

            <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="flex items-center gap-2 px-3 py-2 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 text-xs transition font-semibold">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
        <!-- Top Navigation Header -->
        <header class="bg-slate-900/60 border-b border-slate-800/80 px-6 py-4 flex items-center justify-between backdrop-blur-md sticky top-0 z-30">
            <div>
                <h2 class="text-base font-bold text-white">Panel Pengelolaan Portofolio</h2>
                <p class="text-[11px] text-slate-400">Kelola konten dan pesan masuk secara real-time</p>
            </div>

            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs border border-slate-700 transition">
                    <i class="fa-solid fa-external-link text-[10px]"></i> Live Web
                </a>
                
                <div class="flex items-center gap-2.5 pl-4 border-l border-slate-800">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                        <?= strtoupper(substr($admin_name, 0, 1)) ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-white"><?= sanitize($admin_name) ?></div>
                        <div class="text-[10px] text-slate-400">Super Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content Body Container -->
        <main class="flex-1 p-6 sm:p-8 max-w-7xl w-full mx-auto">
