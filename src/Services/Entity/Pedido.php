<?php

namespace Kriko\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Pedido
{
    private ?int $id = null;
    private ?Mesa $mesa = null;
    private ?Administrador $administrador = null;
    private ?\DateTime $dataPedido = null;
    private string $status = 'aberto';
    private ?string $formaPagamento = null;
    private string $statusPagamento = 'pendente';
    private string $valorTotal = '0.00';
    private ?string $observacao = null;

    /** @var Collection<int, ItemPedido> */
    private Collection $itens;

    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->dataPedido = new \DateTime();
    }

}
