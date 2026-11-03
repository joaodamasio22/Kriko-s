<?php
// Define a página ativa antes de carregar o cabeçalho compartilhado.
$page = 'home';
require __DIR__ . '/includes/header.php';
?>
<main id="conteudo">
  <!-- Abertura em duas colunas no desktop; a grade empilha o conteúdo no celular. -->
  <section class="container py-4 py-lg-5" aria-labelledby="hero-title">
    <div class="row g-0 bg-warning overflow-hidden rounded-1">
      <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center">
        <p class="eyebrow mb-4">Pode chegar. A fome é de casa.</p>
        <h1 class="display-1 text-primary mb-4" id="hero-title">Bateu<br>a fome<span class="text-dark">?</span></h1>
        <p class="fs-4 fw-bold mb-2">A gente resolve.</p>
        <p class="mb-4 text-dark">Pastel crocante, recheio caprichado e um<br class="d-none d-xl-block"> churros pra deixar o dia mais gostoso.</p>
        <div class="d-flex flex-wrap gap-3 align-items-center"><a href="pastel.php" class="btn btn-primary py-3 px-4">Escolher meu pastel <span aria-hidden="true" class="ms-3">→</span></a><a href="churros.php" class="link-dark fw-bold link-offset-3 py-2">Sou do time doce</a></div>
        <div class="border-top border-dark border-opacity-25 mt-5 pt-3 d-flex flex-wrap gap-3 small"><span>Pastéis individuais</span><span aria-hidden="true">✳</span><span>Porções pra dividir</span><span aria-hidden="true">✳</span><span>Churros</span></div>
      </div>
      <div class="col-lg-6 position-relative">
        <div class="ratio h-100" style="--bs-aspect-ratio: 105%;"><img class="w-100 h-100 object-fit-cover hero-photo" src="imagens/pastel.png" alt="Pastéis dourados e crocantes em um prato preto" fetchpriority="high" width="236" height="419"></div>
        <div class="position-absolute bottom-0 start-0 end-0 p-4"><div class="bg-light d-inline-block py-3 px-4 rounded-1"><span class="eyebrow text-primary d-block mb-1">O clássico que não tem erro</span><span class="fw-bold">Crocante na primeira mordida.</span></div></div>
      </div>
    </div>
  </section>
  <div class="container"><div class="row g-0 border-top border-bottom py-3 text-center small fw-bold"><div class="col-4 border-end">Massa crocante</div><div class="col-4 border-end">Recheio de respeito</div><div class="col-4">Doce na medida</div></div></div>
  <!-- Os dois links levam aos cardápios existentes, com seus preços preservados. -->
  <section class="container py-5" id="cardapio" aria-labelledby="cardapio-title">
    <div class="row align-items-end gy-3 mb-4"><div class="col-md-8"><p class="eyebrow text-primary mb-3">Nosso cardápio</p><h2 class="display-4 mb-0" id="cardapio-title">Qual é a sua vontade?</h2></div><div class="col-md-4"><p class="text-secondary mb-1 text-md-end">Comece pelo salgado.<br>Ou vá direto à sobremesa.</p></div></div>
    <div class="row g-4">
      <div class="col-md-6"><a href="pastel.php" class="food-link text-decoration-none text-dark d-block h-100 border rounded-1 overflow-hidden"><div class="row g-0 h-100"><div class="col-5"><img src="imagens/pastel.png" class="w-100 h-100 object-fit-cover" alt="Pastéis da casa" loading="lazy" width="236" height="419"></div><div class="col-7 p-3 p-lg-4 d-flex flex-column justify-content-center"><span class="eyebrow text-primary mb-3">01 / Pra matar a fome</span><h3 class="display-5 mb-3">Pastéis</h3><p class="small text-secondary">Carne, queijo, frango e aquele especial da casa.</p><span class="fw-bold mt-2">A partir de R$ 10,00</span><span class="text-primary small fw-bold mt-3">Ver sabores <span aria-hidden="true">↗</span></span></div></div></a></div>
      <div class="col-md-6"><a href="churros.php" class="food-link text-decoration-none text-dark d-block h-100 border rounded-1 overflow-hidden"><div class="row g-0 h-100"><div class="col-5"><img src="imagens/churros.png" class="w-100 h-100 object-fit-cover" alt="Churros cobertos com açúcar" loading="lazy" width="678" height="452"></div><div class="col-7 p-3 p-lg-4 d-flex flex-column justify-content-center"><span class="eyebrow text-primary mb-3">02 / Pra adoçar o dia</span><h3 class="display-5 mb-3">Churros</h3><p class="small text-secondary">Doce de leite, chocolate e mais um pouco de felicidade.</p><span class="fw-bold mt-2">A partir de R$ 8,00</span><span class="text-primary small fw-bold mt-3">Ver sabores <span aria-hidden="true">↗</span></span></div></div></a></div>
    </div>
  </section>
  <section class="container pb-3" aria-labelledby="dividir-title"><div class="bg-primary text-light p-4 p-lg-5 rounded-1 row g-0 align-items-center"><div class="col-md-8"><p class="eyebrow text-warning mb-3">Chama a turma</p><h2 class="display-4 mb-3" id="dividir-title">Bom mesmo é dividir.<br>Ou tentar.</h2><p class="mb-md-0 text-light text-opacity-75">Mini pastéis e mini churros em porções para compartilhar.</p></div><div class="col-md-4 text-md-end"><a href="pastel.php#porcoes" class="btn btn-light px-4 py-3">Conhecer as porções <span class="ms-3" aria-hidden="true">↗</span></a></div></div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
