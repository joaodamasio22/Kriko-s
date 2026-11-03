<?php

namespace Kriko\Entity;

class ItemPedido
{
    private ?int $id = null;
    private ?int $pedido = null; // Deve ser do tipo Pedido
    private ?int $produto = null; // Deve ser do tipo Produto
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

    public function setPedido(int $pedido) // Deve ser do tipo pedido
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function setProduto(int $produto) // deve ser do tipo Produto
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
