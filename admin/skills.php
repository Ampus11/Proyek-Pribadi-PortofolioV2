<?php
/**
 * CRUD Skills Management
 */
require_once __DIR__ . '/admin-header.php';

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$skill_to_edit = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$edit_id]);
    $skill_to_edit = $stmt->fetch();
}

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Sesi tidak valid.');
        header("Location: skills.php");
        exit;
    }

    $action = $_POST['action'] ?? '';

    // DELETE Action
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Keahlian berhasil dihapus!');
        }
        header("Location: skills.php");
        exit;
    }

    // SAVE Action
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'Frontend');
        $proficiency = min(100, max(1, (int)($_POST['proficiency'] ?? 80)));
        $icon_class = trim($_POST['icon_class'] ?? 'fa-solid fa-code');

        if (empty($name)) {
            set_flash_message('warning', 'Nama keahlian wajib diisi!');
            header("Location: skills.php" . ($id > 0 ? "?edit=$id" : ""));
            exit;
        }

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, proficiency = ?, icon_class = ? WHERE id = ?");
            $stmt->execute([$name, $category, $proficiency, $icon_class, $id]);
            set_flash_message('success', 'Data keahlian berhasil diperbarui!');
        } else {
            $stmt = $pdo->prepare("INSERT INTO skills (name, category, proficiency, icon_class) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $category, $proficiency, $icon_class]);
            set_flash_message('success', 'Keahlian baru berhasil ditambahkan!');
        }

        header("Location: skills.php");
        exit;
    }
}

// Fetch all skills
$skills = $pdo->query("SELECT * FROM skills ORDER BY category ASC, proficiency DESC")->fetchAll();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Keahlian (Skills)</h1>
        <p class="text-xs text-slate-400 mt-1">Atur daftar teknologi, bahasa pemrograman, dan alat yang Anda kuasai</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Skills List (7 cols) -->
    <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-code text-purple-400"></i> Daftar Keahlian (<?= count($skills) ?>)
        </h2>

        <?php if (empty($skills)): ?>
            <div class="text-center py-12 text-slate-500 text-xs">
                Belum ada data keahlian. Gunakan form di sebelah kanan untuk menambahkan skill baru.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase bg-slate-950/60 text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Keahlian</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Tingkat Kemahiran</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($skills as $s): ?>
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-3 font-semibold text-white flex items-center gap-2.5">
                                <i class="<?= sanitize($s['icon_class'] ?: 'fa-solid fa-code') ?> text-purple-400 text-sm"></i>
                                <?= sanitize($s['name']) ?>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 text-[10px]">
                                    <?= sanitize($s['category']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: <?= (int)$s['proficiency'] ?>%"></div>
                                    </div>
                                    <span class="font-mono text-[11px] text-purple-300"><?= (int)$s['proficiency'] ?>%</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-right space-x-2 whitespace-nowrap">
                                <a href="skills.php?edit=<?= $s['id'] ?>" class="p-1.5 rounded-lg bg-blue-600/10 text-blue-400 hover:bg-blue-600 hover:text-white transition inline-block" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="skills.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus keahlian ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white transition cursor-pointer" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form Section (5 cols) -->
    <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl h-fit">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid <?= $skill_to_edit ? 'fa-pen-to-square text-amber-400' : 'fa-plus text-purple-400' ?>"></i>
                <?= $skill_to_edit ? 'Edit Keahlian' : 'Tambah Keahlian Baru' ?>
            </h2>
            <?php if ($skill_to_edit): ?>
                <a href="skills.php" class="text-xs text-slate-400 hover:text-white">Batal Edit</a>
            <?php endif; ?>
        </div>

        <form action="skills.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $skill_to_edit ? $skill_to_edit['id'] : 0 ?>">

            <div>
                <label for="name" class="block font-semibold text-slate-300 mb-1.5">Nama Keahlian <span class="text-rose-400">*</span></label>
                <input type="text" id="name" name="name" required value="<?= $skill_to_edit ? sanitize($skill_to_edit['name']) : '' ?>" placeholder="Contoh: PHP (OOP &amp; PDO)" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label for="category" class="block font-semibold text-slate-300 mb-1.5">Kategori</label>
                <select id="category" name="category" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <?php 
                    $cats = ['Frontend', 'Backend', 'Database & Tools'];
                    $current_cat = $skill_to_edit ? $skill_to_edit['category'] : 'Frontend';
                    foreach ($cats as $c): 
                    ?>
                        <option value="<?= $c ?>" <?= $current_cat === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="proficiency" class="font-semibold text-slate-300">Tingkat Kemahiran (%)</label>
                    <span id="profVal" class="text-purple-400 font-mono font-bold"><?= $skill_to_edit ? (int)$skill_to_edit['proficiency'] : 85 ?>%</span>
                </div>
                <input type="range" id="proficiency" name="proficiency" min="10" max="100" value="<?= $skill_to_edit ? (int)$skill_to_edit['proficiency'] : 85 ?>" oninput="document.getElementById('profVal').innerText = this.value + '%'" class="w-full accent-purple-500 bg-slate-950 cursor-pointer">
            </div>

            <div>
                <label for="icon_class" class="block font-semibold text-slate-300 mb-1.5">FontAwesome Icon Class</label>
                <input type="text" id="icon_class" name="icon_class" value="<?= $skill_to_edit ? sanitize($skill_to_edit['icon_class']) : 'fa-brands fa-php' ?>" placeholder="fa-brands fa-php" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
                <p class="text-[10px] text-slate-500 mt-1">Gunakan class icon dari fontawesome.com (misal: fa-brands fa-js, fa-solid fa-database)</p>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold tracking-wide transition shadow-lg shadow-purple-500/25 flex items-center justify-center gap-2 cursor-pointer mt-4">
                <i class="fa-solid <?= $skill_to_edit ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
                <?= $skill_to_edit ? 'Perbarui Keahlian' : 'Simpan Keahlian Baru' ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
