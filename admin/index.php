<?php
/**
 * Admin Dashboard
 * Modern Minimalist Styling Matching Main Portfolio
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Fetch statistics
$total_projects = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_skills = (int)$pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$total_testimonials = (int)$pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$total_messages = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Fetch recent messages
$recent_messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch recent projects
$recent_projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC LIMIT 4")->fetchAll();

require_once __DIR__ . '/admin-header.php';
?>

<!-- Welcome Banner -->
<div class="craft-card relative overflow-hidden rounded-2xl p-6 sm:p-8 mb-8 border border-white/10 bg-gradient-to-br from-[#131622] to-[#0d0f17]">
    <div class="relative z-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[11px] font-mono mb-3">
            <span class="status-indicator">
                <span class="status-glow"></span>
                <span class="status-dot"></span>
            </span>
            <span>Dashboard Overview</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white mb-2">
            Selamat Datang, <?= sanitize($admin_name) ?> 👋
        </h1>
        <p class="text-zinc-400 text-xs sm:text-sm leading-relaxed max-w-xl">
            Kelola karya proyek, pantau pesan masuk dari calon klien, serta perbarui keahlian dan ulasan portofolio Anda dalam satu panel terintegrasi.
        </p>
    </div>
    <!-- Subtle Background Icon -->
    <i class="fa-solid fa-laptop-code text-8xl absolute -right-4 -bottom-4 text-white/[0.03] pointer-events-none"></i>
</div>

<!-- Stats Counter Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Stat 1: Projects -->
    <a href="projects.php" class="craft-card rounded-2xl p-5 border border-white/10 transition group">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400">Total Proyek</span>
            <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-zinc-300 flex items-center justify-center text-sm group-hover:border-indigo-400/40 group-hover:text-white transition">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white font-mono mb-1"><?= $total_projects ?></div>
        <p class="text-[11px] text-zinc-500 flex items-center gap-1 group-hover:text-zinc-300 font-mono transition">
            Kelola daftar karya &rarr;
        </p>
    </a>

    <!-- Stat 2: Skills -->
    <a href="skills.php" class="craft-card rounded-2xl p-5 border border-white/10 transition group">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400">Total Keahlian</span>
            <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-zinc-300 flex items-center justify-center text-sm group-hover:border-sky-400/40 group-hover:text-white transition">
                <i class="fa-solid fa-code"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white font-mono mb-1"><?= $total_skills ?></div>
        <p class="text-[11px] text-zinc-500 flex items-center gap-1 group-hover:text-zinc-300 font-mono transition">
            Kelola tech stack &rarr;
        </p>
    </a>

    <!-- Stat 3: Testimonials -->
    <a href="testimonials.php" class="craft-card rounded-2xl p-5 border border-white/10 transition group">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400">Testimoni Klien</span>
            <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-zinc-300 flex items-center justify-center text-sm group-hover:border-emerald-400/40 group-hover:text-white transition">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-white font-mono mb-1"><?= $total_testimonials ?></div>
        <p class="text-[11px] text-zinc-500 flex items-center gap-1 group-hover:text-zinc-300 font-mono transition">
            Ulasan dan rating &rarr;
        </p>
    </a>

    <!-- Stat 4: Messages -->
    <a href="messages.php" class="craft-card rounded-2xl p-5 border border-white/10 transition group">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400">Pesan Masuk</span>
            <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-zinc-300 flex items-center justify-center text-sm group-hover:border-rose-400/40 group-hover:text-white transition">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2 mb-1">
            <span class="text-2xl font-bold text-white font-mono"><?= $total_messages ?></span>
            <?php if ($unread_count > 0): ?>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-semibold font-mono">
                    <?= $unread_count ?> baru
                </span>
            <?php endif; ?>
        </div>
        <p class="text-[11px] text-zinc-500 flex items-center gap-1 group-hover:text-zinc-300 font-mono transition">
            Buka kotak pesan &rarr;
        </p>
    </a>
</div>

<!-- Two Columns: Recent Messages & Quick Projects -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Left: Recent Messages (7 cols) -->
    <div class="lg:col-span-7 craft-card rounded-2xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <div>
                <h2 class="text-sm font-bold text-white tracking-tight">Pesan Masuk Terbaru</h2>
                <p class="text-xs text-zinc-400">Pesan langsung dari formulir kontak portofolio</p>
            </div>
            <a href="messages.php" class="text-xs text-zinc-400 hover:text-white font-medium flex items-center gap-1 transition">
                Semua Pesan &rarr;
            </a>
        </div>

        <?php if (empty($recent_messages)): ?>
            <div class="text-center py-10 text-zinc-500 text-xs">
                <i class="fa-regular fa-envelope-open text-2xl mb-2 block text-zinc-600"></i>
                Belum ada pesan yang masuk.
            </div>
        <?php else: ?>
            <div class="divide-y divide-white/5">
                <?php foreach ($recent_messages as $msg): ?>
                <div class="py-3.5 flex items-start justify-between gap-4 <?= $msg['is_read'] ? 'opacity-70' : '' ?>">
                    <div class="flex items-start gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg <?= $msg['is_read'] ? 'bg-zinc-800 text-zinc-400 border border-white/5' : 'bg-indigo-500/15 border border-indigo-500/30 text-indigo-300' ?> flex items-center justify-center text-xs font-mono font-bold flex-shrink-0 mt-0.5">
                            <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                        </div>
                        <div class="overflow-hidden">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white truncate"><?= sanitize($msg['name']) ?></span>
                                <?php if (!$msg['is_read']): ?>
                                    <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0" title="Belum dibaca"></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-[11px] font-medium text-zinc-300 truncate"><?= sanitize($msg['subject']) ?></div>
                            <div class="text-[11px] text-zinc-500 truncate max-w-md"><?= sanitize($msg['message']) ?></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] text-zinc-500 block font-mono"><?= date('d M Y', strtotime($msg['created_at'])) ?></span>
                        <a href="messages.php" class="text-[11px] text-indigo-400 hover:text-indigo-300 font-medium transition">Lihat</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right: Quick Actions & Recent Projects (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
        
        <!-- Quick Action Card -->
        <div class="craft-card rounded-2xl p-6 border border-white/10">
            <h2 class="text-sm font-bold text-white mb-4 tracking-tight">Aksi Cepat</h2>
            <div class="grid grid-cols-2 gap-2.5 text-xs">
                <a href="projects.php#projectFormSection" class="p-3 rounded-xl bg-zinc-900/70 hover:bg-zinc-800/80 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white transition flex flex-col items-center justify-center text-center gap-1.5">
                    <i class="fa-solid fa-folder-plus text-base text-indigo-400"></i>
                    <span class="font-medium">Tambah Proyek</span>
                </a>
                <a href="skills.php" class="p-3 rounded-xl bg-zinc-900/70 hover:bg-zinc-800/80 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white transition flex flex-col items-center justify-center text-center gap-1.5">
                    <i class="fa-solid fa-code-merge text-base text-sky-400"></i>
                    <span class="font-medium">Tambah Skill</span>
                </a>
                <a href="testimonials.php" class="p-3 rounded-xl bg-zinc-900/70 hover:bg-zinc-800/80 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white transition flex flex-col items-center justify-center text-center gap-1.5">
                    <i class="fa-solid fa-comment-medical text-base text-emerald-400"></i>
                    <span class="font-medium">Tambah Ulasan</span>
                </a>
                <a href="settings.php" class="p-3 rounded-xl bg-zinc-900/70 hover:bg-zinc-800/80 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white transition flex flex-col items-center justify-center text-center gap-1.5">
                    <i class="fa-solid fa-sliders text-base text-amber-400"></i>
                    <span class="font-medium">Edit Profil</span>
                </a>
            </div>
        </div>

        <!-- Recent Projects List -->
        <div class="craft-card rounded-2xl p-6 border border-white/10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-white tracking-tight">Proyek Terbaru</h2>
                <a href="projects.php" class="text-xs text-zinc-400 hover:text-white font-medium transition">Semua &rarr;</a>
            </div>

            <div class="space-y-2.5">
                <?php if (empty($recent_projects)): ?>
                    <p class="text-xs text-zinc-500 py-3 text-center">Belum ada proyek.</p>
                <?php else: ?>
                    <?php foreach ($recent_projects as $p): ?>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-900/40 border border-white/5 hover:border-white/10 transition">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <img src="<?= sanitize($p['image_url']) ?>" alt="<?= sanitize($p['title']) ?>" class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-white/10">
                            <div class="truncate">
                                <h4 class="text-xs font-bold text-white truncate"><?= sanitize($p['title']) ?></h4>
                                <span class="text-[10px] text-zinc-400 font-mono"><?= sanitize($p['category']) ?></span>
                            </div>
                        </div>
                        <a href="projects.php?edit=<?= $p['id'] ?>" class="text-zinc-400 hover:text-white p-1.5 rounded-lg hover:bg-white/5 transition text-xs" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
