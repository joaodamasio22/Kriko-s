<?php

namespace Kriko\Entity;

class ItemPedido
{
    private ?int $id = null;
    private ?Pedido $pedido = null;
    private ?Produto $produto = null;
    private int $quantidade;
    private string $precoUnitario;

<<<<<<< Updated upstream
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
        return $this->precoUnitario
    }


=======
>>>>>>> Stashed changes
}
