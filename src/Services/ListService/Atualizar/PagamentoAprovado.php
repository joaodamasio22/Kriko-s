<?php

namespace Kriko\Services\ListService\Atualizar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Pagamento;

class PagamentoAprovado {
    public function __construct(private EntityManager $entityManager)
    { }

    public function pagamentoAprovado(Pagamento $pagamento_id, string $status, string $referencia)
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $pagamento = $this->entityManager->getRepository(Pagamento::class)->findOneBy(['id' => $pagamento_id]);

            $pagamento
                ->setStatus($status)
                ->setDataPagamento(new DateTime())
                ->setReferencia($referencia)
            ;

            echo "Pagamento realizado com sucesso!\n";
            
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
