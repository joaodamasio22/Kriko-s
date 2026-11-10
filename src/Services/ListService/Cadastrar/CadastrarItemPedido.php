<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Exception;
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
            ->setPrecoUnitario($dto->produto->getPreco())
        ;

        $this->entityManager->persist($itemPedido);
        $dto->itemPedido = $itemPedido;

        if(!$dto->pedido) {
            throw new Exception('Pedido não cadastrado, tente novamente');
        }

        $valor_total = $dto->pedido->getValorTotal();
        $valorPedido = $dto->obter('quantidade') * $dto->obter('preco_unitario');
        $valor_total = $valor_total + $valorPedido; 

        $dto->pedido->setValorTotal($valor_total);
    }
}
