<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Categoria;
use Kriko\Entity\Produto;

class CadastrarProduto {
    public function __construct(private EntityManager $entity_manager)
    { }

    public function cadastrarProduto(Categoria $categoria, string $nome, string $descricao, int $preco, string $ativo)
    {
        $conn = $this->entity_manager->getConnection();
        $conn->beginTransaction();

        try{
            $produto = new Produto();
            $produto
                ->setCategoria($categoria)
                ->setNome($nome)
                ->setDescricao($descricao)
                ->setPreco($preco)
                ->setAtivo($ativo)
            ;

            $this->entity_manager->persist($produto);
            $this->entity_manager->flush();

            echo "Produto cadastrado com sucesso!\n";

            $conn->commit();

        } catch (Exception $e) {
            echo "Erro ao cadastrar produto: " . $e->getMessage() . "\n";
            $conn->rollBack();

        }
    }
}