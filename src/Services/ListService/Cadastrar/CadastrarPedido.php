<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pedido;

class CadastrarPedido extends Handler
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function executar(OperacaoDTO $dto): void
    {
        if (
            $dto->mesa->getAtivo() !== 'S' ||
            $dto->administrador->getAtivo() !== 'S' ||
            $dto->administrador->getPerfil() === 'COZINHA'
        ) {
            throw new \DomainException('Mesa ou atendente indisponível para abrir pedidos.');
        }
        $pedido = new Pedido();
        $pedido
            ->setMesa($dto->mesa)
            ->setAdministrador($dto->administrador)
            ->setStatus($dto->obter('status', 'ABERTO'))
            ->setObservacao($dto->obter('observacao'));

        $this->entityManager->persist($pedido);

        $dto->pedido = $pedido;
    }
}
