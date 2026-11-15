<?php

require_once dirname(__DIR__) . '/includes/auth.php';
krikosExigirPerfil(['GARCOM']);

$page = 'home';
$pastelArt = 'imagens/pastel-recortado.png';
$churrosArt = 'imagens/churros-recortado.png';
$temPastel = is_file(__DIR__ . '/' . $pastelArt);
$temChurros = is_file(__DIR__ . '/' . $churrosArt);

require dirname(__DIR__) . '/includes/header.php';
?>
<main id="conteudo" class="home-track">
    <nav class="staff-bar" aria-label="Área do garçom">
        <span>Olá, <?= htmlspecialchars($_SESSION['krikos_usuario']['nome'], ENT_QUOTES, 'UTF-8') ?></span>
        <a class="text-warning" href="pedidos.php">Pedidos e pagamentos</a>
        <form method="post" action="sair.php">
            <input type="hidden" name="csrf"
                value="<?= htmlspecialchars($_SESSION['krikos_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Sair</button>
        </form>
    </nav>

    <section class="home-scene sticky-top d-flex flex-column" aria-labelledby="titulo-inicio">

        <div class="home-intro-top d-flex justify-content-center align-items-center">

            <a class="home-intro-logo" href="index.php" aria-label="Kriko’s — início">KRIKO’S</a>

            <h1 class="home-slogan m-0" id="titulo-inicio">Bateu a
                <br>fome?
                <br>
                <span>A gente
                    <br>resolve!
                </span>
            </h1>

        </div>

        <nav id="cardapio" class="home-options row g-0 flex-grow-1" aria-label="Cardápios">
            <a href="pastel.php"
                class="choice choice-pastel col-6 position-relative d-flex align-items-center justify-content-center">

                <span<?= $temPastel ? ' class="visually-hidden"' : '' ?>>Pastéis crocantes</span>

                    <?php if ($temPastel): ?>

                        <img class="choice-art" src="<?= $pastelArt ?>" alt="Pastéis crocantes">

                    <?php endif; ?>

            </a>

            <a href="churros.php"
                class="choice choice-churros col-6 position-relative d-flex align-items-center justify-content-center">

                <span<?= $temChurros ? ' class="visually-hidden"' : '' ?>>churros feitos</span>

                    <?php if ($temChurros): ?>

                        <img class="choice-art" src="<?= $churrosArt ?>" alt="Churros feitos">

                    <?php endif; ?>

            </a>
        </nav>
    </section>
</main>
<script src="assets/home.js?v=16" defer></script>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
