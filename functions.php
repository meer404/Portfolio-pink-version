<?php
/**
 * Helper Functions
 * Language, CSRF, Auth, and Sanitization utilities
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the current language (defaults to 'en')
 */
function getCurrentLang(): string {
    static $currentLang = null;
    
    if ($currentLang !== null) {
        return $currentLang;
    }

    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ku'])) {
        $_SESSION['lang'] = $_GET['lang'];
        if (!headers_sent()) {
            setcookie('lang', $_GET['lang'], time() + (86400 * 365), '/');
        }
        $currentLang = $_GET['lang'];
        return $currentLang;
    }
    
    if (isset($_SESSION['lang'])) {
        $currentLang = $_SESSION['lang'];
        return $currentLang;
    }
    
    if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['en', 'ku'])) {
        $_SESSION['lang'] = $_COOKIE['lang'];
        $currentLang = $_COOKIE['lang'];
        return $currentLang;
    }
    
    $currentLang = 'en';
    return $currentLang;
}

/**
 * Return the correct translation based on current language
 */
function t(string $en, string $ku): string {
    return getCurrentLang() === 'ku' ? $ku : $en;
}

/**
 * Get text direction
 */
function getDir(): string {
    return getCurrentLang() === 'ku' ? 'rtl' : 'ltr';
}

/**
 * Get HTML lang attribute
 */
function getLangAttr(): string {
    return getCurrentLang() === 'ku' ? 'ckb' : 'en';
}

/**
 * Generate CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST data
 */
function verify_csrf(): bool {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return verify_csrf_token($_POST['csrf_token']);
}

/**
 * Verify a CSRF token string without consuming POST
 */
function verify_csrf_token(string $token): bool {
    if ($token === '' || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $valid;
}

/**
 * Output CSRF hidden input
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Check if admin is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin login - redirect to login page if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Sanitize input
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Get the language suffix for database fields
 */
function langSuffix(): string {
    return '_' . getCurrentLang();
}

/**
 * Read a field that may use alternate column names across DB versions
 */
function dbField(array $row, string $primary, string $fallback = ''): string {
    if (!empty($row[$primary])) {
        return (string) $row[$primary];
    }
    if ($fallback && !empty($row[$fallback])) {
        return (string) $row[$fallback];
    }
    return '';
}

/**
 * Resolve upload/media path for display
 */
function mediaSrc(?string $path): string {
    if (empty($path) || $path === '#') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $path = ltrim($path, '/');
    $path = preg_replace('#^portfolio/#', '', $path);
    if (str_starts_with($path, 'uploads/') && file_exists(__DIR__ . '/' . $path)) {
        return $path;
    }
    $basename = basename($path);
    if (file_exists(__DIR__ . '/uploads/' . $basename)) {
        return 'uploads/' . $basename;
    }
    if (file_exists(__DIR__ . '/' . $path)) {
        return $path;
    }
    return $path;
}

function mediaExists(?string $path): bool {
    $src = mediaSrc($path);
    if ($src === '' || preg_match('#^https?://#i', $src)) {
        return $src !== '';
    }
    return file_exists(__DIR__ . '/' . $src);
}
