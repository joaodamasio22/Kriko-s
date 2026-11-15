<?php

use Kriko\Services\PagamentoPresencial;
use Kriko\Repository\PedidoRepository;

require_once dirname(__DIR__) . '/includes/auth.php';
krikosExigirPerfil(['GARCOM', 'MASTER']);

$metodos = [
    'PIX' => 'Pix',
    'DINHEIRO' => 'Dinheiro',
    'CARTAO_CREDITO' => 'Cartão de crédito',
    'CARTAO_DEBITO' => 'Cartão de débito',
];
$statusNomes = [
    'ABERTO' => 'Na fila',
    'EM_PREPARACAO' => 'Em preparo',
    'PRONTO' => 'Pronto',
    'FINALIZADO' => 'Finalizado',
    'CANCELADO' => 'Cancelado',
];
$erro = '';
$mensagem = $_SESSION['pedidos_mensagem'] ?? '';
unset($_SESSION['pedidos_mensagem']);
$pedidos = [];
$confirmacao = null;

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
        if (!$id) {
            throw new DomainException('Pedido inválido.');
        }
        $servico = new PagamentoPresencial($conexao);
        $responsavelId = (int) $_SESSION['krikos_usuario']['id'];
        if (($_POST['acao'] ?? '') === 'receber') {
            if (($_POST['recebido'] ?? '') !== 'sim') {
                throw new DomainException('Confirme que o valor foi recebido pessoalmente.');
            }
            $metodo = is_string($_POST['metodo'] ?? null) ? $_POST['metodo'] : '';
            $valor = filter_var($_POST['valor_conferido'] ?? null, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);
            if ($valor === false) {
                throw new DomainException('Confira o valor do recebimento novamente.');
            }
            $servico->confirmar($id, $responsavelId, $metodo, $valor);
            $_SESSION['pedidos_mensagem'] =
                'Recebimento do pedido #' . $id . ' confirmado. Você já pode finalizar o pedido.';
        } elseif (($_POST['acao'] ?? '') === 'finalizar') {
            $servico->finalizar($id, $responsavelId);
            $_SESSION['pedidos_mensagem'] = 'Pedido #' . $id . ' finalizado.';
        } else {
            throw new DomainException('Operação inválida.');
        }
        header('Location: pedidos.php', true, 303);
        exit();
    }

    $pedidos = new PedidoRepository($conexao)->recentes();
    foreach ($pedidos as &$pedido) {
        $pedido['recebimento'] = json_decode($pedido['registro_recebimento'] ?? '', true);
        if (
            (string) $pedido['id'] === ($_GET['confirmar'] ?? '') &&
            $pedido['status'] === 'PRONTO' &&
            $pedido['status_pagamento'] === 'PENDENTE'
        ) {
            $confirmacao = $pedido;
        }
    }
    unset($pedido);
} catch (Throwable $falha) {
    error_log('Pedidos e pagamentos: ' . $falha->getMessage());
    $erro =
        $falha instanceof DomainException
            ? $falha->getMessage()
            : 'Não foi possível carregar os pedidos. Tente novamente.';
    http_response_code($falha instanceof DomainException ? 422 : 503);
}

$page = 'cozinha';
$title = 'Pedidos e pagamentos | Kriko’s';
require dirname(__DIR__) . '/includes/header.php';
?>
<main class="kitchen-shell">
    <header class="kitchen-header">
        <div>
            <a class="kitchen-brand" href="pedidos.php">KRIKO’S</a>
            <p>PASTÉIS E CHURROS · ATENDIMENTO</p>
        </div>
        <nav class="kitchen-actions" aria-label="Atendimento">
            <a class="kitchen-button kitchen-button-outline"
                href="<?= e(krikosDestino($_SESSION['krikos_usuario']['perfil'])) ?>">Voltar ao início</a>
            <a class="kitchen-button kitchen-button-outline" href="pedidos.php">Atualizar pedidos</a>
            <form method="post" action="sair.php">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                <button class="kitchen-button kitchen-button-outline" type="submit">Sair</button>
            </form>
        </nav>
    </header>
    <section class="kitchen-intro">
        <p class="kitchen-eyebrow">ATENDIMENTO ATÉ O ÚLTIMO DETALHE</p>
        <h1>Pedidos e pagamentos</h1>
        <p>Receba pessoalmente, confirme aqui e finalize o atendimento.</p>
        <small>Últimos 100 pedidos. Pedidos em andamento aparecem primeiro.</small>
    </section>

    <?php if ($mensagem): ?>
        <p class="kitchen-alert" role="status"><?= e($mensagem) ?></p>
    <?php endif; ?>

    <?php if ($erro): ?>
        <p class="kitchen-alert" role="alert"><?= e($erro) ?></p>
    <?php endif; ?>

    <?php if ($confirmacao): ?>

        <section class="payment-confirm kitchen-ticket" id="confirmacao" aria-labelledby="confirmarTitulo">
            <h2 id="confirmarTitulo">Conferir recebimento</h2>
            <p>Pedido <strong>#<?= (int) $confirmacao['id'] ?></strong> · Mesa <strong>
                    <?= (int) $confirmacao['numero_mesa'] ?></strong>
            </p>
            <p class="fs-3 fw-bold"><?= moeda((int) $confirmacao['valor_total']) ?></p>
            <p>Confira o valor e a forma de pagamento antes de confirmar.</p>
            <form method="post" action="pedidos.php">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                <input type="hidden" name="pedido_id" value="<?= (int) $confirmacao['id'] ?>">
                <input type="hidden" name="acao" value="receber">
                <input type="hidden" name="valor_conferido" value="<?= (int) $confirmacao['valor_total'] ?>">
                <label class="form-label" for="recebimentoMetodo">Forma de pagamento recebida</label>
                <select class="form-select mb-3" id="recebimentoMetodo" name="metodo" required>
                    <option value="">Selecione a forma de pagamento recebida</option>

                    <?php foreach ($metodos as $valor => $nome): ?>
                        <option value="<?= $valor ?>" <?= $confirmacao['forma_pagamento'] === $valor ? ' selected' : '' ?>>
                            <?= $nome ?></option>
                    <?php endforeach; ?>

                </select>
                <label class="d-flex gap-2 align-items-start mb-3">
                    <input class="form-check-input" type="checkbox" name="recebido" value="sim" required> Confirmo que
                    recebi pessoalmente o valor integral deste pedido.
                </label>
                <button class="kitchen-button" type="submit">Confirmar recebimento de
                    <?= moeda((int) $confirmacao['valor_total']) ?></button>
                <a class="btn btn-link payment-cancel" href="pedidos.php">Voltar sem confirmar</a>
            </form>
        </section>

    <?php endif; ?>

    <div class="payment-board">

        <?php foreach ($pedidos as $pedido): ?>

            <article class="kitchen-ticket">
                <header>
                    <strong>Mesa <?= (int) $pedido['numero_mesa'] ?></strong>
                    <span>#<?= (int) $pedido['id'] ?></span>
                </header>
                <p class="kitchen-time"><?= e($pedido['atendente']) ?> ·
                    <?= e((new DateTimeImmutable($pedido['data_pedido']))->format('d/m H:i')) ?></p>
                <p>
                    <span class="badge text-bg-dark"><?= e($statusNomes[$pedido['status']] ?? $pedido['status']) ?></span>
                    <span
                        class="badge <?= $pedido['status_pagamento'] === 'PAGO' ? 'text-bg-success' : 'text-bg-warning' ?>">
                        <?= $pedido['status_pagamento'] === 'PAGO' ? 'Pago' : e($pedido['status_pagamento'] ?: 'Pendente') ?>
                    </span>
                </p>
                <ul>
                    <?php foreach ($pedido['itens'] as $item): ?>
                        <li>
                            <b><?= (int) $item['quantidade'] ?>×</b> <?= e($item['nome']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($pedido['observacao']): ?>
                    <p class="kitchen-note"><?= nl2br(e($pedido['observacao'])) ?></p>
                <?php endif; ?>

                <p class="fw-bold fs-5"><?= moeda((int) $pedido['valor_total']) ?> ·
                    <?= e($metodos[$pedido['forma_pagamento'] ?? ''] ?? 'Pagamento a definir') ?></p>

                <?php if ($pedido['recebido_em']): ?>
                    <p class="small">Recebido em <?= e((new DateTimeImmutable($pedido['recebido_em']))->format('d/m/Y H:i')) ?>
                        <?php if (is_array($pedido['recebimento']) && isset($pedido['recebimento']['responsavel_nome'])): ?>
                            por <?= e($pedido['recebimento']['responsavel_nome']) ?>
                        <?php endif; ?>
                        .</p>
                <?php endif; ?>

                <?php if ($pedido['status'] === 'PRONTO' && $pedido['status_pagamento'] === 'PENDENTE'): ?>

                    <a class="kitchen-button" href="pedidos.php?confirmar=<?= (int) $pedido['id'] ?>#confirmacao">Confirmar
                        recebimento</a>

                <?php elseif ($pedido['status'] === 'PRONTO' && $pedido['status_pagamento'] === 'PAGO'): ?>

                    <form method="post" action="pedidos.php">
                        <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                        <input type="hidden" name="pedido_id" value="<?= (int) $pedido['id'] ?>">
                        <input type="hidden" name="acao" value="finalizar">
                        <button class="kitchen-button" type="submit">Finalizar pedido</button>
                    </form>

                <?php elseif (in_array($pedido['status'], ['ABERTO', 'EM_PREPARACAO'], true)): ?>
                    <p class="small mb-0">Aguardando a cozinha. Confirme o recebimento quando o pedido estiver pronto.</p>
                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    </div>

    <?php if (!$pedidos && !$erro): ?>
        <p class="kitchen-empty">Nenhum pedido registrado ainda.</p>
    <?php endif; ?>

</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
