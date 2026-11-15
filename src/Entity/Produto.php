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
    private int $preco;
    private string $ativo = 'S';

    private Collection $itensPedido;

    public function __construct()
    {
        $this->itensPedido = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getPreco(): int
    {
        return $this->preco;
    }

    public function getAtivo(): string
    {
        return $this->ativo;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function setPreco(int $preco): self
    {
        $this->preco = $preco;
        return $this;
    }

    public function setAtivo(string $ativo): self
    {
        $this->ativo = $ativo;
        return $this;
    }
}
