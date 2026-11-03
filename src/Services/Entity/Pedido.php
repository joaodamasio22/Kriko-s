<?php

namespace Kriko\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

class Pedido
{
    private ?int $id = null;
    private ?Mesa $id_mesa = null;
    private ?Administrador $id_admin = null;
    private ?DateTime $data_pedido = null;
    private ?string $status = 'aberto';
    private ?string $valor_total;
    private ?string $formaPagamento = null;
    private ?string $statusPagamento = null;
    private ?string $observacao = null;

    private Collection $itens;

    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->data_pedido = new \DateTime();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getMesa()
    {
        return $this->id_mesa;
    }

    public function getAdministrador()
    {
        return $this->id_admin;
    }

    public function getDataPedido()
    {
        return $this->data_pedido;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getValorTotal()
    {
        return $this->valor_total;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    // Setters
    public function setMesa(?Mesa $mesa)
    {
        $this->id_mesa = $mesa;
        return $this;
    }

    public function setAdministrador(?Administrador $administrador)
    {
        $this->id_admin = $administrador;
        return $this;
    }

    public function setDataPedido(DateTime $data_pedido)
    {
        $this->data_pedido = $data_pedido;
        return $this;
    }

    public function setStatus(string $status)
    {
        $opcoesValidas = ['ABERTO','EM_PREPARACAO','PRONTO','FINALIZADO','CANCELADO'];

        if(!in_array(strtoupper($status), $opcoesValidas)) {
            throw new Exception("Status inválido. Opções válidas: " . implode(", ", $opcoesValidas));

        }

        $this->status = $status;
        return $this;
    }

    public function setValorTotal(int $quantidade, float $precoUnitario)
    {
        $this->valor_total = $quantidade * $precoUnitario;
        return $this;
    }

    public function setFormaPagamento(string $formaPagamento)
    {
        $opcoesValidas = ['DINHEIRO','CARTAO','PIX'];
        if(in_array(strtoupper($formaPagamento), $opcoesValidas)) {
            throw new Exception("Forma de pagamento inválida. Opções válidas: " . implode(", ", $opcoesValidas));
        }

        $this->formaPagamento = $formaPagamento;
        return $this;
    }

    public function setStatusPagamento(string $statusPagamento)
    {
        $opcoesValidas = ['PAGO','PENDENTE','CANCELADO'];
        if(in_array(strtoupper($statusPagamento), $opcoesValidas)) {
            throw new Exception("Status de pagamento inválido. Opções válidas: " . implode(", ", $opcoesValidas));
        }

        $this->statusPagamento = $statusPagamento;
        return $this;
    }

    public function setObservacao(?string $observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

}
