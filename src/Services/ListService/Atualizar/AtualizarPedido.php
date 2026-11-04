<?php

namespace Kriko\Services\ListService\Atualizar;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Pedido;

class AtualizarPedido {
    public function __construct(private EntityManager $entityManager)
    { }

    public function atualizarPedido(Pedido $pedido_id, int $quantidade, int $precoUnitario,  string $formaPagamento, string $statusPagamento, string $status, string $observacao)
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $pedido = $this->entityManager->getRepository(Pedido::class)->findOneBy(['id' => $$pedido_id]);    

            $pedido
                ->setValorTotal($quantidade, $precoUnitario)
                ->setFormaPagamento($formaPagamento)
                ->setStatusPagamento($statusPagamento)
                ->setStatus($status)
                ->setObservacao($observacao)
            ;

            $this->entityManager->persist($pedido);
            $this->entityManager->flush();

            // throw new Exception("Erro ao cadastrar item do pedido");

            $conn->commit();
            
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;

        }
    }
}
