<?php
require_once __DIR__ . '/bootstrap.php';

$current = basename($_SERVER['SCRIPT_NAME'] ?? '');
$links = [
    'index.php'      => 'Home',
    'calculator.php' => 'Calculator',
    'services.php'   => 'Services',
];
?>
<header class="site-header">
  <div class="container site-header__inner">
    <a class="brand" href="index.php">
      <span class="brand__name">Rolsa Technologies</span>
    </a>

    <nav class="site-nav" aria-label="Main">
      <ul>
        <?php foreach ($links as $file => $label): ?>
          <li>
            <a class="nav__link" href="<?= e($file) ?>"<?= $current === $file ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="nav__account">
        <?php if (is_logged_in()): ?>
          <a class="nav__link" href="user.php"<?= $current === 'user.php' ? ' aria-current="page"' : '' ?>>
            <?= e($_SESSION['first_name'] ?? 'Account') ?>
          </a>
          <form action="logout.php" method="post" class="inline-form">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn--ghost btn--sm">Log out</button>
          </form>
        <?php else: ?>
          <a class="nav__link" href="login.php"<?= $current === 'login.php' ? ' aria-current="page"' : '' ?>>Log in</a>
          <a class="btn btn--sm" href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>
