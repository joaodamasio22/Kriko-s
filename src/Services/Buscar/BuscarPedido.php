<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pedido;
use Exception;

class BuscarPedido extends Handler
{
    public function __construct(private readonly EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $id = filter_var($dto->obter('pedido_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new Exception('Identificador pedido_id inválido.');
        }

        $entidade = $this->entityManager->find(Pedido::class, $id);
        if ($entidade === null) {
            throw new Exception('Pedido não encontrado(a).');
        }

        $dto->pedido = $entidade;
    }
}
