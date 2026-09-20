<?php
/**
 * CRUD Testimonials Management
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
        <h1 class="text-2xl font-extrabold text-white">Kelola Testimoni Klien</h1>
        <p class="text-xs text-slate-400 mt-1">Ulasan kepuasan, apresiasi, dan rekomendasi dari klien Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- List (7 cols) -->
    <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-comments text-emerald-400"></i> Daftar Testimoni (<?= count($testimonials) ?>)
        </h2>

        <?php if (empty($testimonials)): ?>
            <div class="text-center py-12 text-slate-500 text-xs">
                Belum ada ulasan klien. Tambahkan ulasan baru menggunakan form di sebelah kanan.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($testimonials as $t): ?>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex flex-col justify-between gap-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="<?= sanitize($t['avatar_url']) ?>" alt="" class="w-10 h-10 rounded-full object-cover border border-slate-700">
                            <div>
                                <h4 class="text-xs font-bold text-white"><?= sanitize($t['name']) ?></h4>
                                <p class="text-[11px] text-slate-400"><?= sanitize($t['role']) ?> &bull; <?= sanitize($t['company']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-amber-400 text-xs">
                            <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                                <i class="fa-solid fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 italic">
                        "<?= sanitize($t['message']) ?>"
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800/80">
                        <a href="testimonials.php?edit=<?= $t['id'] ?>" class="px-2.5 py-1 rounded-lg bg-blue-600/10 text-blue-400 hover:bg-blue-600 hover:text-white transition text-[11px]" title="Edit">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                        </a>
                        <form action="testimonials.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus testimoni ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white transition text-[11px] cursor-pointer" title="Hapus">
                                <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form (5 cols) -->
    <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl h-fit">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid <?= $testi_to_edit ? 'fa-pen-to-square text-amber-400' : 'fa-plus text-emerald-400' ?>"></i>
                <?= $testi_to_edit ? 'Edit Testimoni' : 'Tambah Testimoni Baru' ?>
            </h2>
            <?php if ($testi_to_edit): ?>
                <a href="testimonials.php" class="text-xs text-slate-400 hover:text-white">Batal</a>
            <?php endif; ?>
        </div>

        <form action="testimonials.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $testi_to_edit ? $testi_to_edit['id'] : 0 ?>">

            <div>
                <label for="name" class="block font-semibold text-slate-300 mb-1.5">Nama Klien <span class="text-rose-400">*</span></label>
                <input type="text" id="name" name="name" required value="<?= $testi_to_edit ? sanitize($testi_to_edit['name']) : '' ?>" placeholder="Contoh: Sarah Angelina" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="role" class="block font-semibold text-slate-300 mb-1.5">Jabatan / Posisi</label>
                    <input type="text" id="role" name="role" value="<?= $testi_to_edit ? sanitize($testi_to_edit['role']) : '' ?>" placeholder="Co-Founder / PM" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="company" class="block font-semibold text-slate-300 mb-1.5">Perusahaan / Brand</label>
                    <input type="text" id="company" name="company" value="<?= $testi_to_edit ? sanitize($testi_to_edit['company']) : '' ?>" placeholder="PT Digital Solusi" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label for="rating" class="block font-semibold text-slate-300 mb-1.5">Rating Bintang (1 - 5)</label>
                <select id="rating" name="rating" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <?php for ($r = 5; $r >= 1; $r--): ?>
                        <option value="<?= $r ?>" <?= ($testi_to_edit && (int)$testi_to_edit['rating'] === $r) ? 'selected' : '' ?>>
                            <?= $r ?> Bintang <?= str_repeat('⭐', $r) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label for="message" class="block font-semibold text-slate-300 mb-1.5">Isi Ulasan <span class="text-rose-400">*</span></label>
                <textarea id="message" name="message" rows="3" required placeholder="Tuliskan testimoni atau review yang diberikan klien..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"><?= $testi_to_edit ? sanitize($testi_to_edit['message']) : '' ?></textarea>
            </div>

            <div>
                <label for="avatar_url" class="block font-semibold text-slate-300 mb-1.5">URL Foto Profil Klien</label>
                <input type="text" id="avatar_url" name="avatar_url" value="<?= $testi_to_edit ? sanitize($testi_to_edit['avatar_url']) : '' ?>" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold tracking-wide transition shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2 cursor-pointer mt-4">
                <i class="fa-solid <?= $testi_to_edit ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
                <?= $testi_to_edit ? 'Perbarui Testimoni' : 'Simpan Testimoni Baru' ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
