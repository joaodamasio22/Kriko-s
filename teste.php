<?php

use Kriko\Services\CriandoProdutos\CriandoProdutos;

require_once 'bootstrap.php';

// Categoria
$dadosCategoria = [
    'nome' => 'Pastel',
    'descricao' => 'Melhor pastel da Cidade'
];

(new CriandoProdutos($entityManager))->criarCategoria($dadosCategoria);

// Mesa
$dadosMesa = [
    'numeroMesa' => 1,
    'descricao' => 'Próximo a Janela',
    'ativo' => 'S'
];

(new CriandoProdutos($entityManager))->criarMesa($dadosMesa);

// Administrador
$dadosAdministrador = [
            'nome' => 'João',
            'email' => 'joao@gmail.com',
            'senha' => '12345678',
            'perfil' => 'MASTER',
            'ativo' => 'S',
];

(new CriandoProdutos($entityManager))->criarAdministrador($dadosAdministrador);

// Produto
$dadosProduto = [
    'categoria_id' => 1,
    'nome' => 'Frango com Catupiry',
    'descricao' => 'Com muito Catupiry',
    'preco' => 2000,
    'ativo' => 'S'
    ];
    
    (new CriandoProdutos($entityManager))->criarProduto($dadosProduto);
    
//        --->>>>   TUDO ACIMA ESTÁ CORRETO!!!!!!!    <<<<---
