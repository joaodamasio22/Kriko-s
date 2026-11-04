<?php

namespace Kriko\Controller;

use Doctrine\ORM\EntityManager;
use Kriko\Entity\Administrador;
use Kriko\Entity\Categoria;
use Kriko\Entity\ItemPedido;
use Kriko\Entity\Mesa;
use Kriko\Entity\Pagamento;
use Kriko\Entity\Pedido;
use Kriko\Entity\Produto;
use Kriko\Services\ListService\Atualizar\AtualizarPedido;
use Kriko\Services\ListService\Atualizar\PagamentoAprovado;
use Kriko\Services\ListService\Cadastrar\CadastrarAdministrador;
use Kriko\Services\ListService\Cadastrar\CadastrarCategoria;
use Kriko\Services\ListService\Cadastrar\CadastrarItemPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarMesa;
use Kriko\Services\ListService\Cadastrar\CadastrarPagamento;
use Kriko\Services\ListService\Cadastrar\CadastrarPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarProduto;

class UserControl
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function index()
    {
        // VERIFICAÇÃO PARA QUAL METODO IRÁ EXECUTAR
    }

    public function categoria(?array $dados = null)
    {
        (new CadastrarCategoria($this->entityManager))->processar($dados);        
    }

    public function produto(Categoria $categoria, string $nome, string $descricao, int $precoUnitario, string $ativo)
    {
        (new CadastrarProduto($this->entityManager))->cadastrarProduto($categoria, $nome, $descricao, $precoUnitario, $ativo);
    }

    public function mesa(int $numeroMesa, string $descricao, string $ativo)
    {
        (new CadastrarMesa($this->entityManager))->cadastrarMesa($numeroMesa, $descricao, $ativo);
    }

    public function administrador(?array $dados = null)
    {
        (new CadastrarAdministrador($this->entityManager))->processar($dados);
    }

    public function pedido(Mesa $mesa, Administrador $admin, string $status)
    {
        (new CadastrarPedido($this->entityManager))->cadastrarPedido($mesa, $admin, $status);
    }

    public function itemPedido(Pedido $pedido, Produto $produto, int $quantidade, int $precoUnitario)
    {
        (new CadastrarItemPedido($this->entityManager))->cadastrarItemPedido($pedido, $produto, $quantidade, $precoUnitario);
    }

    public function atualizarPedido(Pedido $pedido_id, int $quantidade, int $precoUnitario, string $forma_pagamento, string $status_pagamento, string $status, string $observacao)
    {
        (new AtualizarPedido($this->entityManager))->atualizarPedido($pedido_id, $quantidade, $precoUnitario, $forma_pagamento, $status_pagamento, $status, $observacao);
    }

    public function pagamento(Pedido $pedido, string $metodo, string $status)
    {
        (new CadastrarPagamento($this->entityManager))->cadastrarPagamento($pedido, $metodo, $status);
    }

    public function pagamentoAprovado(Pagamento $pagamento_id, string $status, string $referencia)
    {
        (new PagamentoAprovado($this->entityManager))->pagamentoAprovado($pagamento_id, $status, $referencia);
    }
}
