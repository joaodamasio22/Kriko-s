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

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNumero_mesa()
    {
        return $this->numero_mesa;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    // Setters
    public function setNumero_mesa(int $numero_mesa)
    {
        $this->numero_mesa = $numero_mesa;
        return $this;
    }

    public function setDescricao(?string $descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function setAtivo(string $ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }
}
