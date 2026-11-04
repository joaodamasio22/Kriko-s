<?php

namespace Kriko\Entity;

use InvalidArgumentException;

class ItemPedido
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private ?Produto $produto = null;
    private int $quantidade = 1;
    private string $preco_unitario = '0.00';

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

    public function getPrecoUnitario(): string
    {
        return $this->preco_unitario;
    }

    public function getSubtotal(): string
    {
        $subtotal = $this->quantidade * (float) $this->preco_unitario;

        return number_format($subtotal, 2, '.', '');
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

    public function setPrecoUnitario(string $preco_unitario)
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $preco_unitario)) {
            throw new InvalidArgumentException(
                "Preço unitário inválido."
            );
        }

        $this->preco_unitario = number_format((float) $preco_unitario, 2, '.', '');
        return $this;
    }
}
