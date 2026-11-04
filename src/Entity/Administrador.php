<?php

namespace Kriko\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

class Administrador
{
    private ?int $id = null;
    private string $nome;
    private string $email;
    private string $senha;
    private string $perfil;
    private string $ativo = 'S';
    private ?DateTime $dataCadastro = null;

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

    // Setters
    public function setNome(string $nome)
    {
        $this->nome = $nome;
        return $this;
    }

        public function setEmail(string $email)
        {
            $this->email = $email;
            return $this;
        }

    public function setSenha(string $senha)
    {
        $this->senha = $senha;
        return $this;
    }

    public function setPerfil(string $perfil)
    {
        $opcoesValidas = ['MASTER', 'GARÇOM', 'COZINHA'];

        if (!in_array($perfil, $opcoesValidas)) {
            throw new Exception("Perfil inválido. Opções válidas: " . implode(", ", $opcoesValidas));
        }

        $this->perfil = $perfil;
        return $this;
    }

    public function setAtivo(string $ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }

    public function setDataCadastro(DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }
}
