<?php

namespace Kriko\Entity;

use DateTime;

class Pagamento
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private ?int $valor;
    private ?string $metodo;
    private ?string $status;
    private ?DateTime $data_criacao;
    private ?DateTime $data_pagamento;
    private ?string $referencia = null;

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getPedido()
    {
        return $this->pedido;
    }

    public function getValorTotal()
    {
        return $this->valor;
    }

    public function getMetodo()
    {
        return $this->metodo;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDataCriacao()
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
    public function setId(?int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function setPedido(?Pedido $pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setValorTotal(float $valor)
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

    public function setDataCriacao(DateTime $data_criacao)
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
