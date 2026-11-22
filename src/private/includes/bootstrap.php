<?php
declare(strict_types=1);

/**
 * Loaded at the top of every page, before any output.
 * Responsibilities: error policy, security headers, hardened session,
 * and small helpers (escaping, CSRF, flash messages, redirects).
 */

const SESSION_IDLE_TIMEOUT     = 1800;   // 30 min of inactivity
const SESSION_ABSOLUTE_TIMEOUT = 28800;  // 8 h hard limit

if (defined('BOOTSTRAPPED')) {
    return;
}
define('BOOTSTRAPPED', true);

// Never show PHP errors to visitors; log them instead.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

//returns true if https enabled
function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

//returns true if https enabled
function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }
    header_remove('X-Powered-By');
    header("Content-Security-Policy: default-src 'self'; "
        . "script-src 'self'; "
        . "style-src 'self' https://fonts.googleapis.com; "
        . "font-src https://fonts.gstatic.com; "
        . "img-src 'self' data:; "
        . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/** Stop browsers/proxies caching pages that contain account state. */
function no_cache(): void
{
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
}

//starts new session
function start_secure_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');      // reject unknown session IDs
    ini_set('session.use_only_cookies', '1');     // no IDs in URLs
    ini_set('session.use_trans_sid', '0');
    ini_set('session.sid_length', '48');
    ini_set('session.sid_bits_per_character', '6');

    session_name('rolsa_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => is_https(),
        'httponly' => true,   // JS cannot read the cookie
        'samesite' => 'Lax',  // blocks cross-site POSTs carrying the cookie
    ]);
    session_start();
}

//logs users out if session is too long.
function enforce_session_limits(): void
{
    if (empty($_SESSION['logged'])) {
        return;
    }
    $now = time();
    $idleTooLong = $now - ($_SESSION['last_activity'] ?? $now) > SESSION_IDLE_TIMEOUT;
    $tooOld      = $now - ($_SESSION['logged_in_at'] ?? $now) > SESSION_ABSOLUTE_TIMEOUT;

    if ($idleTooLong || $tooOld) {
        end_session();
        flash_set('info', 'You were signed out because your session expired.');
        return;
    }
    $_SESSION['last_activity'] = $now;
}

/** Fully invalidate the current login. */
function end_session(): void
{
    $_SESSION = [];
    session_regenerate_id(true); // new ID, old session file deleted
}

/* ---------- Output escaping ---------- */

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ---------- CSRF ---------- */

// returns csrf token (new if doesen't already exist)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// creates a hidden input field for the csrf token
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// returns true if the csrf token is vaild
function csrf_verify(): bool
{
    $sent = $_POST['csrf_token'] ?? null;
    $real = $_SESSION['csrf_token'] ?? '';
    return is_string($sent) && $real !== '' && hash_equals($real, $sent);
}

/* ---------- Flash messages ---------- */

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------- Navigation / auth helpers ---------- */

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['logged']) && $_SESSION['logged'] === true;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash_set('info', 'Please sign in to view that page.');
        redirect('login.php');
    }
}

send_security_headers();
start_secure_session();
enforce_session_limits();
?>