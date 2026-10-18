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
    private bool $ativo = true;
    private ?\DateTime $dataCadastro = null;

    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
        $this->dataCadastro = new \DateTime();
    }   

}
