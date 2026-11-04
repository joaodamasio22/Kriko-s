<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Categoria;
use Exception;

class BuscarCategoria extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $categoriaId = $dto->obter('categoria_id');

        $categoria = $this->entityManager->find(Categoria::class, $categoriaId);

        if ($categoria === null) {
            throw new Exception('Categoria não encontrada.');
        }

        $dto->categoria = $categoria;
    }
}
