<?php require dirname(__DIR__) . '/includes/master.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Painel Master | Kriko's</title>

        <link rel="stylesheet" href="master.css?v=2">
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
                <form method="post" action="sair.php">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <button type="submit" class="btn-master">Sair</button>
                </form>
            </div>

        </header>

        <main class="painel-container">

            <?php if ($erro !== ''): ?>
                <p role="alert"><?= e($erro) ?></p>
            <?php endif; ?>

            <div class="titulo-painel">
                <h2>Painel Administrativo</h2>
                <p>Gerencie categorias, produtos e mesas.</p>
            </div>

            <nav class="menu-master">
                <a href="pedidos.php">Pedidos e pagamentos</a>
                <a href="#categorias">Categorias</a>
                <a href="#produtos">Produtos</a>
                <a href="#mesas">Mesas</a>
                <a href="#acessos">Acessos</a>
            </nav>

            <section class="painel-section" id="categorias">

                <h2>📁 Gerenciar Categorias</h2>

                <form class="form-master" method="post" action="master.php">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <input type="hidden" name="tipo" value="categoria">

                    <label>Nome da categoria</label>

                    <input type="text" name="nome_categoria" placeholder="Ex: Pastéis" required>

                    <label>Descrição</label>

                    <textarea name="descricao_categoria" placeholder="Descrição da categoria"></textarea>

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

                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>

                                <tr>
                                    <td><?= (int) $categoria['id'] ?></td>
                                    <td><?= e($categoria['nome']) ?></td>
                                    <td><?= e($categoria['descricao']) ?></td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

            </section>

            <section class="painel-section" id="produtos">

                <h2>🍟 Gerenciar Produtos</h2>

                <form class="form-master" method="post" action="master.php">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <input type="hidden" name="tipo" value="produto">

                    <label>Categoria</label>

                    <select name="id_categoria" required>
                        <option value="">Selecione uma categoria</option>

                        <?php foreach ($categorias as $categoria): ?>

                            <option value="<?= (int) $categoria['id'] ?>"><?= e($categoria['nome']) ?></option>

                        <?php endforeach; ?>

                    </select>

                    <label>Nome do produto</label>

                    <input type="text" name="nome_produto" placeholder="Ex: Pastel de carne" required>

                    <label>Descrição</label>

                    <textarea name="descricao_produto" placeholder="Descrição do produto"></textarea>

                    <label>Preço (R$)</label>

                    <input type="number" name="preco" placeholder="0.00" min="0" step="0.01" required>

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

                        <tbody>
                            <?php foreach ($produtos as $produto): ?>

                                <tr>
                                    <td><?= (int) $produto['id'] ?></td>
                                    <td><?= e($produto['categoria_nome']) ?></td>
                                    <td><?= e($produto['nome']) ?></td>
                                    <td>R$ <?= number_format($produto['preco'] / 100, 2, ',', '.') ?></td>
                                    <td>
                                        <span class="status <?= $produto['ativo'] === 'S' ? 'ativo' : 'inativo' ?>">
                                            <?= $produto['ativo'] === 'S' ? 'Ativo' : 'Inativo' ?></span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

            </section>

            <section class="painel-section" id="mesas">

                <h2>🪑 Gerenciar Mesas</h2>

                <form class="form-master" method="post" action="master.php">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <input type="hidden" name="tipo" value="mesa">

                    <label>Número da mesa</label>

                    <input type="number" name="numero_mesa" placeholder="Ex: 01" min="1" required>

                    <label>Descrição</label>

                    <input type="text" name="descricao_mesa" placeholder="Ex: Mesa próxima à janela">

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

                        <tbody>
                            <?php foreach ($mesas as $mesa): ?>

                                <tr>
                                    <td><?= (int) $mesa['id'] ?></td>
                                    <td><?= (int) $mesa['numero_mesa'] ?></td>
                                    <td><?= e($mesa['descricao']) ?></td>
                                    <td>
                                        <span class="status <?= $mesa['ativo'] === 'S' ? 'ativo' : 'inativo' ?>">
                                            <?= $mesa['ativo'] === 'S' ? 'Ativo' : 'Inativo' ?></span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

            </section>

            <section class="painel-section" id="acessos">
                <h2>Cadastrar acesso</h2>
                <form class="form-master" method="post" action="master.php">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                    <input type="hidden" name="tipo" value="administrador">
                    <label for="acessoNome">Nome</label>
                    <input id="acessoNome" name="nome" maxlength="100" autocomplete="name" required>
                    <label for="acessoEmail">E-mail</label>
                    <input id="acessoEmail" name="email" type="email" maxlength="150" autocomplete="email" required>
                    <label for="acessoSenha">Senha</label>
                    <input id="acessoSenha" name="senha" type="password" minlength="8" autocomplete="new-password"
                        required>
                    <label for="acessoPerfil">Perfil</label>
                    <select id="acessoPerfil" name="perfil" required>
                        <option value="GARCOM">Garçom</option>
                        <option value="COZINHA">Cozinha</option>
                        <option value="MASTER">Master</option>
                    </select>
                    <button type="submit" class="btn-master">Cadastrar acesso</button>
                </form>
            </section>
        </main>

        <footer class="painel-footer">
            <p>KRIKO'S - Pastéis e Churros</p>
        </footer>

    </body>
</html>
