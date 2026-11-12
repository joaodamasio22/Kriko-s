<?php

namespace Kriko\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;

final class CardapioRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function listar(string $tipo): array
    {
        [$individuais, $porcoes] = match ($tipo) {
            'pastel' => [
                ['pastel', 'pastéis', 'pasteis'],
                ['porções de pastéis', 'porcoes de pasteis', 'porções de pastel', 'porcoes de pastel'],
            ],
            'churros' => [
                ['churro', 'churros'],
                ['porções de churros', 'porcoes de churros'],
            ],
            default => throw new InvalidArgumentException('Cardápio inválido.'),
        };

        // O ranking soma as unidades de pedidos pagos que não foram cancelados.
        $produtos = $this->connection->fetchAllAssociative(
            <<<'SQL'
            SELECT p.id, p.nome, p.descricao, p.preco,
                   CASE WHEN LOWER(TRIM(c.nome)) IN (:porcoes) THEN 1 ELSE 0 END AS porcao,
                   COALESCE(v.quantidade, 0) AS vendidos
            FROM produtos p
            JOIN categorias c ON c.id = p.categoria
            LEFT JOIN (
                SELECT i.produto, SUM(i.quantidade) AS quantidade
                FROM itens_pedido i
                JOIN pedidos pe ON pe.id = i.pedido
                WHERE pe.status_pagamento = 'PAGO' AND pe.status <> 'CANCELADO'
                GROUP BY i.produto
            ) v ON v.produto = p.id
            WHERE p.ativo = 'S' AND LOWER(TRIM(c.nome)) IN (:categorias)
            ORDER BY vendidos DESC, p.nome, p.id
            SQL,
            ['porcoes' => $porcoes, 'categorias' => array_merge($individuais, $porcoes)],
            ['porcoes' => ArrayParameterType::STRING, 'categorias' => ArrayParameterType::STRING],
        );

        $grupos = ['mais_vendidos' => [], 'individuais' => [], 'porcoes' => []];

        foreach ($produtos as $produto) {
            if ((int) $produto['vendidos'] > 0) {
                $grupos['mais_vendidos'][] = $produto;
            }
            $grupos[$produto['porcao'] ? 'porcoes' : 'individuais'][] = $produto;
        }

        return $grupos;
    }
}
