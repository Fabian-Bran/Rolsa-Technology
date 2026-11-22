<?php
declare(strict_types=1);

/** Shared authentication helpers: hashing, validation, rate limiting. */

const MAX_PASSWORD_LENGTH = 128; // stops huge inputs being fed to the hasher (DoS)
const MIN_PASSWORD_LENGTH = 12;  // length matters more than complexity rules (NCSC / NIST 800-63B)

/* ---------- Password hashing ---------- */

function password_algo(): array
{
    if (defined('PASSWORD_ARGON2ID')) {
        return [PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 1]];
    }
    return [PASSWORD_BCRYPT, ['cost' => 12]]; // fallback if PHP was built without Argon2
}

function hash_password(string $password): string
{
    [$algo, $options] = password_algo();
    return password_hash($password, $algo, $options);
}

/** True when a stored hash uses older/weaker settings and should be upgraded at next login. */
function hash_needs_upgrade(string $hash): bool
{
    [$algo, $options] = password_algo();
    return password_needs_rehash($hash, $algo, $options);
}

/* ---------- Input helpers ---------- */

function post_string(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : ''; // ignores array tampering like email[]=x
}

function normalise_email(string $email): string
{
    return mb_strtolower(trim($email), 'UTF-8');
}

function valid_name(string $name): bool
{
    $len = mb_strlen($name, 'UTF-8');
    return $len >= 1 && $len <= 100
        && preg_match("/^[\p{L}\p{M}][\p{L}\p{M}\s'’.\-]*$/u", $name) === 1;
}

/** Returns an error message, or null if the password is acceptable. */
function password_problem(string $password, string $email, string $first, string $last): ?string
{
    static $common = [
        'password1234', 'passwordpassword', '123456789012', 'qwertyuiop12',
        'letmein12345', 'iloveyou1234', 'administrator', 'welcome12345',
    ];
    $len = mb_strlen($password, 'UTF-8');
    if ($len < MIN_PASSWORD_LENGTH) {
        return 'Use at least ' . MIN_PASSWORD_LENGTH . ' characters. A few random words makes a strong, memorable password.';
    }
    if ($len > MAX_PASSWORD_LENGTH) {
        return 'Password must be ' . MAX_PASSWORD_LENGTH . ' characters or fewer.';
    }
    $lower = mb_strtolower($password, 'UTF-8');
    $local = explode('@', $email)[0] ?? '';
    if (in_array($lower, $common, true)
        || $lower === $email
        || (mb_strlen($local) >= 4 && str_contains($lower, $local))
        || (mb_strlen($first) >= 4 && str_contains($lower, mb_strtolower($first, 'UTF-8')))
        || (mb_strlen($last) >= 4 && str_contains($lower, mb_strtolower($last, 'UTF-8')))
        || count(array_unique(mb_str_split($password))) < 5) {
        return 'That password is too easy to guess. Avoid your name, email, or repeated characters.';
    }
    return null;
}

/* ---------- Rate limiting (brute-force / spam protection) ---------- */

/** REMOTE_ADDR only. X-Forwarded-For is client-controlled, so it is deliberately not trusted. */
function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function attempt_identifier(string $value): string
{
    return hash('sha256', $value);
}

function record_attempt(PDO $db, string $action, string $identifier): void
{
    $stmt = $db->prepare('INSERT INTO auth_attempts (action, identifier, ip) VALUES (:action, :identifier, :ip)');
    $stmt->execute(['action' => $action, 'identifier' => $identifier, 'ip' => client_ip()]);

    if (random_int(1, 100) === 1) { // occasional housekeeping
        $db->exec('DELETE FROM auth_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
    }
}

function clear_attempts(PDO $db, string $action, string $identifier): void
{
    $stmt = $db->prepare('DELETE FROM auth_attempts WHERE action = :action AND identifier = :identifier AND ip = :ip');
    $stmt->execute(['action' => $action, 'identifier' => $identifier, 'ip' => client_ip()]);
}

/**
 * Blocked when this IP has made too many attempts overall, or too many
 * against the same identifier. Keying on (identifier + IP) means a stranger
 * cannot lock a real customer out of their own account.
 */
function is_rate_limited(PDO $db, string $action, string $identifier, int $maxPerIdentifier, int $maxPerIp, int $windowMinutes): bool
{
    $window = max(1, $windowMinutes); // int, safe to inline
    $stmt = $db->prepare(
        "SELECT COUNT(*) AS ip_total, COALESCE(SUM(identifier = :identifier), 0) AS id_total
           FROM auth_attempts
          WHERE action = :action AND ip = :ip
            AND attempted_at > (NOW() - INTERVAL {$window} MINUTE)"
    );
    $stmt->execute(['identifier' => $identifier, 'action' => $action, 'ip' => client_ip()]);
    $row = $stmt->fetch();
    return (int) $row['ip_total'] >= $maxPerIp || (int) $row['id_total'] >= $maxPerIdentifier;
}
