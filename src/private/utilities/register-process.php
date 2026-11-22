<?php
declare(strict_types=1);

/**
 * Handles the registration POST. Expects bootstrap.php already loaded.
 * Sets $errors and $old for the form; redirects to login on success.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db/db.php';

$errors = [];
$old = ['first_name' => '', 'last_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first    = trim(post_string('first_name'));
    $last     = trim(post_string('last_name'));
    $email    = normalise_email(post_string('email'));
    $password = post_string('password');
    $confirm  = post_string('confirm_password');

    $old = [
        'first_name' => mb_substr($first, 0, 100),
        'last_name'  => mb_substr($last, 0, 100),
        'email'      => mb_substr($email, 0, 255),
    ];

    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        if (!valid_name($first)) {
            $errors['first_name'] = 'Enter your first name (letters, spaces, hyphens and apostrophes only).';
        }
        if (!valid_name($last)) {
            $errors['last_name'] = 'Enter your last name (letters, spaces, hyphens and apostrophes only).';
        }
        if ($email === '' || mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (!isset($errors['email'])) {
            if ($problem = password_problem($password, $email, $first, $last)) {
                $errors['password'] = $problem;
            }
        } elseif ($password === '') {
            $errors['password'] = 'Choose a password.';
        }
        if (!isset($errors['password']) && !hash_equals($password, $confirm)) {
            $errors['confirm_password'] = 'The passwords do not match.';
        }

        if (!$errors) {
            try {
                $db = db();
                $id = attempt_identifier($email);

                if (is_rate_limited($db, 'register', $id, 3, 10, 60)) {
                    http_response_code(429);
                    $errors['form'] = 'Too many sign-up attempts from this connection. Please try again later.';
                } else {
                    record_attempt($db, 'register', $id);
                    try {
                        // Insert directly and catch the UNIQUE violation: no check-then-insert race.
                        $stmt = $db->prepare(
                            'INSERT INTO users (first_name, last_name, email, password_hash)
                             VALUES (:first, :last, :email, :hash)'
                        );
                        $stmt->execute([
                            'first' => $first,
                            'last'  => $last,
                            'email' => $email,
                            'hash'  => hash_password($password),
                        ]);
                        flash_set('success', 'Your account has been created. Please sign in.');
                        redirect('login.php');
                    } catch (PDOException $ex) {
                        if (($ex->errorInfo[1] ?? 0) === 1062) { // duplicate email
                            $errors['form'] = 'We could not create an account with those details. If you already have one, try signing in.';
                        } else {
                            throw $ex;
                        }
                    }
                }
            } catch (Throwable $ex) {
                error_log('Register error: ' . $ex->getMessage());
                $errors['form'] = 'Something went wrong on our side. Please try again shortly.';
            }
        }
    }
}
