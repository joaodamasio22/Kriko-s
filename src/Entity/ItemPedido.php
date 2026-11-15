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

    public function setPedido(?Pedido $pedido): self
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setProduto(?Produto $produto): self
    {
        $this->produto = $produto;
        return $this;
    }

    public function setQuantidade(int $quantidade): self
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        $this->quantidade = $quantidade;
        return $this;
    }

    public function setPrecoUnitario(int $preco_unitario): self
    {
        $this->preco_unitario = $preco_unitario;
        return $this;
    }
}
