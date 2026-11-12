<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pagamento;

class CadastrarPagamento extends Handler {

public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        if ($dto->pedido->getStatus() === 'CANCELADO') {
            throw new DomainException('Não é permitido pagar um pedido cancelado.');
        }

        foreach ($dto->pedido->getPagamentos() as $pagamentoExistente) {
            if ($pagamentoExistente->getStatus() === 'PENDENTE') {
                throw new DomainException('Este pedido já possui um pagamento pendente.');
            }
        }

        $pagamento = new Pagamento();
        $pagamento
            ->setPedido($dto->pedido)
            ->setValor($dto->pedido->getValorTotal())
            ->setMetodo($dto->pedido->getFormaPagamento())
            ->setStatus($dto->obter('status', 'PENDENTE'))
            ->setDataCriacao((new DateTime()))
            ->setReferencia($dto->obter('referencia'))
        ;

        $this->entityManager->persist($pagamento);
        $dto->pedido->adicionarPagamento($pagamento);
        $dto->pagamento = $pagamento;
    }
}
