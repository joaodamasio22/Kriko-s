<?php

require_once "bootstrap.php";

use Kriko\Entity\Administrador;
use Kriko\Entity\Categoria;
use Kriko\Entity\ItemPedido;
use Kriko\Entity\Mesa;
use Kriko\Entity\Pagamento;
use Kriko\Entity\Pedido;
use Kriko\Entity\Produto;

// Categoria
$categoria1 = new Categoria();
$categoria1
    ->setNome("Pastel")
    ->setDescricao("Melhor Pastel da cidade")
;
echo "Categoria cadastrada com sucesso!\n";
$entityManager->persist($categoria1);

// ----------------------------------------

// Produto
$produto1 = new Produto();
$produto1
    ->setCategoria($categoria1)
    ->setNome('Pastel de Carne')
    ->setDescricao('Pastel de carne com bastante recheio')
    ->setPreco(1490)
    ->setAtivo('S')
;

echo "Produto cadastrado com sucesso!\n";
$entityManager->persist($produto1);
;

// ----------------------------------------

// Mesa
$mesa1 = new Mesa();
$mesa1
    ->setNumero_mesa(1)
    ->setDescricao('Mesa 1')
    ->setAtivo('S')
;

echo "Mesa cadastrada com sucesso!\n";
$entityManager->persist($mesa1);
;

// ----------------------------------------

// Administrador
$admin = new Administrador();
$admin
    ->setNome('Teste')
    ->setEmail('teste@gmail.com')
    ->setSenha('123456')
    ->setPerfil('MASTER')
    ->setAtivo('S')
    ->setDataCadastro(new DateTime())
;

echo "Administrador cadastrado com sucesso!\n";
$entityManager->persist($admin);

// ----------------------------------------

// Pedido
$pedido = new Pedido();
$pedido
    ->setMesa($mesa1)
    ->setAdministrador($admin)
    ->setStatus('ABERTO')
    ->setDataPedido(new DateTime())
;

echo "Pedido cadastrado com sucesso!\n";
$entityManager->persist($pedido);

// ----------------------------------------

// ItemPedido
$itemPedido = new ItemPedido();
$itemPedido
    ->setPedido($pedido)
    ->setProduto($produto1)
    ->setQuantidade(2)
    ->setPrecoUnitario(1490)
;

echo "Itens do pedido cadastrado com sucesso!\n";
$entityManager->persist($itemPedido);
;

// ----------------------------------------

// Atualizar Pedido
$pedido
    ->setValorTotal($itemPedido->getQuantidade(), $itemPedido->getPrecoUnitario())
    ->setFormaPagamento('DINHEIRO')
    ->setStatusPagamento('PENDENTE')
    ->setStatus('EM_PREPARACAO')
    ->setObservacao('Sem azeitona')
;

echo "Pedido atualizado com sucesso!\n";
$entityManager->persist($pedido);

// ----------------------------------------

// Pedido Finalizado
$pedido->setStatus('PRONTO');
echo "Pedido PRONTO! Venha retirar\n";
$entityManager->persist($pedido);

// ----------------------------------------

// Pagamento
$pagamento = new Pagamento();
$pagamento
    ->setPedido($pedido)
    ->setValor($pedido->getValorTotal())
    ->setMetodo('DINHEIRO')
    ->setStatus('PENDENTE')
    ->setDataCriacao(new DateTimeImmutable())
    ;

echo "Pagamento cadastrado com sucesso!\n";
$entityManager->persist($pagamento);

// ----------------------------------------

// Pagamento Atualizado
$pagamento
    ->setStatus('APROVADO')
    ->setDataPagamento(new DateTimeImmutable())
    ->setReferencia('123456789')
;

echo "Pagamento realizado com sucesso!\n";
$entityManager->persist($pagamento);
$entityManager->flush();

//        --->>>>   TUDO ACIMA ESTÁ CORRETO!!!!!!!    <<<<---

