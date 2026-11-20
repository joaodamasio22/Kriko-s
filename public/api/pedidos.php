<?php

use Kriko\Controller\UserControl;
use Kriko\Repository\CardapioRepository;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder(int $status, array $dados): never
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, ['erro' => 'Método não permitido.']);
}

require_once dirname(__DIR__, 2) . '/includes/auth.php';

$perfil = krikosPerfilAutenticado();
if ($perfil !== 'GARCOM') {
    responder($perfil === null ? 401 : 403, [
        'erro' => 'Entre com um acesso de garçom para registrar pedidos.',
    ]);
}

$origem = $_SERVER['HTTP_ORIGIN'] ?? '';
$protocolo = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
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

if (!is_array($dados) || !csrfValido($dados['csrf'] ?? null)) {
    responder(403, ['erro' => 'Atualize a página e tente novamente.']);
}

$mesaNumero = filter_var($dados['mesa_numero'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$observacao = trim(is_string($dados['observacao'] ?? null) ? $dados['observacao'] : '');
$itens = $dados['itens'] ?? null;

if (
    $mesaNumero === false ||
    !is_array($itens) ||
    $itens === [] ||
    count($itens) > 30 ||
    mb_strlen($observacao) > 500
) {
    responder(422, ['erro' => 'Confira a mesa e os itens do pedido.']);
}

$quantidades = [];
foreach ($itens as $item) {
    if (!is_array($item)) {
        responder(422, ['erro' => 'Há um item inválido no pedido.']);
    }
    $produtoId = filter_var($item['produto_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    $quantidade = filter_var($item['quantidade'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 50],
    ]);
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

if (
    isset($_SESSION['krikos_ultimo_pedido']) &&
    time() - (int) $_SESSION['krikos_ultimo_pedido'] < 10
) {
    responder(429, ['erro' => 'Aguarde alguns segundos antes de enviar outro pedido.']);
}

try {
    require dirname(__DIR__, 2) . '/bootstrap.php';

    $conexao = $entityManager->getConnection();
    $mesa = $conexao->fetchAssociative(
        "SELECT id FROM mesas WHERE numero_mesa = ? AND ativo = 'S'",
        [$mesaNumero],
    );
    if ($mesa === false) {
        responder(422, ['erro' => 'Essa mesa não está cadastrada como ativa.']);
    }

    $adminId = $conexao->fetchOne(
        "SELECT id FROM administradores WHERE id = ? AND ativo = 'S' AND perfil = 'GARCOM'",
        [(int) $_SESSION['krikos_usuario']['id']],
    );
    if ($adminId === false) {
        responder(403, ['erro' => 'O acesso de garçom não está mais ativo. Entre novamente.']);
    }
    $disponiveis = new CardapioRepository($conexao)->disponiveis(array_keys($quantidades));
    if (count($disponiveis) !== count($quantidades)) {
        responder(422, ['erro' => 'Um produto não está mais disponível neste cardápio.']);
    }

    $resultado = new UserControl($entityManager)->pedidoComItens(
        [
            'mesa_id' => (int) $mesa['id'],
            'admin_id' => (int) $adminId,
            'status' => 'ABERTO',
            'observacao' => $observacao,
        ],
        $quantidades,
    );
    $pedidoId = $resultado->pedido->getId();

    $_SESSION['krikos_ultimo_pedido'] = time();
    responder(201, [
        'pedido_id' => $pedidoId,
        'total_centavos' => $resultado->pedido->getValorTotal(),
    ]);
} catch (Throwable $erro) {
    error_log('Falha ao registrar pedido web: ' . $erro->getMessage());
    responder($erro instanceof DomainException ? 422 : 500, [
        'erro' =>
            $erro instanceof DomainException
                ? $erro->getMessage()
                : 'Não foi possível registrar agora. Tente novamente ou fale com a equipe.',
    ]);
}
