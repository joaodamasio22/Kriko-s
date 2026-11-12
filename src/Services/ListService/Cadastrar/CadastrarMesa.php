<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Mesa;

class CadastrarMesa extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $mesa = new Mesa();
        $mesa
            ->setNumero_mesa($dto->obter('numeroMesa'))
            ->setDescricao($dto->obter('descricao'))
            ->setAtivo($dto->obter('ativo', 'S'))
        ;

        $this->entityManager->persist($mesa);
        $dto->mesa = $mesa;
    }
}
