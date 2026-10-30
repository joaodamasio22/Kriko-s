<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Administrador
{
    private ?int $id = null;
    private string $nome;
    private string $email;
    private string $senha;
    private string $perfil;
    private string $ativo = 'T';
    private ?\DateTime $dataCadastro = null;

    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
        $this->dataCadastro = new \DateTime();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getPerfil()
    {
        return $this->perfil;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro
    }

    // Setters
    public function setNome(string $nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function setNome(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function setSenha(string $senha)
    {
        $this->senha = $senha;
        return $this;
    }

    public function setPerfil(string $nome)
    {
        $this->perfil = $perfil;
        return $this;
    }

    public function setAtivo(string $ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }

    public function setPerfil(DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }
}
