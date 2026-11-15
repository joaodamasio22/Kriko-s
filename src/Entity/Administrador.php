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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getPerfil(): string
    {
        return $this->perfil;
    }

    public function getAtivo(): string
    {
        return $this->ativo;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setSenha(string $senha): self
    {
        $this->senha = $senha;
        return $this;
    }

    public function setPerfil(string $perfil): self
    {
        $opcoesValidas = ['MASTER', 'GARCOM', 'COZINHA'];

        if (!in_array($perfil, $opcoesValidas, true)) {
            throw new Exception(
                'Perfil inválido. Opções válidas: ' . implode(', ', $opcoesValidas),
            );
        }

        $this->perfil = $perfil;
        return $this;
    }

    public function setAtivo(string $ativo): self
    {
        $this->ativo = $ativo;
        return $this;
    }

    public function setDataCadastro(DateTime $dataCadastro): self
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }
}
