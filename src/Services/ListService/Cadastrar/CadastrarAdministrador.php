<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Administrador;

class CadastrarAdministrador extends Handler
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function executar(OperacaoDTO $dto): void
    {
        $administrador = new Administrador();

        $administrador
            ->setNome($dto->obter('nome'))
            ->setEmail($dto->obter('email'))
            ->setSenha(
                password_get_info($dto->obter('senha'))['algo'] !== null
                    ? $dto->obter('senha')
                    : password_hash($dto->obter('senha'), PASSWORD_DEFAULT),
            )
            ->setPerfil($dto->obter('perfil'))
            ->setAtivo($dto->obter('ativo', 'S'));

        $this->entityManager->persist($administrador);

        $dto->administrador = $administrador;
    }
}
