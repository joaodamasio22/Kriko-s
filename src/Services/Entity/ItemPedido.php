<?php

namespace Kriko\Entity;

class ItemPedido
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private ?Produto $produto = null;
    private int $quantidade;
    private string $precoUnitario;

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getPedido()
    {
        return $this->pedido;
    }

    public function getProduto()
    {
        return $this->produto;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function getPrecoUnitario()
    {
        return $this->precoUnitario;
    }

    // Setters
    public function setId(?int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function setPedido(Pedido $pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setProduto(Produto $produto)
    {
        $this->produto = $produto;
        return $this;
    }

    public function setQuantidade(int $quantidade)
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    public function setPrecoUnitario(string $precoUnitario)
    {
        $this->precoUnitario = $precoUnitario;
        return $this;
    } 

}
