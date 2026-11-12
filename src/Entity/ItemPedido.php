<?php

namespace Kriko\Entity;

use InvalidArgumentException;

class ItemPedido
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private ?Produto $produto = null;
    private int $quantidade = 1;
    private int $preco_unitario = 0;

    // Getters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getPrecoUnitario(): int
    {
        return $this->preco_unitario;
    }

    public function getSubtotal(): int
    {
        return $this->quantidade * $this->preco_unitario;
    }

    // Setters

    public function setPedido(?Pedido $pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setProduto(?Produto $produto)
    {
        $this->produto = $produto;
        return $this;
    }

    public function setQuantidade(int $quantidade)
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("A quantidade deve ser maior que zero.");
        }

        $this->quantidade = $quantidade;
        return $this;
    }

    public function setPrecoUnitario(int $preco_unitario)
    {
        if ($preco_unitario < 0) {
            throw new InvalidArgumentException("O preço unitário não pode ser negativo.");
        }

        $this->preco_unitario = $preco_unitario;
        return $this;
    }
}
