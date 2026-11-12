<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Categoria;

class CadastrarCategoria extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $categoria = new Categoria();

        $categoria
            ->setNome($dto->obter('nome'))
            ->setDescricao($dto->obter('descricao'))
        ;

        $this->entityManager->persist($categoria);

        $dto->categoria = $categoria;

    }
}
