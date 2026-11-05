<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\ItemPedido;

class CadastrarItemPedido extends Handler {
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $itemPedido = new ItemPedido();
        $itemPedido
            ->setPedido($dto->pedido)
            ->setProduto($dto->produto)
            ->setQuantidade($dto->obter('quantidade'))
            ->setPrecoUnitario($dto->obter('preco_unitario'))
        ;

        $this->entityManager->persist($itemPedido);
        $dto->itemPedido = $itemPedido;
    }
}
