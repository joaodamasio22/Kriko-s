<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Pagamento;
use Kriko\Entity\Pedido;

class CadastrarPagamento {

public function __construct(private EntityManager $entityManager)
    { }

    public function cadastrarPagamento(Pedido $pedido, string $metodo, string $status)
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $pagamento = new Pagamento();
            $pagamento
                ->setPedido($pedido)
                ->setValorTotal($pedido->getValorTotal())
                ->setMetodo($metodo)
                ->setStatus($status)
                ->setDataCriacao(new DateTime())
            ;

            $this->entityManager->persist($pagamento);
            $this->entityManager->flush();

            // throw new Exception("Erro ao cadastrar item do pedido");
            $conn->commit();
 
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;

        }
    }
}
