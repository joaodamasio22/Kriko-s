<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Produto
{
    private ?int $id = null;
    private ?Categoria $categoria = null;
    private string $nome;
    private ?string $descricao = null;
    private string $preco;
    private int $estoque = 0;
    private bool $disponivel = true;
    private ?\DateTime $dataCadastro = null;

    private Collection $itensPedido;

    public function __construct()
    {
        $this->itensPedido = new ArrayCollection();
        $this->dataCadastro = new \DateTime();
    }

}