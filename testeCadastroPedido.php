<?php

require_once "vendor/autoload.php";

use Kriko\Entity\Administrador;
use Kriko\Entity\Categoria;
use Kriko\Entity\ItemPedido;
use Kriko\Entity\Mesa;
use Kriko\Entity\Pagamento;
use Kriko\Entity\Pedido;
use Kriko\Entity\Produto;

require_once "bootstrap.php";

//Categoria
// $categoria1 = new Categoria();
// $categoria1
//     ->setNome("Churros")
//     ->setDescricao("Melhor Churros da cidade")
// ;
// echo "Categoria cadastrada com sucesso!\n";
// $entityManager->persist($categoria1);



// Produto
$p1 = new Produto();
$p1
    ->setCategoria(1)
    ->setNome('Pastel de Carne')
    ->setDescricao('Pastel de carne com bastante recheio')
    ->setPreco(1490)
    ->setAtivo('S')
;

echo "Produto cadastrado com sucesso!\n";
$entityManager->persist($p1);
$entityManager->flush();
// Mesa
// $mesa1 = new Mesa();
// $mesa1
//     ->setNumero_mesa(1)
//     ->setDescricao('Mesa 1')
//     ->setAtivo('S')
// ;

// echo "Mesa cadastrada com sucesso!\n";
// $entityManager->persist($mesa1);
// $entityManager->flush();

// Administrador
// $admin = new Administrador();
// $admin
//     ->setNome('Teste')
//     ->setEmail('teste@gmail.com')
//     ->setSenha('123456')
//     ->setPerfil('MASTER')
//     ->setAtivo('S')
//     ->setDataCadastro(new DateTime())
// ;

// echo "Administrador cadastrado com sucesso!\n";
// $entityManager->persist($admin);
// $entityManager->flush();

// $pedido = new Pedido();
// $pedido
//     ->setMesa(1)
//     ->setAdministrador(1)
//     ->setStatus('ABERTO')
//     ->setDataPedido(new DateTime())
//     ->setStatus('ABERTO')
// ;

// echo "Pedido cadastrado com sucesso!\n";
// $entityManager->persist($pedido);

// $itemPedido = new ItemPedido();
// $itemPedido
//     ->setPedido(1)
//     ->setProduto(1)
//     ->setQuantidade(2)
//     ->setPrecoUnitario(1490)
// ;

// echo "Itens do pedido cadastrado com sucesso!\n";
// $entityManager->persist($itemPedido);
// $entityManager->flush();


// // DEBUGGAR TUDO ABAIXO
// $pedido
//     ->setValorTotal(2, 1490)
//     ->setFormaPagamento('DINHEIRO')
//     ->setStatusPagamento('PENDENTE')
//     ->setStatus('EM_PREPARACAO')
//     ->setObservacao('Sem azeitona')
// ;

// echo "Pedido atualizado com sucesso!\n";
// $entityManager->persist($pedido);


// $pedido->setStatus('PRONTO');
// echo "Pedido PRONTO! Venha retirar\n";
// $entityManager->persist($pedido);


// $pagamento = new Pagamento();
// $pagamento
//     ->setPedido(1)
//     ->setValorTotal(2, 1490)
//     ->setMetodo('DINHEIRO')
//     ->setStatus('PENDENTE')
//     ->setDataCriacao(new DateTime())
// ;

// echo "Pagamento cadastrado com sucesso!\n";
// $entityManager->persist($pagamento);


// $pagamento
//     ->setStatus('PAGO')
//     ->setDataPagamento(new DateTime())
//     ->setReferencia('123456789')
// ;

// echo "Pagamento realizado com sucesso!\n";
// $entityManager->persist($pagamento);
// $entityManager->flush();
