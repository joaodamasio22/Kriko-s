<?php

use Kriko\Services\CriandoProdutos\CriandoProdutos;

require_once 'bootstrap.php';

// // Categoria
// $dadosCategoria = [
//     'nome' => 'Pastel',
//     'descricao' => 'Melhor pastel da Cidade'
// ];

// (new CriandoProdutos($entityManager))->criarCategoria($dadosCategoria);

// // Mesa
// $dadosMesa = [
//     'numeroMesa' => 2,
//     'descricao' => 'Próximo a Entrada',
//     'ativo' => 'N'
// ];

// (new CriandoProdutos($entityManager))->criarMesa($dadosMesa);

// Administrador
$dadosAdministrador = [
            'nome' => 'João Damasio',
            'email' => 'joaoDamasio@gmail.com',
            'senha' => '12345678',
            'perfil' => 'GARCOM',
            'ativo' => 'N',
];

(new CriandoProdutos($entityManager))->criarAdministrador($dadosAdministrador);

// // Produto
// $dadosProduto = [
//     'categoria_id' => 1,
//     'nome' => 'Carne com Queijo',
//     'descricao' => 'Acompanha caldo de Cana',
//     'preco' => 3500,
//     'ativo' => 'S'
//     ];
    
//     (new CriandoProdutos($entityManager))->criarProduto($dadosProduto);
    
// //        --->>>>   TUDO ACIMA ESTÁ CORRETO!!!!!!!    <<<<---
