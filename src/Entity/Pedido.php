<?php

namespace Kriko\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

class Pedido
{
    private ?int $id = null;
    private ?Mesa $mesa = null;
    private ?Administrador $admin = null;
    private ?DateTime $data_pedido = null;
    private ?string $status = 'ABERTO';
    private ?int $valor_total = null;
    private ?string $forma_pagamento = null;
    private ?string $status_pagamento = null;
    private ?string $observacao = null;

    private Collection $itens_pedido;

    public function __construct()
    {
        $this->itens_pedido = new ArrayCollection();
        $this->data_pedido = new DateTime();
    }

    // Getters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesa(): ?Mesa
    {
        return $this->mesa;
    }

    public function getAdministrador(): ?Administrador
    {
        return $this->admin;
    }

    public function getDataPedido(): ?DateTime
    {
        return $this->data_pedido;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getValorTotal(): string
    {
        return $this->valor_total;
    }

    public function getFormaPagamento(): ?string
    {
        return $this->forma_pagamento;
    }

    public function getStatusPagamento(): ?string
    {
        return $this->status_pagamento;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function getItens(): Collection
    {
        return $this->itens_pedido;
    }

    // Setters

    public function setMesa(?Mesa $mesa): self
    {
        $this->mesa = $mesa;
        return $this;
    }

    public function setAdministrador(?Administrador $administrador): self
    {
        $this->admin = $administrador;
        return $this;
    }

    public function setDataPedido(DateTime $data_pedido): self
    {
        $this->data_pedido = $data_pedido;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $opcoesValidas = [
            'ABERTO',
            'EM_PREPARACAO',
            'PRONTO',
            'FINALIZADO',
            'CANCELADO'
        ];

        $status = strtoupper($status);

        if (!in_array($status, $opcoesValidas, true)) { throw new Exception( "Status inválido. Opções válidas: " . implode(", ", $opcoesValidas));
        }

        $this->status = $status;
        return $this;
    }

    public function setValorTotal(float $quantidade, float $preco_unitario): self
    {
        if ($quantidade < 0 || $preco_unitario < 0) {
            throw new Exception("Quantidade e preço unitário devem ser valores não negativos.");
        }

        $this->valor_total = number_format($quantidade * $preco_unitario, 2, '.', '');
        return $this;
    }

    public function setFormaPagamento(string $forma_pagamento): self
    {
        $opcoesValidas = ['PIX','DINHEIRO','CARTAO_CREDITO','CARTAO_DEBITO'];

        $forma_pagamento = strtoupper($forma_pagamento);

        if (!in_array($forma_pagamento, $opcoesValidas, true)) {
            throw new Exception(
                "Forma de pagamento inválida. Opções válidas: " .
                implode(", ", $opcoesValidas)
            );
        }

        $this->forma_pagamento = $forma_pagamento;
        return $this;
    }

    public function setStatusPagamento(string $status_pagamento): self
    {
        $opcoesValidas = ['PAGO', 'PENDENTE', 'ESTORNADO'];

        $status_pagamento = strtoupper($status_pagamento);

        if (!in_array($status_pagamento, $opcoesValidas, true)) {
            throw new Exception(
                "Status de pagamento inválido. Opções válidas: " .
                implode(", ", $opcoesValidas)
            );
        }

        $this->status_pagamento = $status_pagamento;
        return $this;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;
        return $this;
    }

}
