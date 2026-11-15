<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\ItemPedido;

class CadastrarItemPedido extends Handler
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function executar(OperacaoDTO $dto): void
    {
        if (!$dto->pedido || !$dto->produto || $dto->produto->getAtivo() !== 'S') {
            throw new DomainException('Pedido ou produto indisponível.');
        }
        if (
            $dto->pedido->getStatusPagamento() === 'PAGO' ||
            in_array($dto->pedido->getStatus(), ['FINALIZADO', 'CANCELADO'], true)
        ) {
            throw new DomainException(
                'Não é permitido adicionar itens a um pedido pago ou encerrado.',
            );
        }
        $itemPedido = new ItemPedido();
        $itemPedido
            ->setPedido($dto->pedido)
            ->setProduto($dto->produto)
            ->setQuantidade($dto->obter('quantidade'))
            ->setPrecoUnitario($dto->produto->getPreco());

        $this->entityManager->persist($itemPedido);
        $dto->itemPedido = $itemPedido;

        $dto->pedido->adicionarItem($itemPedido);
        $dto->pedido->setValorTotal($dto->pedido->getValorTotal() + $itemPedido->getSubtotal());
    }
}
