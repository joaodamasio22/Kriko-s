<?php

namespace Kriko\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Produto
{
    private ?int $id = null;
    private int $categoria; //Tem que ser tipo Categoria
    private string $nome;
    private ?string $descricao = null;
    private int $preco;
    private string $ativo = 'S';

    private Collection $itensPedido;

    public function __construct()
    {
        $this->itensPedido = new ArrayCollection();
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

    public function getAtivo()
    {
        return $this->ativo;
    }

    // Setters
    public function setCategoria(int $categoria)
    {
        $this->categoria = $categoria; //Tem que ser tipo Categoria
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

    public function setAtivo(string $ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }
    
}
