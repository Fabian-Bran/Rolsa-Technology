<?php
$flash = flash_get();
if ($flash): ?>
  <div class="notice notice--<?= e($flash['type']) ?>" role="status">
    <?= e($flash['message']) ?>
  </div>
<?php endif; ?>
