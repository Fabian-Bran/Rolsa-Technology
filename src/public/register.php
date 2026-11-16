<?php
$pageTitle = 'Eco-conscious technology';
require_once __DIR__ . '../private/db/db.php';
require_once __DIR__ . '/../private/includes/bootstrap.php';
include __DIR__ . '/../private/includes/head.php';
include __DIR__ . '/../private/includes/header.php';
?>
<main id="main">
  <div class="register"> 
    <div class="content">
      <h1>register</h1>
      <form action="../private/includes/header.php" method="post">
          <input type="text" name="first_name" require>
          <input type="text" name="last_name" require>
          <input type="text" name="email" require>
          <input type="text" name="password" require>
          
          <input type="submit">
      </form>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../private/includes/footer.php'?>