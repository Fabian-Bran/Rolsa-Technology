<?php
$pageTitle = 'Eco-conscious technology';
require_once __DIR__ . '/../private/db/db.php';
require_once __DIR__ . '/../private/includes/bootstrap.php';
include __DIR__ . '/../private/includes/head.php';
include __DIR__ . '/../private/includes/header.php';
?>
<main id="main">
  <h1>Loggin</h1>
  <p>.</p>
</main>
<?php include __DIR__ . '/../private/includes/footer.php'?>

<?php
require_once __DIR__ . '/../private/includes/bootstrap.php';
no_cache();

if (is_logged_in()) {
    redirect('user.php');
}

require_once __DIR__ . '/../private/utilities/register-process.php'; // sets $errors, $old

/** Renders the error message for a field and returns its aria attributes. */
function field_attrs(array $errors, string $name): string
{
    return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="' . e($name) . '-error"' : '';
}
function field_error(array $errors, string $name): void
{
    if (isset($errors[$name])) {
        echo '<p class="field__error" id="' . e($name) . '-error">' . e($errors[$name]) . '</p>';
    }
}

$pageTitle = 'Create an account';
$pageScripts = ['auth.js'];
include __DIR__ . '/../private/includes/head.php';
include __DIR__ . '/../private/includes/header.php';
?>
    <main id="main">
      <div class="container">
        <div class="auth">
          <div class="auth__panel">
            <h1>Create your account</h1>
            <p class="lead">Request quotes and manage your consultations in one place.</p>

            <?php if (isset($errors['form'])): ?>
              <div class="notice notice--error" role="alert"><?= e($errors['form']) ?></div>
            <?php elseif ($errors): ?>
              <div class="notice notice--error" role="alert">Please fix the highlighted fields below.</div>
            <?php endif; ?>

            <form action="register.php" method="post" class="form" data-single-submit novalidate>
              <?= csrf_field() ?>

              <div class="form__row">
                <div class="field">
                  <label for="first_name">First name</label>
                  <input type="text" id="first_name" name="first_name" value="<?= e($old['first_name']) ?>"
                         autocomplete="given-name" maxlength="100" required<?= field_attrs($errors, 'first_name') ?> />
                  <?php field_error($errors, 'first_name'); ?>
                </div>
                <div class="field">
                  <label for="last_name">Last name</label>
                  <input type="text" id="last_name" name="last_name" value="<?= e($old['last_name']) ?>"
                         autocomplete="family-name" maxlength="100" required<?= field_attrs($errors, 'last_name') ?> />
                  <?php field_error($errors, 'last_name'); ?>
                </div>
              </div>

              <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="<?= e($old['email']) ?>"
                       autocomplete="email" maxlength="255" required<?= field_attrs($errors, 'email') ?> />
                <?php field_error($errors, 'email'); ?>
              </div>

              <div class="field">
                <label for="password">Password</label>
                <div class="field__password">
                  <input type="password" id="password" name="password" autocomplete="new-password"
                         minlength="<?= MIN_PASSWORD_LENGTH ?>" maxlength="<?= MAX_PASSWORD_LENGTH ?>" required
                         aria-describedby="password-hint<?= isset($errors['password']) ? ' password-error' : '' ?>"
                         <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?> />
                  <button type="button" class="toggle" data-toggle-password="password" aria-pressed="false">Show</button>
                </div>
                <p class="field__hint" id="password-hint">At least <?= MIN_PASSWORD_LENGTH ?> characters. Three or four random words works well.</p>
                <?php field_error($errors, 'password'); ?>
              </div>

              <div class="field">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password"
                       maxlength="<?= MAX_PASSWORD_LENGTH ?>" required<?= field_attrs($errors, 'confirm_password') ?> />
                <?php field_error($errors, 'confirm_password'); ?>
              </div>

              <button type="submit" class="btn btn--block">Create account</button>
            </form>

            <p class="auth__alt">Already registered? <a href="login.php">Log in</a></p>
          </div>
        </div>
      </div>
    </main>
<?php include __DIR__ . '/../private/includes/footer.php'; ?>
