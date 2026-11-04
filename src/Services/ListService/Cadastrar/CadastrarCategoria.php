<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Chain\Handler;
use Kriko\Entity\Categoria;

class CadastrarCategoria extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function processar(?array $dados = null): bool
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try{
            $categoria = new Categoria();
            $categoria
                ->setNome($dados['nome'])
                ->setDescricao($dados['descricao'])
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

        return parent::processar($dados);
    }
}
