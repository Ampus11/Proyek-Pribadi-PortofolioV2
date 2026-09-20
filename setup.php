<?php
/**
 * One-Click Database Setup & System Diagnostic Tool
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_portofolio');

$message = null;
$message_type = null;

// Handle manual 1-Click Install or Re-install
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run_setup') {
    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Run SQL schema
        $sql_file = __DIR__ . '/database.sql';
        if (!file_exists($sql_file)) {
            throw new Exception("File database.sql tidak ditemukan di folder proyek!");
        }

        $sql = file_get_contents($sql_file);
        $pdo->exec($sql);

        $message = "Database 'db_portofolio' dan seluruh tabel serta data bawaan berhasil diinisialisasi!";
        $message_type = "success";
    } catch (Exception $e) {
        $message = "Gagal inisialisasi: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Check PHP version & extensions
$php_ok = version_compare(PHP_VERSION, '8.0.0', '>=');
$pdo_mysql_ok = extension_loaded('pdo_mysql');

// Check MySQL connection status
$mysql_connected = false;
$db_exists = false;
$tables_count = 0;
$conn_error = '';

try {
    $test_pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $mysql_connected = true;

    // Check if db_portofolio exists
    $stmt = $test_pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($stmt->fetch()) {
        $db_exists = true;
        $test_pdo->exec("USE `" . DB_NAME . "`");
        $tbl_stmt = $test_pdo->query("SHOW TABLES");
        $tables = $tbl_stmt->fetchAll(PDO::FETCH_COLUMN);
        $tables_count = count($tables);
    }
} catch (Exception $e) {
    $mysql_connected = false;
    $conn_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup &amp; Diagnostik Database - Portofolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-800">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-blue-500/25">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Setup &amp; Diagnostik Sistem Portofolio</h1>
                <p class="text-xs text-slate-400">Pemeriksaan lingkungan PHP, koneksi MySQL Laragon, dan struktur database</p>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-xl <?= $message_type === 'success' ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-300' : 'bg-rose-500/15 border-rose-500/30 text-rose-300' ?> border text-xs flex items-center gap-3">
                <i class="fa-solid <?= $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark' ?> text-lg"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- Diagnostic Items -->
        <div class="space-y-3 mb-8 text-xs">
            <!-- 1. PHP Version -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <span class="text-slate-300 flex items-center gap-2.5">
                    <i class="fa-brands fa-php text-indigo-400 text-base"></i> Versi PHP (>= 8.0)
                </span>
                <span class="font-mono font-semibold <?= $php_ok ? 'text-emerald-400' : 'text-rose-400' ?>">
                    <?= PHP_VERSION ?> <?= $php_ok ? '✔ Sesuai' : '✖ Perlu Update' ?>
                </span>
            </div>

            <!-- 2. PDO MySQL Extension -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <span class="text-slate-300 flex items-center gap-2.5">
                    <i class="fa-solid fa-puzzle-piece text-purple-400 text-base"></i> Ekstensi PDO MySQL
                </span>
                <span class="font-mono font-semibold <?= $pdo_mysql_ok ? 'text-emerald-400' : 'text-rose-400' ?>">
                    <?= $pdo_mysql_ok ? '✔ Aktif' : '✖ Tidak Aktif' ?>
                </span>
            </div>

            <!-- 3. MySQL Server Connection -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <span class="text-slate-300 flex items-center gap-2.5">
                    <i class="fa-solid fa-database text-blue-400 text-base"></i> Layanan Server MySQL (Port 3306)
                </span>
                <span class="font-mono font-semibold <?= $mysql_connected ? 'text-emerald-400' : 'text-rose-400' ?>">
                    <?= $mysql_connected ? '✔ Terhubung' : '✖ Belum Terhubung' ?>
                </span>
            </div>

            <!-- 4. Database Schema Status -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <span class="text-slate-300 flex items-center gap-2.5">
                    <i class="fa-solid fa-table-list text-amber-400 text-base"></i> Database `db_portofolio` &amp; Tabel
                </span>
                <span class="font-mono font-semibold <?= ($db_exists && $tables_count >= 5) ? 'text-emerald-400' : 'text-amber-400' ?>">
                    <?= $db_exists ? "✔ Ada ($tables_count tabel)" : '✖ Belum Dibuat' ?>
                </span>
            </div>
        </div>

        <?php if (!$mysql_connected): ?>
            <div class="bg-amber-950/30 border border-amber-800/60 rounded-2xl p-5 mb-8 text-xs text-amber-200 space-y-2">
                <h4 class="font-bold text-amber-400 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-lightbulb"></i> MySQL Belum Berjalan
                </h4>
                <p>Silakan buka Laragon, lalu klik <strong>Start All</strong>. Setelah itu klik tombol refresh di bawah ini.</p>
                <div class="bg-slate-950/80 p-3 rounded-lg border border-slate-800 font-mono text-[11px] text-slate-400 break-words">
                    <?= htmlspecialchars($conn_error) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Form -->
        <div class="space-y-3">
            <form action="setup.php" method="POST">
                <input type="hidden" name="action" value="run_setup">
                <button type="submit" <?= !$mysql_connected ? 'disabled' : '' ?> class="w-full py-3.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed text-white font-semibold text-xs tracking-wider uppercase transition shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> <?= $db_exists ? 'Reset / Perbarui Data Database (database.sql)' : 'Inisialisasi Database Sekarang' ?>
                </button>
            </form>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <a href="index.php" class="py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold text-center transition flex items-center justify-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-globe"></i> Buka Website Publik
                </a>
                <a href="admin/login.php" class="py-3 px-4 rounded-xl bg-indigo-600/20 hover:bg-indigo-600 hover:text-white text-indigo-300 text-xs font-semibold text-center transition flex items-center justify-center gap-2 border border-indigo-500/30">
                    <i class="fa-solid fa-lock"></i> Buka Admin Panel
                </a>
            </div>
        </div>
    </div>

</body>
</html>
