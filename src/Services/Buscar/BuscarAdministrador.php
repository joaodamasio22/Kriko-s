<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Administrador;
use Exception;

class BuscarAdministrador extends Handler {
    public function __construct(private readonly EntityManager $entityManager) {}

    protected function executar(OperacaoDTO $dto): void
    {
        $id = filter_var($dto->obter('admin_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1],]);

        if ($id === false) {
            throw new Exception('Identificador admin_id inválido.');
        }

        $entidade = $this->entityManager->find(Administrador::class, $id);
        if ($entidade === null) {
            throw new Exception('Administrador não encontrado(a).');
        }

        if ($entidade->getAtivo() !== 'S') {
            throw new Exception('Administrador inativo.');
        }

        $dto->administrador = $entidade;
    }
}
