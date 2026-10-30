<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Produto
{
    private ?int $id = null;
    private Categoria $categoria;
    private string $nome;
    private ?string $descricao = null;
    private int $preco;
    private int $estoque = 0;
    private string $disponivel = 'T';
    private ?\DateTime $dataCadastro = null;

    private Collection $itensPedido;

    public function __construct()
    {
        $this->itensPedido = new ArrayCollection();
        $this->dataCadastro = new \DateTime();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getCategoria(Categoria $categoria)
    {
        return $this->categoria;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getdescricao()
    {
        return $this->descricao;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function getEstoque()
    {
        return $this->estoque;
    }

    public function getDisponivel()
    {
        return $this->disponivel;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro
    }

    // Setters
    public function setCategoria(Categoria $categoria)
    {
        $this->categoria = categoria;
        return $this;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function setDescricao(?string $descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    
    public function setPreco(int $preco)
    {
        $this->preco = $preco;
        return $this;
    }

    public function setEstoque(int $estoque)
    {
        $this->estoque = $estoque;
        return $this;
    }

    public function setDisponivel(int $disponivel)
    {
        $this->disponivele = $disponivel;
        return $this;
    }

    public function setDataCadastro(?DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }
    
}
