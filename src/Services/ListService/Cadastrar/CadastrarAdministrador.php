<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Administrador;

class CadastrarAdministrador extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $administrador = new Administrador();

        $administrador
            ->setNome($dto->obter('nome'))
            ->setEmail($dto->obter('email'))
            ->setSenha($dto->obter('senha'))
            ->setPerfil($dto->obter('perfil'))
            ->setAtivo($dto->obter('ativo'))
            ->setDataCadastro((new DateTime()))
        ;

        $this->entityManager->persist($administrador);

        $dto->administrador = $administrador;

    }
}
