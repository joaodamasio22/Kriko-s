<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pagamento;

class CadastrarPagamento extends Handler {

public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $pagamento = new Pagamento();
        $pagamento
            ->setPedido($dto->obter('pedido'))
            ->setValor($dto->pedido->getValorTotal())
            ->setMetodo($dto->pedido->getFormaPagamento())
            ->setStatus($dto->obter('status'))
            ->setDataCriacao((new DateTimeImmutable()))
        ;

        $this->entityManager->persist($pagamento);
    }
}
