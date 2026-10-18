<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Categoria
{
    private ?int $id = null;
    private string $nome;
    private ?string $descricao = null;

    private Collection $produtos;

    public function __construct()
    {
        $this->produtos = new ArrayCollection();
    }

}