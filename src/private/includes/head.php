<?php
// Opens the document. Set $pageTitle (and optionally $metaDescription) before including.
require_once __DIR__ . '/bootstrap.php';

$fullTitle = isset($pageTitle) ? $pageTitle . ' | Rolsa Technologies' : 'Rolsa Technologies | Green Tech';
$metaDescription = $metaDescription ?? 'Rolsa Technologies provides eco-friendly technology, carbon footprint tools and expert green energy advice.';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content=<?= e($metaDescription)?> />
    <meta name="color-scheme" content="dark" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:wght@600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/style.css"/>
    <title><?= e($fullTitle)?></title>
  </head>
  <body>
    <a class="skip-link" href="#main">Skip to main content</a>
