<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/admin-header.php';

// Fetch statistics
$total_projects = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_skills = (int)$pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$total_testimonials = (int)$pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$total_messages = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Fetch recent messages
$recent_messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch recent projects
$recent_projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC LIMIT 4")->fetchAll();
?>

<!-- Welcome Banner -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white shadow-xl mb-8">
    <div class="relative z-10 max-w-2xl">
        <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold uppercase tracking-wider mb-3">
            Dashboard Overview
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">
            Selamat Datang, <?= sanitize($admin_name) ?>! 👋
        </h1>
        <p class="text-blue-100 text-xs sm:text-sm leading-relaxed">
            Kelola portofolio, pantau pesan masuk dari calon klien, serta perbarui keahlian dan karya terbaru Anda dengan cepat dan mudah.
        </p>
    </div>
    <!-- Decorative Icon Background -->
    <i class="fa-solid fa-laptop-code text-9xl absolute -right-6 -bottom-6 text-white/10 pointer-events-none"></i>
</div>

<!-- Stats Counter Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Stat 1: Projects -->
    <a href="projects.php" class="bg-slate-900 border border-slate-800 hover:border-blue-500/50 rounded-2xl p-5 transition group shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-400">Total Proyek</span>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1"><?= $total_projects ?></div>
        <p class="text-[11px] text-slate-400 flex items-center gap-1 group-hover:text-blue-400 transition">
            Kelola daftar karya &rarr;
        </p>
    </a>

    <!-- Stat 2: Skills -->
    <a href="skills.php" class="bg-slate-900 border border-slate-800 hover:border-purple-500/50 rounded-2xl p-5 transition group shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-400">Total Keahlian</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fa-solid fa-code"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1"><?= $total_skills ?></div>
        <p class="text-[11px] text-slate-400 flex items-center gap-1 group-hover:text-purple-400 transition">
            Kelola tech stack &rarr;
        </p>
    </a>

    <!-- Stat 3: Testimonials -->
    <a href="testimonials.php" class="bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-2xl p-5 transition group shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-400">Testimoni Klien</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1"><?= $total_testimonials ?></div>
        <p class="text-[11px] text-slate-400 flex items-center gap-1 group-hover:text-emerald-400 transition">
            Ulasan dan rating &rarr;
        </p>
    </a>

    <!-- Stat 4: Messages -->
    <a href="messages.php" class="bg-slate-900 border border-slate-800 hover:border-rose-500/50 rounded-2xl p-5 transition group shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-400">Pesan Masuk</span>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg group-hover:scale-110 transition">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2 mb-1">
            <span class="text-2xl font-bold text-white"><?= $total_messages ?></span>
            <?php if ($unread_count > 0): ?>
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 font-semibold font-mono">
                    <?= $unread_count ?> belum dibaca
                </span>
            <?php endif; ?>
        </div>
        <p class="text-[11px] text-slate-400 flex items-center gap-1 group-hover:text-rose-400 transition">
            Buka kotak pesan &rarr;
        </p>
    </a>
</div>

<!-- Two Columns: Recent Messages & Quick Projects -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left: Recent Messages (7 cols) -->
    <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <div>
                <h2 class="text-base font-bold text-white">Pesan Masuk Terbaru</h2>
                <p class="text-xs text-slate-400">Calon klien yang mengirim pesan lewat form kontak</p>
            </div>
            <a href="messages.php" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1">
                Semua Pesan &rarr;
            </a>
        </div>

        <?php if (empty($recent_messages)): ?>
            <div class="text-center py-10 text-slate-500 text-xs">
                <i class="fa-regular fa-envelope-open text-3xl mb-2 block text-slate-600"></i>
                Belum ada pesan yang masuk.
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-800">
                <?php foreach ($recent_messages as $msg): ?>
                <div class="py-3.5 flex items-start justify-between gap-4 <?= $msg['is_read'] ? 'opacity-75' : '' ?>">
                    <div class="flex items-start gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg <?= $msg['is_read'] ? 'bg-slate-800 text-slate-400' : 'bg-blue-600/20 text-blue-400' ?> flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                            <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                        </div>
                        <div class="overflow-hidden">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white truncate"><?= sanitize($msg['name']) ?></span>
                                <?php if (!$msg['is_read']): ?>
                                    <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" title="Belum dibaca"></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-[11px] font-medium text-slate-300 truncate"><?= sanitize($msg['subject']) ?></div>
                            <div class="text-[10px] text-slate-500 truncate max-w-md"><?= sanitize($msg['message']) ?></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] text-slate-500 block font-mono"><?= date('d M Y', strtotime($msg['created_at'])) ?></span>
                        <a href="messages.php" class="text-[11px] text-blue-400 hover:underline">Lihat</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right: Quick Actions & Recent Projects (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
        
        <!-- Quick Action Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <h2 class="text-base font-bold text-white mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <a href="projects.php?action=create" class="p-3 rounded-xl bg-blue-600/10 border border-blue-500/20 text-blue-300 hover:bg-blue-600 hover:text-white transition flex flex-col items-center justify-center text-center gap-2">
                    <i class="fa-solid fa-folder-plus text-xl"></i>
                    <span>Tambah Proyek</span>
                </a>
                <a href="skills.php" class="p-3 rounded-xl bg-purple-600/10 border border-purple-500/20 text-purple-300 hover:bg-purple-600 hover:text-white transition flex flex-col items-center justify-center text-center gap-2">
                    <i class="fa-solid fa-code-merge text-xl"></i>
                    <span>Tambah Keahlian</span>
                </a>
                <a href="testimonials.php" class="p-3 rounded-xl bg-emerald-600/10 border border-emerald-500/20 text-emerald-300 hover:bg-emerald-600 hover:text-white transition flex flex-col items-center justify-center text-center gap-2">
                    <i class="fa-solid fa-comment-medical text-xl"></i>
                    <span>Tambah Ulasan</span>
                </a>
                <a href="settings.php" class="p-3 rounded-xl bg-amber-600/10 border border-amber-500/20 text-amber-300 hover:bg-amber-600 hover:text-white transition flex flex-col items-center justify-center text-center gap-2">
                    <i class="fa-solid fa-sliders text-xl"></i>
                    <span>Edit Profil</span>
                </a>
            </div>
        </div>

        <!-- Recent Projects List -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-white">Proyek Terbaru</h2>
                <a href="projects.php" class="text-xs text-blue-400 hover:underline">Semua</a>
            </div>

            <div class="space-y-3">
                <?php foreach ($recent_projects as $p): ?>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <img src="<?= sanitize($p['image_url']) ?>" alt="<?= sanitize($p['title']) ?>" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-slate-700">
                        <div class="truncate">
                            <h4 class="text-xs font-bold text-white truncate"><?= sanitize($p['title']) ?></h4>
                            <span class="text-[10px] text-blue-400 font-medium"><?= sanitize($p['category']) ?></span>
                        </div>
                    </div>
                    <a href="projects.php?edit=<?= $p['id'] ?>" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition text-xs" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
