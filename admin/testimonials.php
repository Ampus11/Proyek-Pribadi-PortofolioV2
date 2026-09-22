<?php
/**
 * CRUD Testimonials Management
 * Modern Minimalist Styling Matching Main Portfolio
 */
require_once __DIR__ . '/admin-header.php';

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$testi_to_edit = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$edit_id]);
    $testi_to_edit = $stmt->fetch();
}

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Sesi tidak valid.');
        header("Location: testimonials.php");
        exit;
    }

    $action = $_POST['action'] ?? '';

    // DELETE
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Testimoni berhasil dihapus!');
        }
        header("Location: testimonials.php");
        exit;
    }

    // SAVE
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $rating = min(5, max(1, (int)($_POST['rating'] ?? 5)));
        $avatar_url = trim($_POST['avatar_url'] ?? '');

        if (empty($avatar_url)) {
            $avatar_url = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80';
        }

        if (empty($name) || empty($message)) {
            set_flash_message('warning', 'Nama klien dan isi ulasan wajib diisi!');
            header("Location: testimonials.php" . ($id > 0 ? "?edit=$id" : ""));
            exit;
        }

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, role = ?, company = ?, message = ?, rating = ?, avatar_url = ? WHERE id = ?");
            $stmt->execute([$name, $role, $company, $message, $rating, $avatar_url, $id]);
            set_flash_message('success', 'Data testimoni berhasil diperbarui!');
        } else {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, role, company, message, rating, avatar_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $role, $company, $message, $rating, $avatar_url]);
            set_flash_message('success', 'Testimoni baru berhasil ditambahkan!');
        }

        header("Location: testimonials.php");
        exit;
    }
}

// Fetch all testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Kelola Testimoni Klien</h1>
        <p class="text-xs text-zinc-400 mt-1">Ulasan kepuasan, apresiasi, dan rekomendasi dari klien Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- List (7 cols) -->
    <div class="lg:col-span-7 craft-card rounded-2xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-comments text-indigo-400"></i> Daftar Testimoni (<?= count($testimonials) ?>)
            </h2>
        </div>

        <?php if (empty($testimonials)): ?>
            <div class="text-center py-12 text-zinc-500 text-xs">
                <i class="fa-regular fa-comment-dots text-3xl mb-2 block text-zinc-600"></i>
                Belum ada ulasan klien. Tambahkan ulasan baru menggunakan formulir di sebelah kanan.
            </div>
        <?php else: ?>
            <div class="space-y-3.5">
                <?php foreach ($testimonials as $t): ?>
                <div class="p-4 rounded-xl bg-zinc-900/40 border border-white/5 hover:border-white/10 flex flex-col justify-between gap-3 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="<?= sanitize($t['avatar_url']) ?>" alt="" class="w-9 h-9 rounded-full object-cover border border-white/10">
                            <div>
                                <h4 class="text-xs font-bold text-white"><?= sanitize($t['name']) ?></h4>
                                <p class="text-[11px] text-zinc-400"><?= sanitize($t['role']) ?> &bull; <?= sanitize($t['company']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-0.5 text-amber-400 text-[10px]">
                            <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                                <i class="fa-solid fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <p class="text-xs text-zinc-300 italic leading-relaxed">
                        "<?= sanitize($t['message']) ?>"
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/5">
                        <a href="testimonials.php?edit=<?= $t['id'] ?>" class="px-2.5 py-1 rounded-lg bg-white/5 text-zinc-300 hover:text-white hover:bg-white/10 border border-white/10 transition text-[11px]" title="Edit">
                            <i class="fa-solid fa-pen-to-square mr-1 text-[10px]"></i> Edit
                        </a>
                        <form action="testimonials.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus testimoni ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-300 hover:text-rose-100 hover:bg-rose-500/20 border border-rose-500/20 transition text-[11px] cursor-pointer" title="Hapus">
                                <i class="fa-solid fa-trash-can mr-1 text-[10px]"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form Section (5 cols) -->
    <div class="lg:col-span-5 craft-card rounded-2xl p-6 border border-white/10 h-fit">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <h2 class="text-sm font-bold text-white flex items-center gap-2 tracking-tight">
                <i class="fa-solid <?= $testi_to_edit ? 'fa-pen-to-square text-amber-400' : 'fa-plus text-indigo-400' ?>"></i>
                <?= $testi_to_edit ? 'Edit Testimoni' : 'Tambah Testimoni' ?>
            </h2>
            <?php if ($testi_to_edit): ?>
                <a href="testimonials.php" class="text-xs text-zinc-400 hover:text-white transition">Batal Edit</a>
            <?php endif; ?>
        </div>

        <form action="testimonials.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $testi_to_edit ? $testi_to_edit['id'] : 0 ?>">

            <div>
                <label for="name" class="block font-semibold text-zinc-300 mb-1.5">Nama Klien <span class="text-rose-400">*</span></label>
                <input type="text" id="name" name="name" required value="<?= $testi_to_edit ? sanitize($testi_to_edit['name']) : '' ?>" placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="role" class="block font-semibold text-zinc-300 mb-1.5">Jabatan / Role</label>
                    <input type="text" id="role" name="role" value="<?= $testi_to_edit ? sanitize($testi_to_edit['role']) : '' ?>" placeholder="Contoh: CEO &amp; Founder" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="company" class="block font-semibold text-zinc-300 mb-1.5">Perusahaan / Brand</label>
                    <input type="text" id="company" name="company" value="<?= $testi_to_edit ? sanitize($testi_to_edit['company']) : '' ?>" placeholder="Contoh: PT Sentosa Jaya" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div>
                <label for="rating" class="block font-semibold text-zinc-300 mb-1.5">Rating Bintang</label>
                <select id="rating" name="rating" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    <?php $curr_rate = $testi_to_edit ? (int)$testi_to_edit['rating'] : 5; ?>
                    <option value="5" <?= $curr_rate === 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 Bintang - Sempurna)</option>
                    <option value="4" <?= $curr_rate === 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 Bintang - Sangat Baik)</option>
                    <option value="3" <?= $curr_rate === 3 ? 'selected' : '' ?>>⭐⭐⭐ (3 Bintang - Cukup)</option>
                </select>
            </div>

            <div>
                <label for="avatar_url" class="block font-semibold text-zinc-300 mb-1.5">URL Foto Avatar</label>
                <input type="text" id="avatar_url" name="avatar_url" value="<?= $testi_to_edit ? sanitize($testi_to_edit['avatar_url']) : '' ?>" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="message" class="block font-semibold text-zinc-300 mb-1.5">Isi Ulasan / Testimoni <span class="text-rose-400">*</span></label>
                <textarea id="message" name="message" rows="3" required placeholder="Tuliskan testimoni yang diberikan oleh klien..." class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition resize-none"><?= $testi_to_edit ? sanitize($testi_to_edit['message']) : '' ?></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs tracking-tight transition flex items-center justify-center gap-2 cursor-pointer shadow-sm mt-3">
                <i class="fa-solid <?= $testi_to_edit ? 'fa-floppy-disk' : 'fa-plus' ?> text-[11px]"></i>
                <?= $testi_to_edit ? 'Perbarui Testimoni' : 'Simpan Testimoni Baru' ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
