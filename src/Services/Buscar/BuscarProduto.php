<?php

namespace Kriko\Services\Buscar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Produto;
use Exception;

class BuscarProduto extends Handler
{
    public function __construct(private readonly EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $id = filter_var($dto->obter('produto_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($id === false) {
            throw new Exception('Identificador produto_id inválido.');
        }

        $entidade = $this->entityManager->find(Produto::class, $id);
        if ($entidade === null) {
            throw new Exception('Produto não encontrado(a).');
        }

        $dto->produto = $entidade;
    }
}