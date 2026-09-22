<?php
/**
 * Admin Login Page
 * Modern Minimalist Dark Design Matching Main Portfolio
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header("Location: index.php");
    exit;
}

$site_name = get_setting($pdo, 'full_name', 'Rizky Pratama');
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
<html lang="id" class="h-full bg-[#090a0f]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — <?= sanitize($site_name) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="h-full font-sans text-zinc-100 flex items-center justify-center p-4 bg-[#090a0f] relative overflow-hidden antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Canvas Lighting -->
    <div class="canvas-grid fixed inset-0 pointer-events-none z-0"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo & Heading -->
        <div class="text-center mb-6">
            <div class="w-11 h-11 rounded-xl bg-zinc-800 border border-white/15 flex items-center justify-center text-white font-mono font-bold text-base shadow-sm mx-auto mb-3">
                <?= strtoupper(substr($site_name, 0, 1)) ?>
            </div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-zinc-900 border border-white/10 text-[11px] text-zinc-400 font-mono mb-2">
                <span class="status-indicator">
                    <span class="status-glow"></span>
                    <span class="status-dot"></span>
                </span>
                <span>Protected Admin Area</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Login Administrator</h1>
            <p class="text-xs text-zinc-400 mt-1">Masuk untuk mengelola data portofolio &amp; pesan klien</p>
        </div>

        <!-- Card Container -->
        <div class="craft-card rounded-2xl p-7 sm:p-8 border border-white/10 shadow-2xl backdrop-blur-xl">
            
            <?php if (!empty($error_message)): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-sm flex-shrink-0 text-rose-400"></i>
                <span><?= sanitize($error_message) ?></span>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div>
                    <label for="username" class="block text-xs font-semibold text-zinc-300 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username" value="<?= isset($_POST['username']) ? sanitize($_POST['username']) : 'admin' ?>" class="w-full pl-9 pr-3.5 py-2.5 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password" value="admin123" class="w-full pl-9 pr-3.5 py-2.5 bg-[#090a0f] border border-white/10 rounded-xl text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white text-zinc-950 hover:bg-zinc-200 font-semibold text-xs tracking-tight transition flex items-center justify-center gap-2 cursor-pointer shadow-sm mt-2">
                    <i class="fa-solid fa-right-to-bracket text-[11px]"></i> Masuk ke Dashboard
                </button>
            </form>

            <!-- Default Credential Helper -->
            <div class="mt-6 pt-5 border-t border-white/10 text-center">
                <p class="text-[11px] text-zinc-500 font-mono mb-2">Kredensial Default Demo:</p>
                <div class="inline-flex items-center gap-2 bg-zinc-900 px-3 py-1.5 rounded-lg border border-white/10 text-[11px] font-mono text-zinc-300">
                    <span>User: <strong class="text-white">admin</strong></span>
                    <span class="text-zinc-600">&bull;</span>
                    <span>Pass: <strong class="text-white">admin123</strong></span>
                </div>
            </div>
        </div>

        <!-- Back to Website Link -->
        <div class="text-center mt-5">
            <a href="../index.php" class="text-xs text-zinc-400 hover:text-white transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Kembali ke Halaman Utama Portofolio
            </a>
        </div>
    </div>

</body>
</html>
