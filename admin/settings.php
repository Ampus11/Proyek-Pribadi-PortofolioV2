<?php
/**
 * Settings & Profile Management
 * Modern Minimalist Styling Matching Main Portfolio
 */
require_once __DIR__ . '/admin-header.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        set_flash_message('danger', 'Sesi tidak valid.');
        header("Location: settings.php");
        exit;
    }

    $form_type = $_POST['form_type'] ?? '';

    // 1. UPDATE PROFILE SETTINGS
    if ($form_type === 'profile') {
        $keys = [
            'full_name', 'job_title', 'hero_greeting', 'bio_summary',
            'about_description', 'email', 'phone', 'location',
            'years_experience', 'completed_projects', 'happy_clients',
            'github_url', 'linkedin_url', 'instagram_url'
        ];

        $stmt = $pdo->prepare("INSERT INTO profile_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

        foreach ($keys as $k) {
            $val = trim($_POST[$k] ?? '');
            $stmt->execute([$k, $val]);
        }

        // Also update session admin full name if full_name changed
        if (!empty($_POST['full_name'])) {
            $_SESSION['admin_full_name'] = trim($_POST['full_name']);
        }

        set_flash_message('success', 'Pengaturan profil berhasil diperbarui!');
        header("Location: settings.php");
        exit;
    }

    // 2. CHANGE ADMIN PASSWORD
    if ($form_type === 'password') {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
            set_flash_message('warning', 'Semua kolom sandi wajib diisi!');
            header("Location: settings.php");
            exit;
        }

        if ($new_pass !== $confirm_pass) {
            set_flash_message('warning', 'Konfirmasi kata sandi baru tidak cocok!');
            header("Location: settings.php");
            exit;
        }

        if (strlen($new_pass) < 6) {
            set_flash_message('warning', 'Kata sandi baru minimal 6 karakter!');
            header("Location: settings.php");
            exit;
        }

        $admin_id = $_SESSION['admin_user_id'];
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$admin_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_pass, $user['password'])) {
            $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->execute([$new_hash, $admin_id]);
            set_flash_message('success', 'Kata sandi administrator berhasil diubah!');
        } else {
            set_flash_message('danger', 'Kata sandi saat ini salah!');
        }

        header("Location: settings.php");
        exit;
    }
}

// Fetch current settings
$full_name = get_setting($pdo, 'full_name', '');
$job_title = get_setting($pdo, 'job_title', '');
$hero_greeting = get_setting($pdo, 'hero_greeting', '');
$bio_summary = get_setting($pdo, 'bio_summary', '');
$about_description = get_setting($pdo, 'about_description', '');
$email = get_setting($pdo, 'email', '');
$phone = get_setting($pdo, 'phone', '');
$location = get_setting($pdo, 'location', '');
$years_experience = get_setting($pdo, 'years_experience', '');
$completed_projects = get_setting($pdo, 'completed_projects', '');
$happy_clients = get_setting($pdo, 'happy_clients', '');
$github_url = get_setting($pdo, 'github_url', '');
$linkedin_url = get_setting($pdo, 'linkedin_url', '');
$instagram_url = get_setting($pdo, 'instagram_url', '');
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Pengaturan Profil &amp; Akun</h1>
        <p class="text-xs text-zinc-400 mt-1">Ubah identitas, biografi, tautan sosial, dan keamanan akun Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Profile Info Form (8 cols) -->
    <div class="lg:col-span-8 craft-card rounded-2xl p-6 sm:p-8 border border-white/10">
        <h2 class="text-sm font-bold text-white mb-6 pb-4 border-b border-white/10 flex items-center gap-2 tracking-tight">
            <i class="fa-solid fa-user-pen text-indigo-400"></i> Informasi Data Diri &amp; Bio
        </h2>

        <form action="settings.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="form_type" value="profile">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="full_name" class="block font-semibold text-zinc-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="<?= sanitize($full_name) ?>" required class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="job_title" class="block font-semibold text-zinc-300 mb-1.5">Profesi / Headline</label>
                    <input type="text" id="job_title" name="job_title" value="<?= sanitize($job_title) ?>" required class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div>
                <label for="hero_greeting" class="block font-semibold text-zinc-300 mb-1.5">Salam Pembuka Hero</label>
                <input type="text" id="hero_greeting" name="hero_greeting" value="<?= sanitize($hero_greeting) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="bio_summary" class="block font-semibold text-zinc-300 mb-1.5">Ringkasan Singkat (Hero Section)</label>
                <textarea id="bio_summary" name="bio_summary" rows="2" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition resize-none"><?= sanitize($bio_summary) ?></textarea>
            </div>

            <div>
                <label for="about_description" class="block font-semibold text-zinc-300 mb-1.5">Deskripsi Lengkap (About Me)</label>
                <textarea id="about_description" name="about_description" rows="4" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition resize-none"><?= sanitize($about_description) ?></textarea>
            </div>

            <!-- Contacts -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div>
                    <label for="email" class="block font-semibold text-zinc-300 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="<?= sanitize($email) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="phone" class="block font-semibold text-zinc-300 mb-1.5">Nomor Telepon / WA</label>
                    <input type="text" id="phone" name="phone" value="<?= sanitize($phone) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="location" class="block font-semibold text-zinc-300 mb-1.5">Kota / Lokasi</label>
                    <input type="text" id="location" name="location" value="<?= sanitize($location) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <!-- Stats Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="years_experience" class="block font-semibold text-zinc-300 mb-1.5">Tahun Pengalaman</label>
                    <input type="text" id="years_experience" name="years_experience" value="<?= sanitize($years_experience) ?>" placeholder="3+" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition font-mono">
                </div>
                <div>
                    <label for="completed_projects" class="block font-semibold text-zinc-300 mb-1.5">Proyek Selesai</label>
                    <input type="text" id="completed_projects" name="completed_projects" value="<?= sanitize($completed_projects) ?>" placeholder="28+" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition font-mono">
                </div>
                <div>
                    <label for="happy_clients" class="block font-semibold text-zinc-300 mb-1.5">Klien Puas</label>
                    <input type="text" id="happy_clients" name="happy_clients" value="<?= sanitize($happy_clients) ?>" placeholder="20+" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition font-mono">
                </div>
            </div>

            <!-- Social Media -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="github_url" class="block font-semibold text-zinc-300 mb-1.5"><i class="fa-brands fa-github text-zinc-400 mr-1"></i> GitHub URL</label>
                    <input type="text" id="github_url" name="github_url" value="<?= sanitize($github_url) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="linkedin_url" class="block font-semibold text-zinc-300 mb-1.5"><i class="fa-brands fa-linkedin text-sky-400 mr-1"></i> LinkedIn URL</label>
                    <input type="text" id="linkedin_url" name="linkedin_url" value="<?= sanitize($linkedin_url) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div>
                    <label for="instagram_url" class="block font-semibold text-zinc-300 mb-1.5"><i class="fa-brands fa-instagram text-rose-400 mr-1"></i> Instagram URL</label>
                    <input type="text" id="instagram_url" name="instagram_url" value="<?= sanitize($instagram_url) ?>" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs tracking-tight transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-[11px]"></i> Simpan Pengaturan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- Security / Change Password Form (4 cols) -->
    <div class="lg:col-span-4 craft-card rounded-2xl p-6 sm:p-8 border border-white/10 h-fit">
        <h2 class="text-sm font-bold text-white mb-6 pb-4 border-b border-white/10 flex items-center gap-2 tracking-tight">
            <i class="fa-solid fa-key text-amber-400"></i> Ubah Kata Sandi
        </h2>

        <form action="settings.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="form_type" value="password">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div>
                <label for="current_password" class="block font-semibold text-zinc-300 mb-1.5">Kata Sandi Saat Ini <span class="text-rose-400">*</span></label>
                <input type="password" id="current_password" name="current_password" required placeholder="Masukkan kata sandi lama" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="new_password" class="block font-semibold text-zinc-300 mb-1.5">Kata Sandi Baru <span class="text-rose-400">*</span></label>
                <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <div>
                <label for="confirm_password" class="block font-semibold text-zinc-300 mb-1.5">Konfirmasi Kata Sandi Baru <span class="text-rose-400">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2 bg-[#090a0f] border border-white/10 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs tracking-tight transition shadow-sm flex items-center justify-center gap-2 cursor-pointer mt-3">
                <i class="fa-solid fa-lock text-[11px]"></i> Perbarui Kata Sandi
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>
