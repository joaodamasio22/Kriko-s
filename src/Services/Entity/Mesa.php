<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mesa
{
    private ?int $id = null;
    private int $numeroMesa;
    private ?string $descricao = null;
    private bool $ativa = true;

    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
    }

}
