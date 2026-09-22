<?php
/**
 * Messages Inbox Management
 * Modern Minimalist Styling Matching Main Portfolio
 */
require_once __DIR__ . '/admin-header.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Sesi tidak valid.');
        header("Location: messages.php");
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Pesan berhasil dihapus!');
        } elseif ($action === 'toggle_read') {
            $current_read = (int)($_POST['current_read'] ?? 0);
            $new_read = $current_read ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE messages SET is_read = ? WHERE id = ?");
            $stmt->execute([$new_read, $id]);
            set_flash_message('success', $new_read ? 'Pesan ditandai sebagai sudah dibaca.' : 'Pesan ditandai sebagai belum dibaca.');
        }
    }

    header("Location: messages.php");
    exit;
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Kotak Pesan Masuk</h1>
        <p class="text-xs text-zinc-400 mt-1">Daftar pesan dan formulir kontak yang dikirim oleh pengunjung website</p>
    </div>
</div>

<div class="craft-card rounded-2xl p-6 border border-white/10">
    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
        <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-inbox text-indigo-400"></i> Semua Pesan (<?= count($messages) ?>)
        </h2>
    </div>

    <?php if (empty($messages)): ?>
        <div class="text-center py-16 text-zinc-500 text-xs">
            <i class="fa-regular fa-envelope-open text-4xl mb-3 block text-zinc-600"></i>
            Belum ada pesan yang masuk dari pengunjung.
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($messages as $msg): ?>
            <div class="p-5 rounded-xl transition border <?= $msg['is_read'] ? 'bg-zinc-900/30 border-white/5 opacity-80' : 'bg-[#12141e] border-indigo-500/30 shadow-lg shadow-indigo-500/5' ?>">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl <?= $msg['is_read'] ? 'bg-zinc-800 text-zinc-400 border border-white/5' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' ?> flex items-center justify-center text-xs font-mono font-bold flex-shrink-0">
                            <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-white"><?= sanitize($msg['name']) ?></h4>
                                <?php if (!$msg['is_read']): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold">Baru</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[11px] text-zinc-400 font-mono"><?= sanitize($msg['email']) ?></p>
                        </div>
                    </div>

                    <div class="text-xs text-zinc-500 font-mono">
                        <i class="fa-regular fa-clock text-[10px] mr-1"></i> <?= format_date_id($msg['created_at']) ?>
                    </div>
                </div>

                <div class="mb-4 pl-0 sm:pl-12">
                    <div class="text-xs font-semibold text-zinc-200 mb-1.5">
                        Subjek: <span class="text-white"><?= sanitize($msg['subject']) ?></span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-black/40 border border-white/5 text-xs text-zinc-300 leading-relaxed whitespace-pre-line font-sans">
                        <?= sanitize($msg['message']) ?>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 pl-0 sm:pl-12 pt-3 border-t border-white/5 text-xs">
                    <!-- Quick Reply by Mail -->
                    <a href="mailto:<?= sanitize($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject']) ?>" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-zinc-200 border border-white/10 transition flex items-center gap-1.5 font-medium text-xs">
                        <i class="fa-solid fa-reply text-[10px]"></i> Balas Email
                    </a>

                    <!-- Toggle Read/Unread -->
                    <form action="messages.php" method="POST" class="inline-block">
                        <input type="hidden" name="action" value="toggle_read">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <input type="hidden" name="current_read" value="<?= $msg['is_read'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-white/10 transition flex items-center gap-1.5 text-xs cursor-pointer">
                            <i class="fa-regular <?= $msg['is_read'] ? 'fa-envelope' : 'fa-envelope-open' ?> text-[10px]"></i>
                            <span><?= $msg['is_read'] ? 'Tandai Belum Dibaca' : 'Tandai Sudah Dibaca' ?></span>
                        </button>
                    </form>

                    <!-- Delete Message -->
                    <form action="messages.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-500/10 text-rose-300 hover:text-rose-100 hover:bg-rose-500/20 border border-rose-500/20 transition flex items-center gap-1.5 text-xs cursor-pointer">
                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
