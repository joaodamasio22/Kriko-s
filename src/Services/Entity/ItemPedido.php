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
    public fuction getId()
    {
        return $this->id;
    }


}
