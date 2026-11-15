<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mesa
{
    private ?int $id = null;
    private int $numero_mesa;
    private ?string $descricao = null;
    private string $ativo = 'S';

    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero_mesa(): int
    {
        return $this->numero_mesa;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getAtivo(): string
    {
        return $this->ativo;
    }

    public function setNumero_mesa(int $numero_mesa): self
    {
        $this->numero_mesa = $numero_mesa;
        return $this;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function setAtivo(string $ativo): self
    {
        $this->ativo = $ativo;
        return $this;
    }
}
