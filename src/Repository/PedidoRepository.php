<?php

namespace Kriko\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class PedidoRepository
{
    public function __construct(private readonly Connection $conexao)
    {
    }

    public function fila(): array
    {
        return $this->comItens(
            $this->conexao->fetchAllAssociative(
                "SELECT p.id, p.status, p.observacao, p.data_pedido, m.numero_mesa
             FROM pedidos p JOIN mesas m ON m.id = p.mesa
             WHERE p.status IN ('ABERTO', 'EM_PREPARACAO', 'PRONTO')
             ORDER BY p.data_pedido, p.id",
            ),
        );
    }

    public function recentes(): array
    {
        return $this->comItens(
            $this->conexao->fetchAllAssociative(
                "SELECT p.*, m.numero_mesa, a.nome AS atendente,
             pg.data_pagamento AS recebido_em, pg.referencia AS registro_recebimento
             FROM pedidos p JOIN mesas m ON m.id = p.mesa JOIN administradores a ON a.id = p.admin
             LEFT JOIN pagamentos pg ON pg.id = (
                 SELECT MAX(pg2.id) FROM pagamentos pg2 WHERE pg2.pedido = p.id AND pg2.status = 'APROVADO'
             )
             ORDER BY CASE WHEN p.status IN ('FINALIZADO', 'CANCELADO') THEN 1 ELSE 0 END, p.id DESC LIMIT 100",
            ),
        );
    }

    private function comItens(array $pedidos): array
    {
        if (!$pedidos) {
            return [];
        }
        $itens = [];
        foreach (
            $this->conexao->fetchAllAssociative(
                'SELECT i.pedido, i.quantidade, p.nome FROM itens_pedido i JOIN produtos p ON p.id = i.produto
             WHERE i.pedido IN (?) ORDER BY i.id',
                [array_column($pedidos, 'id')],
                [ArrayParameterType::INTEGER],
            )
            as $item
        ) {
            $itens[$item['pedido']][] = $item;
        }
        foreach ($pedidos as &$pedido) {
            $pedido['itens'] = $itens[$pedido['id']] ?? [];
        }
        return $pedidos;
    }
}
