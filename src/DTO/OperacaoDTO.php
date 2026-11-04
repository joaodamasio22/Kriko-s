<?php

namespace Kriko\DTO;

use Kriko\Entity\Administrador;
use Kriko\Entity\Categoria;
use Kriko\Entity\ItemPedido;
use Kriko\Entity\Mesa;
use Kriko\Entity\Pedido;
use Kriko\Entity\Produto;
use Kriko\Entity\Pagamento;

class OperacaoDTO
{
    public function __construct(
        public array $dados = [],
        public ?Administrador $administrador = null,
        public ?Categoria $categoria = null,
        public ?ItemPedido $itemPedido = null,
        public ?Mesa $mesa = null,
        public ?Pedido $pedido = null,
        public ?Produto $produto = null,
        public ?Pagamento $pagamento = null,
    ) {}

    public function obter(string $campo, mixed $padrao = null): mixed
    {
        return $this->dados[$campo] ?? $padrao;
    }

    public function definir(string $campo, mixed $valor): void
    {
        $this->dados[$campo] = $valor;
    }

    public function possui(string $campo): bool
    {
        return array_key_exists($campo, $this->dados);
    }
}
