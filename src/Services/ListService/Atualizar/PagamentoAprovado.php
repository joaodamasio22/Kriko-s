<?php

namespace Kriko\Services\ListService\Atualizar;

use Doctrine\ORM\EntityManager;
use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Services\PagamentoPresencial;

class PagamentoAprovado extends Handler
{
    public function __construct(private readonly EntityManager $entityManager) {}

    protected function executar(OperacaoDTO $dto): void
    {
        $pagamento = $dto->pagamento;
        $pedido = $pagamento?->getPedido();
        $responsavelId = filter_var($dto->obter('responsavel_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$pedido || !$responsavelId) {
            throw new DomainException('Informe o pagamento e o responsável pelo recebimento.');
        }
        (new PagamentoPresencial($this->entityManager->getConnection()))->confirmar(
            (int) $pedido->getId(), $responsavelId, $pedido->getFormaPagamento(), (int) $pagamento->getValorTotal()
        );
        $this->entityManager->refresh($pedido);
        $this->entityManager->refresh($pagamento);
    }
}
