<?php

use Kriko\Controller\UserControl;
use Kriko\Repository\CardapioRepository;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder(int $status, array $dados): never
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, ['erro' => 'Método não permitido.']);
}

require_once dirname(__DIR__, 2) . '/includes/session.php';

$origem = $_SERVER['HTTP_ORIGIN'] ?? '';
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$origemEsperada = $protocolo . '://' . ($_SERVER['HTTP_HOST'] ?? '');
if ($origem !== '' && !hash_equals($origemEsperada, $origem)) {
    responder(403, ['erro' => 'Origem do pedido inválida.']);
}

$corpo = file_get_contents('php://input');
if ($corpo === false || strlen($corpo) > 16384) {
    responder(413, ['erro' => 'Pedido muito grande.']);
}

try {
    $dados = json_decode($corpo, true, 32, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    responder(400, ['erro' => 'Dados do pedido inválidos.']);
}

if (!is_array($dados) || !isset($_SESSION['krikos_csrf']) ||
    !is_string($dados['csrf'] ?? null) || !hash_equals($_SESSION['krikos_csrf'], $dados['csrf'])) {
    responder(403, ['erro' => 'Atualize a página e tente novamente.']);
}

$mesaNumero = filter_var($dados['mesa_numero'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$formaPagamento = $dados['forma_pagamento'] ?? '';
$tipoCardapio = $dados['cardapio'] ?? '';
$observacao = trim(is_string($dados['observacao'] ?? null) ? $dados['observacao'] : '');
$itens = $dados['itens'] ?? null;

if ($mesaNumero === false || !in_array($tipoCardapio, ['pastel', 'churros'], true) ||
    !in_array($formaPagamento, ['PIX', 'DINHEIRO', 'CARTAO_CREDITO', 'CARTAO_DEBITO'], true) ||
    !is_array($itens) || $itens === [] || count($itens) > 30 || mb_strlen($observacao) > 500) {
    responder(422, ['erro' => 'Confira a mesa, o pagamento e os itens do pedido.']);
}

$quantidades = [];
foreach ($itens as $item) {
    if (!is_array($item)) {
        responder(422, ['erro' => 'Há um item inválido no pedido.']);
    }
    $produtoId = filter_var($item['produto_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $quantidade = filter_var($item['quantidade'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 50]]);
    if ($produtoId === false || $quantidade === false) {
        responder(422, ['erro' => 'Há uma quantidade ou produto inválido.']);
    }
    $quantidades[$produtoId] = ($quantidades[$produtoId] ?? 0) + $quantidade;
    if ($quantidades[$produtoId] > 50) {
        responder(422, ['erro' => 'O limite por produto é 50 unidades.']);
    }
}

if (array_sum($quantidades) > 100) {
    responder(422, ['erro' => 'O limite por pedido é 100 unidades.']);
}

if (isset($_SESSION['krikos_ultimo_pedido']) && time() - (int) $_SESSION['krikos_ultimo_pedido'] < 10) {
    responder(429, ['erro' => 'Aguarde alguns segundos antes de enviar outro pedido.']);
}

try {
    require dirname(__DIR__, 2) . '/bootstrap.php';

    $conexao = $entityManager->getConnection();
    $mesa = $conexao->fetchAssociative(
        "SELECT id FROM mesas WHERE numero_mesa = ? AND ativo = 'S'",
        [$mesaNumero]
    );
    if ($mesa === false) {
        responder(422, ['erro' => 'Essa mesa não está cadastrada como ativa.']);
    }

    $menu = (new CardapioRepository($conexao))->listar($tipoCardapio);
    $produtosDoCardapio = [];
    foreach (['mais_vendidos', 'individuais', 'porcoes'] as $grupo) {
        foreach ($menu[$grupo] as $produto) {
            $produtosDoCardapio[(int) $produto['id']] = true;
        }
    }

    $adminConfigurado = filter_var(getenv('KRIKOS_WEB_ORDER_ADMIN_ID') ?: null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($adminConfigurado !== false) {
        $adminId = $conexao->fetchOne("SELECT id FROM administradores WHERE id = ? AND ativo = 'S'", [$adminConfigurado]);
        if ($adminId === false) {
            responder(503, ['erro' => 'O atendente configurado para pedidos não está ativo.']);
        }
    } else {
        $administradores = $conexao->fetchFirstColumn("SELECT id FROM administradores WHERE ativo = 'S' ORDER BY id LIMIT 2");
        if (count($administradores) !== 1) {
            responder(503, ['erro' => 'Configure KRIKOS_WEB_ORDER_ADMIN_ID para registrar pedidos.']);
        }
        $adminId = $administradores[0];
    }

    $precos = [];
    foreach ($quantidades as $produtoId => $_) {
        if (!isset($produtosDoCardapio[$produtoId])) {
            responder(422, ['erro' => 'Um produto não pertence a este cardápio.']);
        }
        $preco = $conexao->fetchOne("SELECT preco FROM produtos WHERE id = ? AND ativo = 'S'", [$produtoId]);
        if ($preco === false) {
            responder(422, ['erro' => 'Um produto do pedido não está mais disponível.']);
        }
        $precos[$produtoId] = (int) $preco;
    }

    $conexao->beginTransaction();
    try {
        $controle = new UserControl($entityManager);
        $resultado = $controle->pedido([
            'mesa_id' => (int) $mesa['id'],
            'admin_id' => (int) $adminId,
            'status' => 'ABERTO',
            'observacao' => $observacao,
        ]);
        $pedidoId = $resultado->pedido?->getId();
        if (!$pedidoId) {
            throw new RuntimeException('O pedido não recebeu identificador.');
        }

        foreach ($quantidades as $produtoId => $quantidade) {
            $controle->itemPedido([
                'pedido_id' => $pedidoId,
                'produto_id' => (int) $produtoId,
                'quantidade' => $quantidade,
                'preco_unitario' => $precos[$produtoId],
            ]);
        }

        $controle->atualizarPedido([
            'pedido_id' => $pedidoId,
            'forma_pagamento' => $formaPagamento,
            'statusPagamento' => 'PENDENTE',
        ]);
        $controle->pagamento(['pedido_id' => $pedidoId, 'status' => 'PENDENTE']);
        $conexao->commit();
    } catch (Throwable $erro) {
        if ($conexao->getTransactionNestingLevel() > 0) {
            $conexao->rollBack();
        }
        throw $erro;
    }

    $_SESSION['krikos_ultimo_pedido'] = time();
    responder(201, [
        'pedido_id' => $pedidoId,
        'total_centavos' => $resultado->pedido->getValorTotal(),
    ]);
} catch (Throwable $erro) {
    error_log('Falha ao registrar pedido web: ' . $erro->getMessage());
    responder(500, ['erro' => 'Não foi possível registrar agora. Tente novamente ou fale com a equipe.']);
}
