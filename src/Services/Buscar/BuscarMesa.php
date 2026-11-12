<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Mesa;
use Exception;

class BuscarMesa extends Handler
{
    public function __construct(private readonly EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $id = filter_var($dto->obter('mesa_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1],]);

        if ($id === false) {
            throw new Exception('Identificador mesa_id inválido.');
        }

        $entidade = $this->entityManager->find(Mesa::class, $id);
        if ($entidade === null) {
            throw new Exception('Mesa não encontrado(a).');
        }

        if ($entidade->getAtivo() !== 'S') {
            throw new Exception('Mesa inativa.');
        }

        $dto->mesa = $entidade;
    }
}
