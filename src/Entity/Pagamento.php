<?php

namespace Kriko\Entity;

use DateTime;

class Pagamento
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private int $valor;
    private string $metodo;
    private string $status;
    private ?DateTime $data_criacao = null;
    private ?DateTime $data_pagamento = null;
    private ?string $referencia = null;

    public function __construct()
    {
        $this->data_criacao = new DateTime();
        $this->data_pagamento = null;

    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function getValorTotal(): int
    {
        return $this->valor;
    }

    public function getValor(): int
    {
        return $this->valor;
    }

    public function getMetodo(): string
    {
        return $this->metodo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDataCriacao(): ?DateTime
    {
        return $this->data_criacao;
    }
    
    public function getDataPagamento()
    {
        return $this->data_pagamento;
    }

    public function getReferencia()
    {
        return $this->referencia;
    }

    // Setters
    public function setPedido(?Pedido $pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setValor(int $valor)
    {
        $this->valor = $valor;
        return $this;
    }

    public function setMetodo(string $metodo)
    {
        $this->metodo = $metodo;
        return $this;
    }

    public function setStatus(string $status)
    {
        $opcoesValidaes = ['PENDENTE', 'APROVADO', "RECUSADO",'CANCELADO'];
        
        if(!in_array($status, $opcoesValidaes)) {
            throw new \InvalidArgumentException("Status inválido. Opções válidas: " . implode(", ", $opcoesValidaes));
        }

        $this->status = $status;
        return $this;
    }

    public function setDataCriacao(DateTime $data_criacao): self
    {
        $this->data_criacao = $data_criacao;
        return $this;
    }

    public function setDataPagamento(DateTime $data_pagamento)
    {
        $this->data_pagamento = $data_pagamento;
        return $this;
    }

    public function setReferencia(?string $referencia)
    {
        $this->referencia = $referencia;
        return $this;
    }

}
