<?php

use Kriko\Repository\PedidoRepository;

require_once dirname(__DIR__) . '/includes/auth.php';
krikosExigirPerfil(['COZINHA']);

$erro = '';
$mensagem = $_SESSION['cozinha_mensagem'] ?? '';
unset($_SESSION['cozinha_mensagem']);
$colunas = ['ABERTO' => 'Na fila', 'EM_PREPARACAO' => 'Em preparo', 'PRONTO' => 'Prontos'];
$pedidos = array_fill_keys(array_keys($colunas), []);
try {
    require dirname(__DIR__) . '/bootstrap.php';
    $conexao = $entityManager->getConnection();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrfValido($_POST['csrf'] ?? null)) {
            throw new DomainException('A página expirou. Atualize e tente novamente.');
        }
        $id = filter_var($_POST['pedido_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $novoStatus = $_POST['status'] ?? '';
        $anterior = match ($novoStatus) {
            'EM_PREPARACAO' => 'ABERTO',
            'PRONTO' => 'EM_PREPARACAO',
            default => null,
        };
        if (!$id || $anterior === null) {
            throw new DomainException('Atualização de pedido inválida.');
        }
        $alterados = $conexao->executeStatement(
            'UPDATE pedidos SET status = ? WHERE id = ? AND status = ?',
            [$novoStatus, $id, $anterior],
        );
        $_SESSION['cozinha_mensagem'] =
            $alterados === 1
                ? 'Pedido #' . $id . ' atualizado.'
                : 'Esse pedido já foi atualizado. Confira a fila.';
        header('Location: cozinha.php', true, 303);
        exit();
    }
    $lista = new PedidoRepository($conexao)->fila();
    foreach ($lista as $pedido) {
        $pedidos[$pedido['status']][] = $pedido;
    }
} catch (Throwable $falha) {
    error_log('Cozinha Krikos: ' . $falha->getMessage());
    $erro =
        $falha instanceof DomainException
            ? $falha->getMessage()
            : 'Não foi possível carregar os pedidos. Tente atualizar a fila.';
    http_response_code($falha instanceof DomainException ? 422 : 503);
}

$page = 'cozinha';
$title = 'Cozinha | Kriko’s';
require dirname(__DIR__) . '/includes/header.php';
?>
<main class="kitchen-shell">
    <header class="kitchen-header">
        <div>
            <a class="kitchen-brand" href="cozinha.php">KRIKO’S</a>
            <p>PASTÉIS E CHURROS · COZINHA</p>
        </div>
        <div class="kitchen-actions">
            <span><?= e($_SESSION['krikos_usuario']['nome']) ?></span>
            <a class="kitchen-button kitchen-button-outline" href="cozinha.php">Atualizar fila</a>
            <form method="post" action="sair.php">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                <button class="kitchen-button kitchen-button-outline" type="submit">Sair</button>
            </form>
        </div>
    </header>
    <section class="kitchen-intro" aria-labelledby="cozinhaTitulo">
        <p class="kitchen-eyebrow">CAPRICHO EM CADA PEDIDO</p>
        <h1 id="cozinhaTitulo">Bora preparar?</h1>
        <p>Acompanhe as mesas e deixe tudo no ponto.</p>
        <small>Pedidos mais antigos aparecem primeiro. Atualize a fila para ver novos pedidos.</small>
    </section>

    <?php if ($erro): ?>
        <p class="kitchen-alert" role="alert"><?= e($erro) ?></p>
    <?php endif; ?>

    <?php if ($mensagem): ?>
        <p class="kitchen-alert" role="status"><?= e($mensagem) ?></p>
    <?php endif; ?>

    <div class="kitchen-board">

        <?php foreach ($colunas as $status => $titulo): ?>

            <section class="kitchen-column" aria-labelledby="coluna-<?= $status ?>">
                <h2 id="coluna-<?= $status ?>"><?= $titulo ?> <span><?= count($pedidos[$status]) ?></span>
                </h2>

                <?php if (!$pedidos[$status] && !$erro): ?>
                    <p class="kitchen-empty">Nenhum pedido
                        <?= $status === 'ABERTO' ? 'na fila' : ($status === 'PRONTO' ? 'pronto' : 'em preparo') ?> por aqui.</p>
                <?php endif; ?>

                <?php foreach ($pedidos[$status] as $pedido): ?>

                    <article class="kitchen-ticket">
                        <header>
                            <strong>Mesa <?= e($pedido['numero_mesa']) ?></strong>
                            <span>#<?= e($pedido['id']) ?></span>
                        </header>
                        <p class="kitchen-time">Recebido em
                            <?= e((new DateTimeImmutable($pedido['data_pedido']))->format('d/m H:i')) ?></p>
                        <ul>
                            <?php foreach ($pedido['itens'] as $item): ?>
                                <li>
                                    <b><?= e($item['quantidade']) ?>×</b> <?= e($item['nome']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if ($pedido['observacao']): ?>
                            <p class="kitchen-note">
                                <strong>Observações:</strong>
                                <br><?= nl2br(e($pedido['observacao'])) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($status !== 'PRONTO'): ?>

                            <form method="post" action="cozinha.php">
                                <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                                <input type="hidden" name="pedido_id" value="<?= e($pedido['id']) ?>">
                                <input type="hidden" name="status" value="<?= $status === 'ABERTO' ? 'EM_PREPARACAO' : 'PRONTO' ?>">
                                <button class="kitchen-button" type="submit">
                                    <?= $status === 'ABERTO' ? 'Iniciar preparo' : 'Marcar como pronto' ?></button>
                            </form>

                        <?php else: ?>
                            <p class="kitchen-ready">Pronto para servir!</p>
                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endforeach; ?>

    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
