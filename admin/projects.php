<?php
/**
 * CRUD Projects Management
 * Modern Minimalist Styling Matching Main Portfolio
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$project_to_edit = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$edit_id]);
    $project_to_edit = $stmt->fetch();
}

// Handle POST actions (Create, Update, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Sesi tidak valid.');
        header("Location: projects.php");
        exit;
    }

    $action = $_POST['action'] ?? '';

    // DELETE Action
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Proyek berhasil dihapus!');
        }
        header("Location: projects.php");
        exit;
    }

    // SAVE Action (Create or Update)
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'Web App');
        $description = trim($_POST['description'] ?? '');
        $technologies = trim($_POST['technologies'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $demo_url = trim($_POST['demo_url'] ?? '#');
        $github_url = trim($_POST['github_url'] ?? '#');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // Handle Image Upload if provided
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = $_FILES['image_file']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($file_ext, $allowed)) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = 'project_' . time() . '_' . uniqid() . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $image_url = 'uploads/' . $new_filename;
                }
            }
        }

        if (empty($image_url)) {
            $image_url = 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80';
        }

        if (empty($title) || empty($description)) {
            set_flash_message('warning', 'Judul dan deskripsi proyek wajib diisi!');
            header("Location: projects.php" . ($id > 0 ? "?edit=$id" : ""));
            exit;
        }

        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, category = ?, description = ?, technologies = ?, image_url = ?, demo_url = ?, github_url = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([$title, $category, $description, $technologies, $image_url, $demo_url, $github_url, $is_featured, $id]);
            set_flash_message('success', 'Data proyek berhasil diperbarui!');
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO projects (title, category, description, technologies, image_url, demo_url, github_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $description, $technologies, $image_url, $demo_url, $github_url, $is_featured]);
            set_flash_message('success', 'Proyek baru berhasil ditambahkan!');
        }

        header("Location: projects.php");
        exit;
    }
}

// Fetch all projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/admin-header.php';
?>

<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Kelola Portofolio Proyek</h1>
        <p class="text-xs text-zinc-400 mt-1">Tambah, perbarui, dan atur proyek showcase portofolio Anda</p>
    </div>
    
    <?php if (!$project_to_edit): ?>
    <button onclick="document.getElementById('projectFormSection').scrollIntoView({behavior: 'smooth'})" class="px-4 py-2 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs transition flex items-center gap-2 shadow-sm w-fit">
        <i class="fa-solid fa-plus text-[11px]"></i> Tambah Proyek Baru
    </button>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Projects Table List (7 cols) -->
    <div class="lg:col-span-7 craft-card rounded-2xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-indigo-400"></i> Daftar Proyek (<?= count($projects) ?>)
            </h2>
        </div>

        <?php if (empty($projects)): ?>
            <div class="text-center py-12 text-zinc-500 text-xs">
                <i class="fa-regular fa-folder-open text-3xl mb-2 block text-zinc-600"></i>
                Belum ada data proyek. Gunakan formulir di sebelah kanan untuk menambahkan proyek pertama Anda.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="text-[10px] uppercase font-mono tracking-wider bg-white/[0.02] text-zinc-400 border-b border-white/10">
                        <tr>
                            <th class="py-3 px-3">Proyek</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Teknologi</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($projects as $p): ?>
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <img src="<?= sanitize($p['image_url']) ?>" alt="" class="w-11 h-9 rounded-lg object-cover border border-white/10 flex-shrink-0">
                                    <div class="max-w-[180px]">
                                        <div class="font-bold text-white text-xs truncate"><?= sanitize($p['title']) ?></div>
                                        <?php if ($p['is_featured']): ?>
                                            <span class="text-[10px] text-amber-400 font-mono inline-flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-star text-[8px]"></i> Featured
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="tech-pill">
                                    <?= sanitize($p['category']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 text-[11px] text-zinc-400 font-mono max-w-[140px] truncate">
                                <?= sanitize($p['technologies']) ?>
                            </td>
                            <td class="py-3 px-3 text-right space-x-1.5 whitespace-nowrap">
                                <a href="projects.php?edit=<?= $p['id'] ?>" class="p-1.5 rounded-lg bg-white/5 text-zinc-300 hover:text-white hover:bg-white/10 border border-white/10 transition inline-block text-xs" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="projects.php" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 text-rose-300 hover:text-rose-100 hover:bg-rose-500/20 border border-rose-500/20 transition cursor-pointer text-xs" title="Hapus">
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
    <div id="projectFormSection" class="lg:col-span-5 craft-card rounded-2xl p-6 border border-white/10 h-fit">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <h2 class="text-sm font-bold text-white flex items-center gap-2 tracking-tight">
                <i class="fa-solid <?= $project_to_edit ? 'fa-pen-to-square text-amber-400' : 'fa-plus text-indigo-400' ?>"></i>
                <?= $project_to_edit ? 'Edit Data Proyek' : 'Tambah Proyek Baru' ?>
            </h2>
            <?php if ($project_to_edit): ?>
                <a href="projects.php" class="text-xs text-zinc-400 hover:text-white transition">Batal Edit</a>
            <?php endif; ?>
        </div>

        <form action="projects.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $project_to_edit ? $project_to_edit['id'] : 0 ?>">

            <div>
                <label for="title" class="block font-semibold text-zinc-300 mb-1.5">Judul Proyek <span class="text-rose-400">*</span></label>
                <input type="text" id="title" name="title" required value="<?= $project_to_edit ? sanitize($project_to_edit['title']) : '' ?>" placeholder="Contoh: Sistem POS Kasir Apotek" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="category" class="block font-semibold text-zinc-300 mb-1.5">Kategori</label>
                    <select id="category" name="category" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                        <?php 
                        $cats = ['Web App', 'E-Commerce', 'Landing Page', 'Mobile', 'UI/UX'];
                        $current_cat = $project_to_edit ? $project_to_edit['category'] : 'Web App';
                        foreach ($cats as $c): 
                        ?>
                            <option value="<?= $c ?>" <?= $current_cat === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="technologies" class="block font-semibold text-zinc-300 mb-1.5">Teknologi</label>
                    <input type="text" id="technologies" name="technologies" value="<?= $project_to_edit ? sanitize($project_to_edit['technologies']) : 'PHP, MySQL, Tailwind CSS' ?>" placeholder="PHP, MySQL, JS" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div>
                <label for="description" class="block font-semibold text-zinc-300 mb-1.5">Deskripsi Proyek <span class="text-rose-400">*</span></label>
                <textarea id="description" name="description" rows="3" required placeholder="Jelaskan fitur utama, peran Anda, dan hasil yang dicapai..." class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition resize-none"><?= $project_to_edit ? sanitize($project_to_edit['description']) : '' ?></textarea>
            </div>

            <div>
                <label for="image_url" class="block font-semibold text-zinc-300 mb-1.5">URL Gambar Thumbnail</label>
                <input type="text" id="image_url" name="image_url" value="<?= $project_to_edit ? sanitize($project_to_edit['image_url']) : '' ?>" placeholder="https://images.unsplash.com/... atau gunakan upload di bawah" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="image_file" class="block font-semibold text-zinc-300 mb-1.5">Upload Gambar dari Komputer</label>
                <input type="file" id="image_file" name="image_file" accept="image/*" class="w-full text-zinc-400 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-white/10 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-200 hover:file:bg-zinc-700 cursor-pointer">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="demo_url" class="block font-semibold text-zinc-300 mb-1.5">Link Live Demo</label>
                    <input type="text" id="demo_url" name="demo_url" value="<?= $project_to_edit ? sanitize($project_to_edit['demo_url']) : '#' ?>" placeholder="https://demo.example.com" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="github_url" class="block font-semibold text-zinc-300 mb-1.5">Link GitHub Repo</label>
                    <input type="text" id="github_url" name="github_url" value="<?= $project_to_edit ? sanitize($project_to_edit['github_url']) : '#' ?>" placeholder="https://github.com/..." class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-zinc-300 font-medium">
                    <input type="checkbox" name="is_featured" value="1" <?= (!$project_to_edit || $project_to_edit['is_featured']) ? 'checked' : '' ?> class="rounded border-white/10 text-indigo-600 focus:ring-indigo-500 w-4 h-4 bg-zinc-950">
                    <span>Tampilkan sebagai Proyek Unggulan (Featured)</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs tracking-tight transition flex items-center justify-center gap-2 cursor-pointer shadow-sm mt-3">
                <i class="fa-solid <?= $project_to_edit ? 'fa-floppy-disk' : 'fa-plus' ?> text-[11px]"></i>
                <?= $project_to_edit ? 'Perbarui Proyek' : 'Simpan Proyek Baru' ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
