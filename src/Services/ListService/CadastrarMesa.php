<?php

namespace Kriko\Services\ListService;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Mesa;

class CadastrarMesa
{
    public function __construct(private EntityManager $entity_manager)
    { }

    public function cadastrarMesa(int $numero_mesa, string $descricao, string $ativo)
    {
        $conn = $this->entity_manager->getConnection();
        $conn->beginTransaction();

        try{
            $mesa = new Mesa();
            $mesa
                ->setNumero_mesa($numero_mesa)
                ->setDescricao($descricao)
                ->setAtivo($ativo)
            ;

            $this->entity_manager->persist($mesa);
            $this->entity_manager->flush();

            echo "Mesa cadastrada com sucesso!\n";

            $conn->commit();

        } catch (Exception $e) {
            echo "Erro ao cadastrar mesa: " . $e->getMessage() . "\n";
            $conn->rollBack();

        }
    }
}
