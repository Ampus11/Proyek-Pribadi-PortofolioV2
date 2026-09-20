<?php
/**
 * Database Connection & Auto-Setup
 * Menggunakan PDO untuk keamanan (Prepared Statements)
 */

// Allow environment variables for Vercel / Cloud deployment with local Laragon fallback
$env_host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$env_port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$env_user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
$env_pass = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
$env_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'db_portofolio';

define('DB_HOST', $env_host);
define('DB_PORT', $env_port);
define('DB_USER', $env_user);
define('DB_PASS', $env_pass);
define('DB_NAME', $env_name);
define('IS_VERCEL', (bool)(getenv('VERCEL') || isset($_ENV['VERCEL'])));

function get_db_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // 1. Coba koneksi langsung ke database yang sudah ada
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Enable SSL for cloud MySQL providers if requested or on remote DB
        if (getenv('DB_SSL') === 'true' || (!in_array(DB_HOST, ['127.0.0.1', 'localhost']) && defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'))) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Jika database belum ada di server lokal (error 1049: Unknown database), buat otomatis
        if (($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) && in_array(DB_HOST, ['127.0.0.1', 'localhost'])) {
            try {
                // Koneksi ke MySQL server tanpa dbname
                $root_dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                $root_pdo = new PDO($root_dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                $root_pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                // Sambungkan ke database baru
                $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                // Eksekusi skema database.sql jika file ada
                $sql_file = __DIR__ . '/../database.sql';
                if (file_exists($sql_file)) {
                    $sql_content = file_get_contents($sql_file);
                    $pdo->exec($sql_content);
                }

                return $pdo;
            } catch (Exception $init_err) {
                if (IS_VERCEL) {
                    error_log("Vercel DB Init Warning: " . $init_err->getMessage());
                    return null;
                }
                render_db_error("Gagal menginisialisasi database otomatis: " . $init_err->getMessage());
            }
        } else {
            // Jika di Vercel atau Cloud hosting dan DB belum disetel / offline, jangan crash dengan 500 error!
            if (IS_VERCEL || !in_array(DB_HOST, ['127.0.0.1', 'localhost'])) {
                error_log("Database Connection Failed: " . $e->getMessage());
                return null;
            }
            // MySQL service belum dijalankan di Laragon lokal
            render_db_error($e->getMessage());
        }
    }
}

/**
 * Tampilan error database yang user-friendly & informatif
 */
function render_db_error($errorMessage) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Koneksi Database Gagal - Portofolio</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>
    <body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen p-4 font-sans">
        <div class="max-w-lg w-full bg-slate-800/90 border border-slate-700/80 rounded-2xl p-8 shadow-2xl backdrop-blur-sm text-center">
            <div class="w-16 h-16 bg-amber-500/20 text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-5 text-3xl shadow-inner">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h1 class="text-2xl font-bold mb-2 text-white">Layanan Database Belum Aktif</h1>
            <p class="text-slate-400 text-sm mb-6 leading-relaxed">
                Website tidak dapat terhubung ke MySQL server. Ini biasanya terjadi jika Laragon atau MySQL belum dijalankan.
            </p>

            <div class="bg-slate-950/60 rounded-xl p-4 text-left text-xs font-mono text-slate-300 mb-6 border border-slate-800 break-words">
                <p class="text-amber-400 font-semibold mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Pesan Error:</p>
                <code><?= htmlspecialchars($errorMessage) ?></code>
            </div>

            <div class="bg-blue-950/40 border border-blue-800/60 rounded-xl p-4 text-left text-sm text-blue-200 mb-6 space-y-2">
                <p class="font-semibold text-blue-300 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-400"></i> Cara Menyelesaikan:
                </p>
                <ol class="list-decimal list-inside space-y-1 text-xs text-blue-100/90 pl-1">
                    <li>Buka aplikasi <strong>Laragon</strong> di komputer Anda.</li>
                    <li>Klik tombol <strong class="text-emerald-400">Start All</strong> (untuk menyalakan Apache & MySQL).</li>
                    <li>Muat ulang (Refresh) halaman browser ini (<kbd class="bg-slate-800 px-1 py-0.5 rounded text-[11px]">F5</kbd>).</li>
                </ol>
            </div>

            <button onclick="window.location.reload();" class="inline-flex items-center justify-center gap-2 w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-medium rounded-xl transition duration-200 shadow-lg shadow-blue-500/25 cursor-pointer">
                <i class="fa-solid fa-arrows-rotate"></i> Coba Muat Ulang Sekarang
            </button>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$pdo = get_db_connection();
