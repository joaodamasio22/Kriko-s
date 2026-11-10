<?php
$page = 'home';
$pastelArt = 'imagens/pastel-recortado.png';
$churrosArt = 'imagens/churros-recortado.png';
$temPastel = is_file(__DIR__ . '/' . $pastelArt);
$temChurros = is_file(__DIR__ . '/' . $churrosArt);
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="conteudo" class="home-track">
  <!-- A rolagem desloca o mesmo logo enquanto os dois cardápios ficam à vista. -->
  <section class="home-scene sticky-top d-flex flex-column" aria-labelledby="titulo-inicio">
    <div class="home-intro-top d-flex justify-content-center align-items-center">
      <a class="home-intro-logo" href="index.php" aria-label="Kriko’s — início">KRIKO’S</a>
      <h1 class="home-slogan m-0" id="titulo-inicio">
        Bateu a<br>fome?<br>
        <span>A gente<br>resolve!</span>
      </h1>
    </div>

    <nav id="cardapio" class="home-options row g-0 flex-grow-1" aria-label="Cardápios">
      <a href="pastel.php" class="choice choice-pastel col-6 position-relative d-flex align-items-center justify-content-center">
        <span<?= $temPastel ? ' class="visually-hidden"' : '' ?>>Pastéis crocantes</span>
        <?php if ($temPastel): ?>
        <img class="choice-art" src="<?= $pastelArt ?>" alt="">
        <?php endif; ?>
      </a>
      <a href="churros.php" class="choice choice-churros col-6 position-relative d-flex align-items-center justify-content-center">
        <span<?= $temChurros ? ' class="visually-hidden"' : '' ?>>churros feitos</span>
        <?php if ($temChurros): ?>
        <img class="choice-art" src="<?= $churrosArt ?>" alt="">
        <?php endif; ?>
      </a>
    </nav>
  </section>
</main>
<script src="assets/home.js?v=16" defer></script>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
