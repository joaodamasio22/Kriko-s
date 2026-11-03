<?php
// Reaproveita os sabores e preços originais das duas páginas, sem banco de dados.
$isPastel = $page === 'pastel';
$categoria = $isPastel ? 'Pastéis' : 'Churros';
$foto = $isPastel ? 'pastel.png' : 'churros.png';
$grupos = ['mais-vendidos' => ['Mais vendidos', $mais_vendidos], 'individuais' => [$categoria . ' individuais', $individuais], 'porcoes' => ['Porções para dividir', $porcoes]];

// Defina o número real com país e DDD, somente dígitos. Vazio mantém pedidos desativados.
$whatsapp = '';
$whatsappValido = (bool) preg_match('/^[1-9][0-9]{9,14}$/', $whatsapp);

// Escapa qualquer valor do cardápio antes de escrevê-lo no HTML.
function e($valor) { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
require __DIR__ . '/header.php';
?>
<main id="conteudo">
  <!-- O breadcrumb permite voltar à abertura sem depender do botão do navegador. -->
  <div class="container pt-4"><nav aria-label="Você está aqui"><ol class="breadcrumb small mb-4"><li class="breadcrumb-item"><a href="index.php">Início</a></li><li class="breadcrumb-item active" aria-current="page"><?= $categoria ?></li></ol></nav></div>

  <!-- A grade Bootstrap empilha texto e foto no celular e os divide no desktop. -->
  <section class="container mb-4" aria-labelledby="categoria-titulo">
    <div class="row g-0 bg-warning rounded-1 overflow-hidden">
      <div class="col-md-8 p-4 p-lg-5"><p class="eyebrow text-primary mb-3"><?= $isPastel ? 'O salgado da vez' : 'Sempre cabe um doce' ?></p><h1 class="display-3 text-primary mb-3" id="categoria-titulo"><?= $isPastel ? 'Fome de pastel.' : 'Seu lado doce.' ?></h1><p class="mb-0 fs-5"><?= $isPastel ? 'Escolha o recheio. A crocância já vem junto.' : 'Açúcar, canela e o seu recheio favorito.' ?></p></div>
      <div class="col-md-4"><div class="ratio ratio-4x3 h-100"><img src="imagens/<?= $foto ?>" class="w-100 h-100 object-fit-cover" alt="<?= $categoria ?> da casa" width="<?= $isPastel ? 236 : 678 ?>" height="<?= $isPastel ? 419 : 452 ?>"></div></div>
    </div>
  </section>

  <!-- Links de âncora tornam as três categorias acessíveis sem JavaScript próprio. -->
  <div class="container" id="sabores"><nav class="d-flex flex-wrap gap-2 border-bottom pb-4" aria-label="Categorias do cardápio"><?php foreach ($grupos as $id => $grupo): ?><a class="btn btn-outline-primary px-3" href="#<?= $id ?>"><?= e($grupo[0]) ?></a><?php endforeach; ?></nav></div>

  <div class="container py-4">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php foreach ($grupos as $id => $grupo): ?>
        <!-- Cada categoria mantém título e lista semânticos; o preço fica junto ao sabor. -->
        <section class="mb-5" id="<?= $id ?>" aria-labelledby="titulo-<?= $id ?>"><div class="d-flex justify-content-between gap-3 align-items-center border-bottom pb-3 mb-2"><h2 class="h3 fw-bold mb-0" id="titulo-<?= $id ?>"><?= e($grupo[0]) ?></h2><span class="eyebrow text-secondary"><?= count($grupo[1]) ?> opções</span></div>
          <ul class="list-unstyled mb-0">
            <?php foreach ($grupo[1] as $item): ?>
            <li class="menu-item py-4 border-bottom d-flex justify-content-between gap-3"><div><span class="eyebrow text-secondary d-block mb-2"><?= e($item['nome']) ?></span><h3 class="h5 mb-0 fw-bold"><?= e($item['desc']) ?></h3></div><span class="fw-bold text-primary text-nowrap align-self-center"><?= e($item['preco']) ?></span></li>
            <?php endforeach; ?>
          </ul>
        </section>
        <?php endforeach; ?>
      </div>

      <!-- Não envia pedidos nem inventa um contato quando não há telefone configurado. -->
      <aside class="col-lg-4"><div class="bg-warning p-4 rounded-1"><p class="eyebrow text-primary mb-3">Já escolheu?</p><h2 class="display-5 mb-3">Deu vontade,<br>né?</h2><p class="text-dark">Anote seus sabores favoritos e confira os valores aqui no cardápio.</p>
        <?php if ($whatsappValido): ?>
        <a class="btn btn-primary w-100 py-3" href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer">Pedir pelo WhatsApp <span aria-hidden="true">↗</span></a>
        <?php else: ?>
        <p class="small border-top border-dark border-opacity-25 pt-3 mb-0">Pedidos online em breve. O contato da loja ainda não está disponível neste site.</p>
        <?php endif; ?>
      </div><a class="btn btn-outline-primary w-100 py-3 mt-3" href="<?= $isPastel ? 'churros.php' : 'pastel.php' ?>"><?= $isPastel ? 'E um churros pra fechar?' : 'Antes do doce, um pastel?' ?> <span aria-hidden="true">→</span></a><p class="small text-secondary mt-3">Imagens ilustrativas. Consulte a disponibilidade dos sabores com a loja.</p></aside>
    </div>
  </div>
</main>
<?php require __DIR__ . '/footer.php'; ?>
