<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Administrador;
use Kriko\Entity\Mesa;
use Kriko\Entity\Pedido;

class CadastrarPedido {
    public function __construct(private EntityManager $entityManager)
    { }

    public function cadastrarPedido(Mesa $mesa, Administrador $admin, string $status)
    {        
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $pedido = new Pedido();
            $pedido
                ->setMesa($mesa)
                ->setAdministrador($admin)
                ->setStatus($status)
                ->setDataPedido(new DateTime())
            ;
            $this->entityManager->persist($pedido);
            $this->entityManager->flush();
            
            echo "Pedido cadastrado com sucesso!\n";
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;

        }
    }
}
