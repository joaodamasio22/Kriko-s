<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\ItemPedido;
use Kriko\Entity\Pedido;
use Kriko\Entity\Produto;

class CadastrarItemPedido {
    public function __construct(private EntityManager $entityManager)
    { }

    public function cadastrarItemPedido(Pedido $pedido, Produto $produto, int $quantidade, int $precoUnitario)
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $itemPedido = new ItemPedido();
            $itemPedido
                ->setPedido($pedido)
                ->setProduto($produto)
                ->setQuantidade($quantidade)
                ->setPrecoUnitario($precoUnitario)
            ;

            $this->entityManager->persist($itemPedido);
            $this->entityManager->flush();

            // throw new Exception("Erro ao cadastrar item do pedido");
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;

        }
    }
}
