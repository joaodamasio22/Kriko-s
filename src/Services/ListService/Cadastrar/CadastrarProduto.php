<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Produto;

class CadastrarProduto extends Handler
{
    public function __construct(private EntityManager $entityManager)
    { }

    protected function executar(OperacaoDTO $dto): void
    {
        $produto = new Produto();

        $produto->setCategoria($dto->categoria);
        $produto->setNome($dto->obter('nome'));
        $produto->setDescricao($dto->obter('descricao'));
        $produto->setPreco($dto->obter('preco'));
        $produto->setAtivo($dto->obter('ativo', '1'));

        $this->entityManager->persist($produto);

        $dto->produto = $produto;
    }
}
