<?php
/**
 * Helper Functions for Portfolio Website
 */

// Enable output buffering to prevent premature header output
if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize string output to avoid XSS
 */
function sanitize($str) {
    return htmlspecialchars(trim($str ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Get profile setting value by key
 */
function get_setting($pdo, $key, $default = '') {
    if ($pdo === null) {
        return $default;
    }
    static $settings_cache = null;

    if ($settings_cache === null) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM profile_settings");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $settings_cache = $rows ?: [];
        } catch (Exception $e) {
            return $default;
        }
    }

    return $settings_cache[$key] ?? $default;
}

/**
 * Set a flash message into session
 */
function set_flash_message($type, $text) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'text' => $text
    ];
}

/**
 * Get and clear flash message
 */
function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if admin user is currently logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_user_id']) && !empty($_SESSION['admin_user_id']);
}

/**
 * Require admin authentication for protected pages
 */
function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Generate or get CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format timestamp into Indonesian date format
 */
function format_date_id($datetime) {
    if (!$datetime) return '-';
    $timestamp = strtotime($datetime);
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    return "$day $month $year, $time WIB";
}
