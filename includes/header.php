<?php
// Configura valores padrão para o cabeçalho compartilhado das três páginas.
$page = $page ?? 'home';
$title = $title ?? "Kriko's — Pastéis & Churros";
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="O salgado, o doce e a sua próxima pausa. Conheça os pastéis, churros e porções do Kriko's e confira nosso cardápio.">
  <meta name="theme-color" content="#a82d24">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" href="imagens/logo-pastel.png" type="image/png">
  <!-- Arquivos locais dispensam conexão ao CDN durante a navegação. -->
  <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<a class="visually-hidden-focusable position-absolute top-0 start-0 bg-light p-3 z-3" href="#conteudo">Pular para o conteúdo</a>
<div class="bg-primary text-light text-center py-2 px-3 small">Pastel na mão. Churros de sobremesa. <span class="d-none d-sm-inline">O resto pode esperar.</span></div>
<!-- Navbar e menu recolhível utilizam os componentes nativos do Bootstrap. -->
<header class="border-bottom">
  <nav class="navbar navbar-expand-md py-3" aria-label="Navegação principal">
    <div class="container gap-3">
      <a class="navbar-brand brand text-primary m-0" href="index.php" aria-label="Kriko's — início">KRIKO’S<span class="d-block eyebrow text-dark mt-1" style="font-family: Arial, sans-serif; font-size: .6rem; letter-spacing: .25em;">PASTÉIS & CHURROS</span></a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navegacao" aria-controls="navegacao" aria-expanded="false" aria-label="Abrir menu"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navegacao">
        <ul class="navbar-nav mx-auto gap-md-4 py-3 py-md-0">
          <?php foreach (['home' => ['index.php', 'Início'], 'pastel' => ['pastel.php', 'Pastéis'], 'churros' => ['churros.php', 'Churros']] as $key => $link): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $link[0] ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>><?= $link[1] ?></a></li>
          <?php endforeach; ?>
        </ul>
        <a class="btn btn-primary px-4 py-2" href="<?= $page === 'home' ? '#cardapio' : '#sabores' ?>">Ver cardápio <span aria-hidden="true" class="ms-2">↗</span></a>
      </div>
    </div>
  </nav>
</header>
