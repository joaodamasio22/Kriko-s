<?php

namespace Kriko\Services\CriandoProdutos;

use Doctrine\ORM\EntityManager;
use Kriko\DTO\OperacaoDTO;
use Kriko\Controller\UserControl;

class CriandoProdutos
{
    private readonly UserControl $controle;

    public function __construct(EntityManager $entityManager)
    {
        $this->controle = new UserControl($entityManager);
    }

    public function criarCategoria(array $dados): OperacaoDTO
    {
        return $this->controle->categoria($dados);
    }

    public function criarMesa(array $dados): OperacaoDTO
    {
        return $this->controle->mesa($dados);
    }

    public function criarAdministrador(array $dados): OperacaoDTO
    {
        return $this->controle->administrador($dados);
    }

    public function criarProduto(array $dados): OperacaoDTO
    {
        return $this->controle->produto($dados);
    }
}
