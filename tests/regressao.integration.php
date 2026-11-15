<?php

error_reporting(E_ALL);
set_error_handler(static function (
    int $nivel,
    string $mensagem,
    string $arquivo,
    int $linha,
): never {
    throw new ErrorException($mensagem, 0, $nivel, $arquivo, $linha);
});
require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__) . '/includes/helpers.php';

use Kriko\Controller\UserControl;
use Kriko\Entity\Administrador;
use Kriko\Entity\Pedido;
use Kriko\Repository\CardapioRepository;
use Kriko\Repository\PedidoRepository;
use Kriko\Services\Autenticacao;

function conferir(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

function rejeitar(callable $acao): void
{
    try {
        $acao();
    } catch (DomainException) {
        return;
    }
    throw new RuntimeException('Operação inválida foi aceita.');
}

$conexao = $entityManager->getConnection();
$conexao->beginTransaction();
try {
    $controle = new UserControl($entityManager);
    $sufixo = bin2hex(random_bytes(6));
    $email = $sufixo . '@example.test';
    $admin = $controle->administrador([
        'nome' => 'Regressão',
        'email' => $email,
        'senha' => 'Teste temporario 123',
        'perfil' => 'GARCOM',
    ])->administrador;
    conferir(
        password_verify('Teste temporario 123', $admin->getSenha()),
        'Senha não foi protegida no cadastro.',
    );
    $autenticacao = new Autenticacao($entityManager);
    conferir(
        $autenticacao->entrar($email, 'Teste temporario 123')['id'] === $admin->getId(),
        'Login falhou.',
    );
    rejeitar(fn() => $autenticacao->entrar($email, 'senha incorreta'));
    $conexao->update('administradores', ['senha' => 'Senha antiga 123'], ['id' => $admin->getId()]);
    $entityManager->refresh($admin);
    $autenticacao->entrar($email, 'Senha antiga 123');
    conferir(
        password_verify('Senha antiga 123', $admin->getSenha()),
        'Senha antiga não foi migrada.',
    );

    $numero = (int) $conexao->fetchOne('SELECT COALESCE(MAX(numero_mesa), 0) + 1 FROM mesas');
    $mesa = $controle->mesa(['numeroMesa' => $numero])->mesa;
    $categoriaId = $conexao->fetchOne("SELECT id FROM categorias WHERE nome = 'Pastel'");
    if ($categoriaId === false) {
        $categoriaId = $controle->categoria(['nome' => 'Pastel'])->categoria->getId();
    }
    $produtos = [];
    foreach ([1401, 2003] as $preco) {
        $produtos[] = $controle->produto([
            'categoria_id' => $categoriaId,
            'nome' => 'Regressão ' . $sufixo . $preco,
            'preco' => $preco,
        ])->produto;
    }
    $inativo = $controle->produto([
        'categoria_id' => $categoriaId,
        'nome' => 'Inativo ' . $sufixo,
        'preco' => 100,
        'ativo' => 'N',
    ])->produto;
    conferir(
        $produtos[0]->getCategoria()->getId() === (int) $categoriaId,
        'Getter de categoria incorreto.',
    );
    $menu = new CardapioRepository($conexao);
    $ids = array_map('intval', $menu->disponiveis([$produtos[0]->getId(), $inativo->getId()]));
    conferir($ids === [$produtos[0]->getId()], 'Validação de produtos inativos incorreta.');
    $listados = array_column($menu->listar('pastel')['individuais'], 'id');
    conferir(
        in_array($produtos[0]->getId(), $listados, false),
        'Produto ativo não aparece no cardápio.',
    );
    $porcaoId = $conexao->fetchOne("SELECT id FROM categorias WHERE nome = 'Porções de pastéis'");
    if ($porcaoId === false) {
        $porcaoId = $controle->categoria(['nome' => 'Porções de pastéis'])->categoria->getId();
    }
    $porcao = $controle->produto([
        'categoria_id' => $porcaoId,
        'nome' => 'Porção ' . $sufixo,
        'preco' => 5000,
    ])->produto;
    conferir(
        count($menu->disponiveis([$porcao->getId()])) === 1,
        'Categoria com acentos não foi reconhecida.',
    );
    conferir(
        in_array($porcao->getId(), array_column($menu->listar('pastel')['porcoes'], 'id'), false),
        'Porção não aparece no grupo correto.',
    );

    $dados = ['mesa_id' => $mesa->getId(), 'admin_id' => $admin->getId()];
    $itens = [$produtos[0]->getId() => 2, $produtos[1]->getId() => 1];
    $gravacoes = new class {
        public int $total = 0;
        public function onFlush(): void
        {
            $this->total++;
        }
    };
    $entityManager->getEventManager()->addEventListener(['onFlush'], $gravacoes);
    $pedido = $controle->pedidoComItens($dados, $itens)->pedido;
    $entityManager->getEventManager()->removeEventListener(['onFlush'], $gravacoes);
    conferir($gravacoes->total === 1, 'Pedido completo deve ser gravado em um único flush.');
    conferir(
        $pedido->getValorTotal() === 4805 && $pedido->getItens()->count() === 2,
        'Itens ou valor em centavos incorretos.',
    );
    conferir(
        $pedido->getStatusPagamento() === 'PENDENTE' && $pedido->getFormaPagamento() === null,
        'Estado inicial incorreto.',
    );
    $pedidoId = $pedido->getId();
    $repositorio = new PedidoRepository($conexao);
    $fila = array_column($repositorio->fila(), null, 'id');
    conferir(count($fila[$pedidoId]['itens']) === 2, 'Fila não carregou todos os itens.');
    $recentes = array_column($repositorio->recentes(), null, 'id');
    conferir(isset($recentes[$pedidoId]), 'Pedido não aparece no atendimento.');
    $rotulos = ['PIX' => 'Pix'];
    conferir(
        ($rotulos[$recentes[$pedidoId]['forma_pagamento'] ?? ''] ?? 'Pagamento a definir') ===
            'Pagamento a definir',
        'Pedido sem forma de pagamento gerou erro.',
    );

    $antes = (int) $conexao->fetchOne('SELECT COUNT(*) FROM pedidos');
    rejeitar(fn() => $controle->pedidoComItens($dados, [$inativo->getId() => 1]));
    rejeitar(fn() => $controle->pedidoComItens($dados, [$produtos[0]->getId() => 51]));
    conferir(
        (int) $conexao->fetchOne('SELECT COUNT(*) FROM pedidos') === $antes,
        'Falha deixou pedido parcial no banco.',
    );
    $controle->categoria(['nome' => 'Após falha ' . $sufixo]);
    conferir(
        (int) $conexao->fetchOne('SELECT COUNT(*) FROM pedidos') === $antes,
        'Entidade inválida foi gravada por outra operação.',
    );
    rejeitar(
        fn() => $controle->atualizarPedido(['pedido_id' => $pedidoId, 'statusPagamento' => 'PAGO']),
    );
    $controle->atualizarPedido(['pedido_id' => $pedidoId, 'forma_pagamento' => 'PIX']);
    $controle->pagamento(['pedido_id' => $pedidoId]);
    rejeitar(fn() => $controle->pagamento(['pedido_id' => $pedidoId]));
    conferir(
        (int) $conexao->fetchOne('SELECT COUNT(*) FROM pagamentos WHERE pedido = ?', [
            $pedidoId,
        ]) === 1,
        'Pagamento duplicado.',
    );
    $controle->atualizarPedido(['pedido_id' => $pedidoId, 'status' => 'CANCELADO']);
    rejeitar(fn() => $controle->atualizarPedido(['pedido_id' => $pedidoId, 'status' => 'ABERTO']));
    rejeitar(
        fn() => $controle->itemPedido([
            'pedido_id' => $pedidoId,
            'produto_id' => $produtos[0]->getId(),
            'quantidade' => 1,
        ]),
    );
    conferir(
        precoCentavos('12,05') === 1205 && precoCentavos('0.01') === 1,
        'Conversão monetária incorreta.',
    );
    rejeitar(fn() => precoCentavos('1e3'));
    echo "OK: autenticação, senhas, cardápio, gravação em lote, centavos, rollback e bloqueio de operações inválidas.\n";
} finally {
    while ($conexao->getTransactionNestingLevel() > 0) {
        $conexao->rollBack();
    }
    restore_error_handler();
}
