<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pedido;

class CadastrarPedido extends Handler {
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        $pedido = new Pedido();
        $pedido
            ->setMesa($dto->mesa)
            ->setAdministrador($dto->administrador)
            ->setDataPedido((new DateTime()))
            ->setStatus($dto->obter('status'))
        ;

        $this->entityManager->persist($pedido);

        $dto->pedido = $pedido;

    }
}
