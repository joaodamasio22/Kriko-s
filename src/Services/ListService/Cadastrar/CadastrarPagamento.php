<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pagamento;

class CadastrarPagamento extends Handler
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function executar(OperacaoDTO $dto): void
    {
        if (
            $this->entityManager
                ->getConnection()
                ->fetchOne(
                    "SELECT id FROM pagamentos WHERE pedido = ? AND status IN ('PENDENTE', 'APROVADO')",
                    [$dto->pedido->getId()],
                ) !== false
        ) {
            throw new \DomainException('Este pedido já possui um pagamento registrado.');
        }
        $pagamento = new Pagamento();
        $pagamento
            ->setPedido($dto->pedido)
            ->setValor($dto->pedido->getValorTotal())
            ->setMetodo($dto->pedido->getFormaPagamento())
            ->setStatus($dto->obter('status', 'PENDENTE'))
            ->setReferencia($dto->obter('referencia'));

        $this->entityManager->persist($pagamento);
        $dto->pagamento = $pagamento;
    }
}
