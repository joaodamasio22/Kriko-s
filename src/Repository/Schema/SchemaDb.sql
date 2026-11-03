CREATE DATABASE IF NOT EXISTS krikos_db;

USE krikos_db;

CREATE TABLE administradores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(128) NOT NULL,
    perfil ENUM('MASTER','GARÇOM', 'COZINHA') NOT NULL DEFAULT 'ADMIN',
    ativo CHAR(1) NOT NULL DEFAULT('S'),
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mesas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    numero_mesa INT NOT NULL UNIQUE,
    descricao VARCHAR(100),
    ativo CHAR(1) NOT NULL DEFAULT('S')
);

CREATE TABLE categorias (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL UNIQUE,
    descricao VARCHAR(255)
);

CREATE TABLE produtos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    categoria BIGINT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL
    ativo CHAR(1) NOT NULL DEFAULT('S'),

    CONSTRAINT FK_PRODUTO_CATEGORIA
        FOREIGN KEY (categoria)
        REFERENCES categorias(id),

    CONSTRAINT chk_produto_preco
        CHECK (preco >= 0),

    CONSTRAINT chk_produto_estoque
        CHECK (estoque >= 0)
);

CREATE TABLE pedidos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    mesa BIGINT NOT NULL,
    admin BIGINT NOT NULL,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ABERTO','EM_PREPARACAO','PRONTO','FINALIZADO','CANCELADO') NOT NULL DEFAULT 'ABERTO',
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observacao TEXT,

    CONSTRAINT FK_PEDIDO_MESA
        FOREIGN KEY (mesa)
        REFERENCES mesas(id),

    CONSTRAINT FK_PEDIDO_ADMIN
        FOREIGN KEY (admin)
        REFERENCES administradores(id),

    CONSTRAINT chk_pedido_valor
        CHECK (valor_total >= 0)
);

CREATE TABLE itens_pedido (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pedido BIGINT NOT NULL,
    produto BIGINT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,

    CONSTRAINT FK_ITEM_PEDIDO
        FOREIGN KEY (pedido)
        REFERENCES pedidos(id),

    CONSTRAINT FK_ITEM_PRODUTO 
        FOREIGN KEY (produto)
        REFERENCES produtos(id),

    CONSTRAINT chk_item_quantidade
        CHECK (quantidade > 0),

    CONSTRAINT chk_item_preco
        CHECK (preco_unitario >= 0)
);

CREATE TABLE pagamentos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pedido BIGINT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    metodo ENUM('PIX','CREDITO','DEBITO','DINHEIRO') NOT NULL,
    status ENUM('PENDENTE','APROVADO','RECUSADO','CANCELADO') NOT NULL DEFAULT 'PENDENTE',
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_pagamento DATETIME NULL,
    referencia VARCHAR(255) NULL,

    CONSTRAINT fk_pagamento_pedido
        FOREIGN KEY (pedido)
        REFERENCES pedidos(id)
);
