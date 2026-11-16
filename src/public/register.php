<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Source+Serif+4:wght@600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Rolsa Technologies | Green Tech</title>
  </head>
  <body>
    <?php include "../private/includes/header.php"?>
    <main>
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
    <?php include "../private/includes/footer.php"?>
  </body>
</html>