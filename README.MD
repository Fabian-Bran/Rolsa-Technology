This is a website made with html, css, js and php

## deliverables:

The clients (owners of Rolsa Technologies) would like to develop a digital solution
that will:

1. provide customers with information about:
2. green energy products currently on the market
3. how to reduce their carbon footprint
4. allow customers to:
5. calculate their carbon footprint.
6. account registration to allow customers to manage their consultations and data
7. accessibility features to support a wide range of users
8. a tool for calculating and tracking energy usage.

## Setup

1. Create the database: `mysql -u root -p < src/private/db/db.sql`
2. Copy `src/private/config.local.example.php` to `src/private/config.local.php` and enter your DB credentials (git-ignored).
3. Point your web server's document root at `src/public`. Everything in `src/private` must stay outside the web root.
4. Requires PHP 8.1+ with the `pdo_mysql` and `mbstring` extensions.

## Security notes

- Passwords hashed with Argon2id (bcrypt fallback), upgraded automatically at login if settings change.
- All SQL uses PDO prepared statements (emulation off).
- CSRF tokens on every POST form; logout is POST-only.
- Session cookie is HttpOnly + SameSite=Lax (+ Secure on HTTPS); ID regenerated at login/logout; idle (30 min) and absolute (8 h) timeouts.
- Login/register are rate limited (table `auth_attempts`); login errors are generic to prevent account enumeration.
- Strict Content-Security-Policy and other security headers are sent from `src/private/includes/bootstrap.php`.
- Serve the live site over HTTPS only.
