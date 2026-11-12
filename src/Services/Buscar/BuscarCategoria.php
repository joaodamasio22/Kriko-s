<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Categoria;
use Exception;

class BuscarCategoria extends Handler
{
    public function __construct(private readonly EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $id = filter_var($dto->obter('categoria_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($id === false) {
            throw new Exception('Identificador categoria_id inválido.');
        }

        $entidade = $this->entityManager->find(Categoria::class, $id);
        if ($entidade === null) {
            throw new Exception('Categoria não encontrado(a).');
        }

        $dto->categoria = $entidade;
    }
}