<?php
declare(strict_types=1);

/**
 * Handles the login POST. Expects bootstrap.php already loaded.
 * Sets $errors and $old for the form; redirects on success.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db/db.php';

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = normalise_email(post_string('email'));
    $password = post_string('password');
    $old['email'] = mb_substr($email, 0, 255);

    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } elseif ($email === '' || $password === '') {
        $errors['form'] = 'Enter your email and password.';
    } else {
        try {
            $db = db();
            $id = attempt_identifier($email);

            if (is_rate_limited($db, 'login', $id, 5, 20, 15)) {
                http_response_code(429);
                $errors['form'] = 'Too many sign-in attempts. Please wait 15 minutes and try again.';
            } else {
                $stmt = $db->prepare('SELECT id, first_name, role, password_hash FROM users WHERE email = :email LIMIT 1');
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                $valid = false;
                if ($user && $user['password_hash'] !== null && strlen($password) <= MAX_PASSWORD_LENGTH * 4) {
                    $valid = password_verify($password, $user['password_hash']);
                } else {
                    hash_password($password === '' ? 'x' : substr($password, 0, MAX_PASSWORD_LENGTH)); // equalise timing
                }

                if (!$valid) {
                    record_attempt($db, 'login', $id);
                    // One generic message: never reveal whether the email exists.
                    $errors['form'] = 'Incorrect email or password.';
                } else {
                    if (hash_needs_upgrade($user['password_hash'])) {
                        $upd = $db->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
                        $upd->execute(['hash' => hash_password($password), 'id' => $user['id']]);
                    }
                    clear_attempts($db, 'login', $id);

                    session_regenerate_id(true);   // defeats session fixation
                    unset($_SESSION['csrf_token']); // fresh CSRF token for the new session
                    $_SESSION['logged']        = true;
                    $_SESSION['user_id']       = (int) $user['id'];
                    $_SESSION['first_name']    = $user['first_name'];
                    $_SESSION['role']          = $user['role'];
                    $_SESSION['logged_in_at']  = time();
                    $_SESSION['last_activity'] = time();

                    redirect('user.php');
                }
            }
        } catch (Throwable $ex) {
            error_log('Login error: ' . $ex->getMessage());
            $errors['form'] = 'Something went wrong on our side. Please try again shortly.';
        }
    }
}
