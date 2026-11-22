<?php
// Renders the footer and closes <body> and <html>.
// Optional: set $pageScripts = ['auth.js'] before including.
?>
<footer class="site-footer">
  <div class="container site-footer__inner">
    <p><strong>Rolsa Technologies</strong></p>
    <p>Telephone: <a href="tel:01752000000">01752 000000</a></p>
    <p>&copy; <?= date('Y') ?> Rolsa Technologies</p>
  </div>
</footer>
<?php foreach (($pageScripts ?? []) as $script): ?>
<script src="assets/js/<?= e($script) ?>" defer></script>
<?php endforeach; ?>
  </body>
</html>
