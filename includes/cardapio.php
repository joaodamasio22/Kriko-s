<?php
use Kriko\Repository\CardapioRepository;

require_once __DIR__ . '/auth.php';
krikosExigirPerfil(['GARCOM']);

$isPastel = $page === 'pastel';
$categoria = $isPastel ? 'Pastéis' : 'Churros';
$logo = $isPastel ? 'logo-pastel.png' : 'logo-churros.png';

$produtos = [];
$cardapioIndisponivel = false;
try {
    require dirname(__DIR__) . '/bootstrap.php';
    $produtos = new CardapioRepository($entityManager->getConnection())->listar($page);
} catch (Throwable $erro) {
    error_log('Falha ao consultar o cardápio: ' . $erro->getMessage());
    http_response_code(503);
    $cardapioIndisponivel = true;
}

$grupos = array_filter(
    [
        ['Mais vendidos', $produtos['mais_vendidos'] ?? []],
        [$categoria . ' individuais', $produtos['individuais'] ?? []],
        ['Porções', $produtos['porcoes'] ?? []],
    ],
    static fn(array $grupo) => $grupo[1] !== [],
);

$whatsapp = trim(getenv('KRIKOS_WHATSAPP') ?: '');
$instagram = ltrim(trim(getenv('KRIKOS_INSTAGRAM') ?: ''), '@');
$pedidoUrl = preg_match('/\A[1-9][0-9]{9,14}\z/', $whatsapp) ? 'https://wa.me/' . $whatsapp : '';
$instagramUrl = preg_match('/\A[a-zA-Z0-9_](?:[a-zA-Z0-9_.]{0,28}[a-zA-Z0-9_])?\z/', $instagram)
    ? 'https://www.instagram.com/' . $instagram . '/'
    : '';
$redes = [['WhatsApp', 'whatsapp', $pedidoUrl], ['Instagram', 'instagram', $instagramUrl]];

require __DIR__ . '/header.php';
?>
<main id="conteudo" class="menu-frame mx-auto">
    <h1 class="visually-hidden"><?= e($categoria) ?></h1>
    <header class="menu-brand d-flex align-items-center justify-content-between">
        <a href="index.php#cardapio" aria-label="Voltar à escolha de cardápios">
            <img class="img-fluid" src="imagens/<?= e($logo) ?>" alt="Kriko’s <?= e($categoria) ?>">
        </a>
        <nav class="menu-socials d-flex flex-column" aria-label="Redes sociais">

            <?php foreach ($redes as [$nomeRede, $icone, $url]): ?>

                <?php if ($url !== ''): ?>

                    <a class="btn social-button d-inline-flex align-items-center justify-content-center" href="<?= e($url) ?>"
                        aria-label="<?= e($nomeRede) ?>" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon social-<?= e($icone) ?>" aria-hidden="true"></span>
                    </a>

                <?php else: ?>

                    <button class="btn social-button d-inline-flex align-items-center justify-content-center" type="button"
                        aria-label="<?= e($nomeRede) ?>" disabled>
                        <span class="social-icon social-<?= e($icone) ?>" aria-hidden="true"></span>
                    </button>

                <?php endif; ?>

            <?php endforeach; ?>

        </nav>
    </header>

    <nav class="d-flex gap-2 mb-3" aria-label="Escolher cardápio">
        <a class="btn btn-outline-dark" href="pedidos.php">Pedidos e pagamentos</a>
        <a class="btn btn-outline-dark<?= $isPastel ? ' active' : '' ?>" href="pastel.php"
            <?= $isPastel ? ' aria-current="page"' : '' ?>>Pastéis</a>
        <a class="btn btn-outline-dark<?= !$isPastel ? ' active' : '' ?>" href="churros.php"
            <?= !$isPastel ? ' aria-current="page"' : '' ?>>Churros</a>
    </nav>

    <?php if ($cardapioIndisponivel): ?>

        <p class="text-dark">O cardápio está temporariamente indisponível.</p>

    <?php elseif ($grupos === []): ?>

        <p class="text-dark">Ainda não há produtos cadastrados neste cardápio.</p>

    <?php endif; ?>

    <?php foreach ($grupos as $indice => [$nomeGrupo, $itens]): ?>

        <section class="menu-group" aria-labelledby="grupo-<?= $indice ?>">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                <h2 id="grupo-<?= $indice ?>"><?= e($nomeGrupo) ?></h2>
                <div class="carousel-controls text-nowrap" hidden>
                    <button class="btn btn-sm" type="button" data-scroll="-1" aria-controls="produtos-<?= $indice ?>"
                        aria-label="Voltar em <?= e($nomeGrupo) ?>">
                        <span aria-hidden="true">‹</span>
                    </button>
                    <button class="btn btn-sm" type="button" data-scroll="1" aria-controls="produtos-<?= $indice ?>"
                        aria-label="Avançar em <?= e($nomeGrupo) ?>">
                        <span aria-hidden="true">›</span>
                    </button>
                </div>
            </div>

            <ul id="produtos-<?= $indice ?>" class="product-carousel list-unstyled d-flex overflow-x-auto mb-0"
                aria-labelledby="grupo-<?= $indice ?>" tabindex="0">

                <?php foreach ($itens as $item): ?>

                    <li class="product-tile d-flex flex-column align-items-start justify-content-center gap-2 rounded-3"
                        title="<?= e($item['descricao'] ?? '') ?>">
                        <span><?= e($item['nome']) ?></span>
                        <span class="product-price fw-semibold">R$ <?= number_format((int) $item['preco'] / 100, 2, ',', '.') ?>
                        </span>
                        <button class="btn btn-sm add-product" type="button" data-add-product
                            data-product-id="<?= (int) $item['id'] ?>" data-product-name="<?= e($item['nome']) ?>"
                            data-product-price="<?= (int) $item['preco'] ?>">Adicionar</button>
                    </li>

                <?php endforeach; ?>

            </ul>
        </section>

    <?php endforeach; ?>

    <div class="d-flex justify-content-end menu-order">
        <button class="btn order-button" type="button" data-bs-toggle="modal" data-bs-target="#pedidoModal">
            Faça Seu Pedido <span class="badge text-bg-dark" id="pedidoQuantidade" aria-live="polite">0</span>
        </button>
    </div>

    <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" id="pedidoForm"
                data-cart-key="krikos-pedido-<?= (int) $_SESSION['krikos_usuario']['id'] ?>-<?= e($_SESSION['krikos_csrf']) ?>">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="pedidoTitulo">Seu pedido</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group mb-3" id="pedidoItens" aria-live="polite"></ul>
                    <p class="d-flex justify-content-between fw-semibold">
                        <span>Total</span>
                        <span id="pedidoTotal">R$ 0,00</span>
                    </p>
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <p class="small">Pastéis e churros vão juntos no mesmo pedido.</p>
                    <nav class="d-flex gap-2 mb-3" aria-label="Continuar adicionando itens">
                        <a class="btn btn-sm btn-outline-dark" href="pastel.php">Adicionar pastéis</a>
                        <a class="btn btn-sm btn-outline-dark" href="churros.php">Adicionar churros</a>
                    </nav>
                    <div class="mb-3">
                        <label class="form-label" for="pedidoMesa">Número da mesa</label>
                        <input class="form-control" id="pedidoMesa" name="mesa_numero" type="number" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="pedidoObservacao">Observação <span class="text-muted">(opcional)
                            </span>
                        </label>
                        <textarea class="form-control" id="pedidoObservacao" name="observacao" rows="2"
                            maxlength="500"></textarea>
                    </div>
                    <p class="small mb-0" id="pedidoMensagem" role="status"></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">Continuar escolhendo
                    </button>
                    <button class="btn order-button" id="pedidoEnviar" type="submit" disabled>Enviar pedido</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="assets/cardapio.js?v=16" defer></script>

<?php require __DIR__ . '/footer.php'; ?>
