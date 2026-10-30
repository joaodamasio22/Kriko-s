<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mesa
{
    private ?int $id = null;
    private int $numeroMesa;
    private ?string $descricao = null;
    private string $ativa = 'T';

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

    public function getNumeroMesa()
    {
        return $this->numeroMesa;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getAtiva()
    {
        return $this->ativa;
    }

    // Setters
    public function setNumeroMesa(int $numeroMesa)
    {
        $this->numeroMesa = $numeroMesa;
        return $this;
    }

    public function setDescricao(?string $descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function setAtiva(string $ativa)
    {
        $this->ativa = $ativa;
        return $this;
    }
}
