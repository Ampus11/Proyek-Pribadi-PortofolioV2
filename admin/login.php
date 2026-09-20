<?php
/**
 * Admin Login Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error_message = 'Sesi form telah kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error_message = 'Username dan Password wajib diisi!';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Login successful!
                    $_SESSION['admin_user_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_full_name'] = $user['full_name'];

                    set_flash_message('success', 'Selamat datang kembali, ' . $user['full_name'] . '!');
                    header("Location: index.php");
                    exit;
                } else {
                    $error_message = 'Username atau Password salah!';
                }
            } catch (PDOException $e) {
                $error_message = 'Terjadi kesalahan sistem: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full font-sans text-slate-100 flex items-center justify-center p-4 bg-slate-950 relative overflow-hidden">

    <!-- Ambient glow -->
    <div class="absolute w-96 h-96 bg-blue-600/20 -top-20 -left-20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute w-96 h-96 bg-purple-600/20 -bottom-20 -right-20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo & Heading -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-blue-500/25 mx-auto mb-4">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Login Administrator</h1>
            <p class="text-xs text-slate-400 mt-1">Masuk untuk mengelola konten dan portofolio Anda</p>
        </div>

        <!-- Card Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-md">
            
            <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0"></i>
                <span><?= sanitize($error_message) ?></span>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-300 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username" value="<?= isset($_POST['username']) ? sanitize($_POST['username']) : 'admin' ?>" class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500 text-xs">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password" value="admin123" class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs tracking-wider uppercase transition shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard
                </button>
            </form>

            <!-- Default Credential Helper -->
            <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                <p class="text-[11px] text-slate-400 mb-1">Kredensial Default:</p>
                <div class="inline-flex items-center gap-2 bg-slate-950 px-3 py-1.5 rounded-lg border border-slate-800 text-[11px] font-mono text-slate-300">
                    <span>User: <strong class="text-blue-400">admin</strong></span>
                    <span class="text-slate-600">|</span>
                    <span>Pass: <strong class="text-blue-400">admin123</strong></span>
                </div>
            </div>
        </div>

        <!-- Back to Website Link -->
        <div class="text-center mt-6">
            <a href="../index.php" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Utama Portofolio
            </a>
        </div>
    </div>

</body>
</html>
