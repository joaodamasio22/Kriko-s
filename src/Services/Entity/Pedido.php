<?php

namespace Kriko\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Pedido
{
    private ?int $id = null;
    private ?Mesa $mesa = null;
    private ?Administrador $administrador = null;
    private ?\DateTime $dataPedido = null;
    private string $status = 'aberto';
    private string $valorTotal = '0.00';
    private ?string $observacao = null;

    private Collection $itens;

    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->dataPedido = new \DateTime();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getMesa()
    {
        return $this->mesa;
    }

    public function getAdministrador()
    {
        return $this->administrador;
    }

    public function getDataPedido()
    {
        return $this->dataPedido;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    // Setters
    public function setMesa(Mesa $mesa)
    {
        $this->mesa = $mesa;
        return $this;
    }

    public function setAdministrador(Administrador $administrador)
    {
        $this->administrador = $administrador;
        return $this;
    }

    public function setDataPedido(DateTime $dataPedido)
    {
        $this->dataPedido = $dataPedido;
        return $this;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    public function setValorTotal(string $valorTotal)
    {
        $this->valorTotal = $valorTotal;
        return $this;
    }

    public function setObservacao(?string $observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

}
