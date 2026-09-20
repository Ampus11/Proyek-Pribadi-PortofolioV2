<?php
/**
 * Messages Inbox Management
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
        <h1 class="text-2xl font-extrabold text-white">Kotak Pesan Masuk</h1>
        <p class="text-xs text-slate-400 mt-1">Daftar pesan dan formulir kontak yang dikirim oleh pengunjung website</p>
    </div>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-inbox text-rose-400"></i> Semua Pesan (<?= count($messages) ?>)
        </h2>
    </div>

    <?php if (empty($messages)): ?>
        <div class="text-center py-16 text-slate-500 text-xs">
            <i class="fa-regular fa-envelope-open text-4xl mb-3 block text-slate-600"></i>
            Belum ada pesan yang masuk dari pengunjung.
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($messages as $msg): ?>
            <div class="p-5 rounded-2xl <?= $msg['is_read'] ? 'bg-slate-950/40 border-slate-800/80 opacity-80' : 'bg-slate-950 border-blue-500/40 shadow-lg shadow-blue-500/5' ?> border transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl <?= $msg['is_read'] ? 'bg-slate-800 text-slate-400' : 'bg-blue-600 text-white' ?> flex items-center justify-center text-xs font-bold flex-shrink-0">
                            <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-white"><?= sanitize($msg['name']) ?></h4>
                                <?php if (!$msg['is_read']): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 text-[10px] font-bold">Baru</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[11px] text-slate-400 font-mono"><?= sanitize($msg['email']) ?></p>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-mono">
                        <i class="fa-regular fa-clock text-[10px] mr-1"></i> <?= format_date_id($msg['created_at']) ?>
                    </div>
                </div>

                <div class="mb-4 pl-12">
                    <div class="text-xs font-semibold text-slate-200 mb-1">
                        Subjek: <span class="text-white"><?= sanitize($msg['subject']) ?></span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                        <?= sanitize($msg['message']) ?>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pl-12 pt-2 border-t border-slate-800/60 text-xs">
                    <!-- Quick Reply by Mail -->
                    <a href="mailto:<?= sanitize($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject']) ?>" class="px-3 py-1.5 rounded-lg bg-blue-600/10 text-blue-400 hover:bg-blue-600 hover:text-white transition flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-reply"></i> Balas Email
                    </a>

                    <!-- Toggle Read/Unread -->
                    <form action="messages.php" method="POST" class="inline-block">
                        <input type="hidden" name="action" value="toggle_read">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <input type="hidden" name="current_read" value="<?= $msg['is_read'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition flex items-center gap-1.5 font-medium cursor-pointer">
                            <i class="fa-solid <?= $msg['is_read'] ? 'fa-envelope' : 'fa-envelope-open' ?>"></i>
                            <?= $msg['is_read'] ? 'Tandai Belum Dibaca' : 'Tandai Sudah Dibaca' ?>
                        </button>
                    </form>

                    <!-- Delete -->
                    <form action="messages.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white transition flex items-center gap-1.5 font-medium cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
