<?php

namespace Kriko\Services\ListService;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Categoria;

class CadastrarCategoria
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function cadastrarCategoria(string $nome, string $descricao)
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $categoria = new Categoria();
            $categoria
                ->setNome($nome)
                ->setDescricao($descricao)
            ;

            $this->entityManager->persist($categoria);
            $this->entityManager->flush();

            
            echo "Categoria cadastrada com sucesso!\n";

            // throw new Exception("Testando Rollback\n");
            $conn->commit();

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
