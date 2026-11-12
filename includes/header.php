<?php
$page = $page ?? 'home';
$title = $title ?? 'Kriko’s';
$themeColors = ['home' => '#5e210c', 'pastel' => '#ff8d28', 'churros' => '#f42626'];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="<?= $themeColors[$page] ?? $themeColors['home'] ?>">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="style.css?v=17">
  <script src="assets/vendor/bootstrap.bundle.min.js" defer></script>
</head>
<body class="page-<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>">
