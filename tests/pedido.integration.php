<?php

// Usa o banco configurado e desfaz todos os registros criados no teste.
require dirname(__DIR__) . '/bootstrap.php';

$conexao = $entityManager->getConnection();
$conexao->beginTransaction();
try {
    $controle = new Kriko\Controller\UserControl($entityManager);
    $sufixo = bin2hex(random_bytes(6));
    $admin = $controle->administrador([
        'nome' => 'Teste ' . $sufixo,
        'email' => $sufixo . '@example.test',
        'senha' => password_hash('Teste temporario 123', PASSWORD_DEFAULT),
        'perfil' => 'GARCOM',
        'ativo' => 'S',
    ])->administrador;
    $numero = (int) $conexao->fetchOne('SELECT COALESCE(MAX(numero_mesa), 0) + 1 FROM mesas');
    $mesa = $controle->mesa([
        'numeroMesa' => $numero,
        'descricao' => 'Teste temporário',
        'ativo' => 'S',
    ])->mesa;
    $categoria = $controle->categoria([
        'nome' => 'Teste ' . $sufixo,
        'descricao' => 'Teste temporário',
    ])->categoria;
    $produtos = [];
    foreach ([1400, 2000] as $preco) {
        $produtos[] = $controle->produto([
            'categoria_id' => $categoria->getId(),
            'nome' => 'Produto ' . $preco,
            'descricao' => '',
            'preco' => $preco,
            'ativo' => 'S',
        ])->produto;
    }
    $pedido = $controle->pedido([
        'mesa_id' => $mesa->getId(),
        'admin_id' => $admin->getId(),
        'status' => 'ABERTO',
        'observacao' => 'Teste',
    ])->pedido;
    if ($pedido->getValorTotal() !== 0) {
        throw new RuntimeException('Pedido deve começar com total zero.');
    }
    foreach ($produtos as $produto) {
        // Preço enviado pelo cliente não pode alterar o preço ou total do pedido.
        $controle->itemPedido([
            'pedido_id' => $pedido->getId(),
            'produto_id' => $produto->getId(),
            'quantidade' => 2,
            'preco_unitario' => 1,
        ]);
    }
    $controle->atualizarPedido([
        'pedido_id' => $pedido->getId(),
        'forma_pagamento' => 'PIX',
        'statusPagamento' => 'PENDENTE',
    ]);
    $controle->pagamento(['pedido_id' => $pedido->getId(), 'status' => 'PENDENTE']);
    $total = (int) $conexao->fetchOne('SELECT valor_total FROM pedidos WHERE id = ?', [
        $pedido->getId(),
    ]);
    $pagamento = (int) $conexao->fetchOne('SELECT valor FROM pagamentos WHERE pedido = ?', [
        $pedido->getId(),
    ]);
    $itens = $conexao->fetchAllAssociative(
        'SELECT quantidade, preco_unitario FROM itens_pedido WHERE pedido = ? ORDER BY id',
        [$pedido->getId()],
    );
    if (
        $total !== 6800 ||
        $pagamento !== 6800 ||
        count($itens) !== 2 ||
        (int) $itens[0]['preco_unitario'] !== 1400
    ) {
        throw new RuntimeException('Itens, total ou pagamento incorretos.');
    }
    $presencial = new Kriko\Services\PagamentoPresencial($conexao);
    $exigirRejeicao = static function (callable $acao): void {
        try {
            $acao();
        } catch (DomainException) {
            return;
        }
        throw new RuntimeException('A operação deveria ter sido bloqueada.');
    };
    $exigirRejeicao(fn() => $presencial->finalizar($pedido->getId(), $admin->getId()));
    $exigirRejeicao(fn() => $presencial->confirmar($pedido->getId(), $admin->getId(), 'PIX', 6800));
    $controle->atualizarPedido(['pedido_id' => $pedido->getId(), 'status' => 'PRONTO']);
    $exigirRejeicao(fn() => $presencial->confirmar($pedido->getId(), $admin->getId(), 'PIX', 1));
    $cozinha = $controle->administrador([
        'nome' => 'Cozinha teste',
        'email' => 'cozinha-' . $sufixo . '@example.test',
        'senha' => password_hash('Teste temporario 123', PASSWORD_DEFAULT),
        'perfil' => 'COZINHA',
        'ativo' => 'S',
    ])->administrador;
    $exigirRejeicao(
        fn() => $presencial->confirmar($pedido->getId(), $cozinha->getId(), 'PIX', 6800),
    );
    $controle->atualizarPedido(['pedido_id' => $pedido->getId(), 'forma_pagamento' => 'DINHEIRO']);
    $pagamentoId = (int) $conexao->fetchOne('SELECT id FROM pagamentos WHERE pedido = ?', [
        $pedido->getId(),
    ]);
    $controle->pagamentoAprovado([
        'pagamento_id' => $pagamentoId,
        'responsavel_id' => $admin->getId(),
    ]);
    $registro = $conexao->fetchAssociative('SELECT * FROM pagamentos WHERE pedido = ?', [
        $pedido->getId(),
    ]);
    $auditoria = json_decode($registro['referencia'], true, 512, JSON_THROW_ON_ERROR);
    if (
        $registro['status'] !== 'APROVADO' ||
        $registro['metodo'] !== 'DINHEIRO' ||
        !$registro['data_pagamento'] ||
        $auditoria['responsavel_id'] !== $admin->getId()
    ) {
        throw new RuntimeException(
            'Confirmação sem registro correto de responsável, data ou método.',
        );
    }
    if (
        $conexao->fetchOne('SELECT status_pagamento FROM pedidos WHERE id = ?', [
            $pedido->getId(),
        ]) !== 'PAGO'
    ) {
        throw new RuntimeException('Pedido não foi marcado como pago.');
    }
    $presencial->confirmar($pedido->getId(), $admin->getId(), 'PIX', 6800);
    $aposRepeticao = $conexao->fetchAssociative('SELECT * FROM pagamentos WHERE pedido = ?', [
        $pedido->getId(),
    ]);
    if ($registro !== $aposRepeticao) {
        throw new RuntimeException('Uma confirmação repetida alterou o registro.');
    }
    $presencial->finalizar($pedido->getId(), $admin->getId());
    $presencial->finalizar($pedido->getId(), $admin->getId());
    if (
        $conexao->fetchOne('SELECT status FROM pedidos WHERE id = ?', [$pedido->getId()]) !==
        'FINALIZADO'
    ) {
        throw new RuntimeException('Pedido não finalizado.');
    }
    $master = $controle->administrador([
        'nome' => 'Master teste',
        'email' => 'master-' . $sufixo . '@example.test',
        'senha' => password_hash('Teste temporario 123', PASSWORD_DEFAULT),
        'perfil' => 'MASTER',
        'ativo' => 'S',
    ])->administrador;
    $outro = $controle->pedido([
        'mesa_id' => $mesa->getId(),
        'admin_id' => $admin->getId(),
        'status' => 'ABERTO',
        'observacao' => 'Teste master',
    ])->pedido;
    $controle->itemPedido([
        'pedido_id' => $outro->getId(),
        'produto_id' => $produtos[0]->getId(),
        'quantidade' => 1,
    ]);
    $controle->atualizarPedido([
        'pedido_id' => $outro->getId(),
        'status' => 'PRONTO',
        'statusPagamento' => 'PENDENTE',
    ]);
    if ($outro->getFormaPagamento() !== null) {
        throw new RuntimeException('Pedido novo não deve ter forma de pagamento definida.');
    }
    if (
        (int) $conexao->fetchOne('SELECT COUNT(*) FROM pagamentos WHERE pedido = ?', [
            $outro->getId(),
        ]) !== 0
    ) {
        throw new RuntimeException('Pagamento não deve ser criado na abertura do pedido.');
    }
    $presencial->confirmar($outro->getId(), $master->getId(), 'PIX', 1400);
    $recebimento = $conexao->fetchAssociative(
        'SELECT metodo, status, valor FROM pagamentos WHERE pedido = ?',
        [$outro->getId()],
    );
    if (
        !$recebimento ||
        $recebimento['metodo'] !== 'PIX' ||
        $recebimento['status'] !== 'APROVADO' ||
        (int) $recebimento['valor'] !== 1400
    ) {
        throw new RuntimeException(
            'Pagamento deve ser criado com o método escolhido no recebimento.',
        );
    }
    if (
        $conexao->fetchOne('SELECT status_pagamento FROM pedidos WHERE id = ?', [
            $outro->getId(),
        ]) !== 'PAGO'
    ) {
        throw new RuntimeException('MASTER não confirmou o pagamento.');
    }
    echo "OK: pedido misto, total e pagamento; confirmação presencial, auditoria, permissões, repetição e finalização.\n";
} finally {
    while ($conexao->getTransactionNestingLevel() > 0) {
        $conexao->rollBack();
    }
}
