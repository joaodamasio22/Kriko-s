<?php require __DIR__ . '/includes/master.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Master | Kriko's</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="painel-body">

    <header class="painel-header">

        <div class="marca">
            <h1>KRIKO'S</h1>
            <span>PASTÉIS E CHURROS</span>
        </div>

        <div class="admin">
            <span>👤</span>
            <strong>MASTER</strong>
        </div>

    </header>

    <main class="painel-container">
        <?php if ($erro !== ''): ?><p role="alert"><?= masterEsc($erro) ?></p><?php endif; ?>

        <div class="titulo-painel">
            <h2>Painel Administrativo</h2>
            <p>Gerencie categorias, produtos e mesas.</p>
        </div>

        <!-- MENU DE NAVEGAÇÃO -->

        <nav class="menu-master">
            <a href="#categorias">Categorias</a>
            <a href="#produtos">Produtos</a>
            <a href="#mesas">Mesas</a>
        </nav>


        <!-- CATEGORIAS -->

        <section class="painel-section" id="categorias">

            <h2>📁 Gerenciar Categorias</h2>

            <form class="form-master" method="post" action="master.php">
                <input type="hidden" name="csrf" value="<?= masterEsc($_SESSION['krikos_csrf']) ?>">
                <input type="hidden" name="tipo" value="categoria">

                <label>Nome da categoria</label>

                <input
                    type="text"
                    name="nome_categoria"
                    placeholder="Ex: Pastéis"
                    required
                >

                <label>Descrição</label>

                <textarea
                    name="descricao_categoria"
                    placeholder="Descrição da categoria"
                ></textarea>

                <button type="submit" class="btn-master">
                    Cadastrar Categoria
                </button>

            </form>

            <h3>Categorias cadastradas</h3>

            <div class="tabela-container">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>

                    <tbody><?php foreach ($categorias as $categoria): ?>
                        <tr><td><?= (int) $categoria['id'] ?></td><td><?= masterEsc($categoria['nome']) ?></td><td><?= masterEsc($categoria['descricao']) ?></td></tr>
                        <?php endforeach; ?></tbody>

                </table>

            </div>

        </section>


        <!-- PRODUTOS -->

        <section class="painel-section" id="produtos">

            <h2>🍟 Gerenciar Produtos</h2>

            <form class="form-master" method="post" action="master.php">
                <input type="hidden" name="csrf" value="<?= masterEsc($_SESSION['krikos_csrf']) ?>">
                <input type="hidden" name="tipo" value="produto">

                <label>Categoria</label>

                <select name="id_categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>"><?= masterEsc($categoria['nome']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Nome do produto</label>

                <input
                    type="text"
                    name="nome_produto"
                    placeholder="Ex: Pastel de carne"
                    required
                >

                <label>Descrição</label>

                <textarea
                    name="descricao_produto"
                    placeholder="Descrição do produto"
                ></textarea>

                <label>Preço (R$)</label>

                <input
                    type="number"
                    name="preco"
                    placeholder="0.00"
                    min="0"
                    step="0.01"
                    required
                >

                <label>Status</label>

                <select name="ativo" required>
                    <option value="S">Ativo</option>
                    <option value="N">Inativo</option>
                </select>

                <button type="submit" class="btn-master">
                    Cadastrar Produto
                </button>

            </form>

            <h3>Produtos cadastrados</h3>

            <div class="tabela-container">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Categoria</th>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody><?php foreach ($produtos as $produto): ?>
                        <tr><td><?= (int) $produto['id'] ?></td><td><?= masterEsc($produto['categoria_nome']) ?></td><td><?= masterEsc($produto['nome']) ?></td><td>R$ <?= number_format($produto['preco'] / 100, 2, ',', '.') ?></td><td><span class="status <?= $produto['ativo'] === 'S' ? 'ativo' : 'inativo' ?>"><?= $produto['ativo'] === 'S' ? 'Ativo' : 'Inativo' ?></span></td></tr>
                        <?php endforeach; ?></tbody>

                </table>

            </div>

        </section>


        <!-- MESAS -->

        <section class="painel-section" id="mesas">

            <h2>🪑 Gerenciar Mesas</h2>

            <form class="form-master" method="post" action="master.php">
                <input type="hidden" name="csrf" value="<?= masterEsc($_SESSION['krikos_csrf']) ?>">
                <input type="hidden" name="tipo" value="mesa">

                <label>Número da mesa</label>

                <input
                    type="number"
                    name="numero_mesa"
                    placeholder="Ex: 01"
                    min="1"
                    required
                >

                <label>Descrição</label>

                <input
                    type="text"
                    name="descricao_mesa"
                    placeholder="Ex: Mesa próxima à janela"
                >

                <label>Status</label>

                <select name="ativo" required>
                    <option value="S">Ativo</option>
                    <option value="N">Inativo</option>
                </select>

                <button type="submit" class="btn-master">
                    Cadastrar Mesa
                </button>

            </form>

            <h3>Mesas cadastradas</h3>

            <div class="tabela-container">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Número</th>
                            <th>Descrição</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody><?php foreach ($mesas as $mesa): ?>
                        <tr><td><?= (int) $mesa['id'] ?></td><td><?= (int) $mesa['numero_mesa'] ?></td><td><?= masterEsc($mesa['descricao']) ?></td><td><span class="status <?= $mesa['ativo'] === 'S' ? 'ativo' : 'inativo' ?>"><?= $mesa['ativo'] === 'S' ? 'Ativo' : 'Inativo' ?></span></td></tr>
                        <?php endforeach; ?></tbody>

                </table>

            </div>

        </section>

    </main>

    <footer class="painel-footer">
        <p>KRIKO'S - Pastéis e Churros</p>
    </footer>

</body>
</html>
