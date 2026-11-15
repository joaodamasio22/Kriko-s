<?php

use Kriko\Controller\UserControl;
use Kriko\Entity\Administrador;

require_once dirname(__DIR__) . '/includes/auth.php';

if (isset($_SESSION['krikos_usuario']) && !in_array($_SESSION['krikos_usuario']['perfil'] ?? '', ['MASTER', 'GARCOM', 'COZINHA'], true)) {
    unset($_SESSION['krikos_usuario']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['krikos_usuario'])) {
    header('Location: ' . krikosDestino($_SESSION['krikos_usuario']['perfil']), true, 302);
    exit;
}

function painelEsc(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function painelCsrf(): bool
{
    return is_string($_POST['csrf'] ?? null) &&
        isset($_SESSION['krikos_csrf']) &&
        hash_equals($_SESSION['krikos_csrf'], $_POST['csrf']);
}

function painelPost(string $campo): string
{
    return is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
}

function painelExigirPerfil(array $perfis): void
{
    $perfil = $_SESSION['krikos_usuario']['perfil'] ?? '';
    if (!in_array($perfil, $perfis, true)) {
        throw new RuntimeException('Seu perfil não pode executar essa operação.');
    }
}

function painelRedirecionar(string $mensagem): never
{
    $_SESSION['painel_mensagem'] = $mensagem;
    header('Location: painel.php', true, 303);
    exit;
}

function painelPrecoCentavos(string $valor): int
{
    $valor = str_replace(',', '.', $valor);
    if (!preg_match('/\A[0-9]+(?:\.[0-9]{1,2})?\z/', $valor)) {
        throw new InvalidArgumentException('Informe o preço em reais, por exemplo 12,50.');
    }
    [$reais, $centavos] = array_pad(explode('.', $valor, 2), 2, '0');
    return ((int) $reais * 100) + (int) str_pad($centavos, 2, '0');
}

$erro = '';
$mensagem = $_SESSION['painel_mensagem'] ?? '';
unset($_SESSION['painel_mensagem']);
$usuario = $_SESSION['krikos_usuario'] ?? null;
$entityManager = null;

try {
    require dirname(__DIR__) . '/bootstrap.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!painelCsrf()) {
            throw new RuntimeException('A página expirou. Atualize e tente de novo.');
        }

        $acao = painelPost('acao');
        if ($acao === 'entrar') {
            $email = filter_var(painelPost('email'), FILTER_VALIDATE_EMAIL);
            $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
            $admin = $email ? $entityManager->getRepository(Administrador::class)->findOneBy([
                'email' => $email,
                'ativo' => 'S',
            ]) : null;

            if (!$admin || $senha === '') {
                throw new RuntimeException('E-mail ou senha inválidos.');
            }

            $senhaArmazenada = $admin->getSenha();
            $infoHash = password_get_info($senhaArmazenada);
            $usaHash = ($infoHash['algo'] ?? null) !== null;
            $senhaValida = $usaHash
                ? password_verify($senha, $senhaArmazenada)
                : hash_equals($senhaArmazenada, $senha);

            if (!$senhaValida) {
                throw new RuntimeException('E-mail ou senha inválidos.');
            }

            if (!$usaHash) {
                $admin->setSenha(password_hash($senha, PASSWORD_DEFAULT));
                $entityManager->flush();
            }

            session_regenerate_id(true);
            $_SESSION['krikos_usuario'] = [
                'id' => (int) $admin->getId(),
                'nome' => $admin->getNome(),
                'perfil' => $admin->getPerfil(),
            ];
            header('Location: ' . krikosDestino($admin->getPerfil()), true, 303);
            exit;
        }

        if ($acao === 'sair') {
            session_regenerate_id(true);
            unset($_SESSION['krikos_usuario']);
            painelRedirecionar('Sessão encerrada.');
        }

        if (!$usuario) {
            throw new RuntimeException('Entre no painel para continuar.');
        }

        if ($usuario['perfil'] === 'COZINHA') {
            throw new RuntimeException('Use a tela da cozinha para atualizar os pedidos.');
        }

        $controle = new UserControl($entityManager);
        switch ($acao) {
            case 'categoria':
                painelExigirPerfil(['MASTER']);
                $controle->categoria(['nome' => painelPost('nome'), 'descricao' => painelPost('descricao')]);
                painelRedirecionar('Categoria cadastrada.');

            case 'produto':
                painelExigirPerfil(['MASTER']);
                $controle->produto([
                    'categoria_id' => painelPost('categoria_id'),
                    'nome' => painelPost('nome'),
                    'descricao' => painelPost('descricao'),
                    'preco' => painelPrecoCentavos(painelPost('preco')),
                    'ativo' => 'S',
                ]);
                painelRedirecionar('Produto cadastrado.');

            case 'mesa':
                painelExigirPerfil(['MASTER', 'GARCOM']);
                $controle->mesa([
                    'numeroMesa' => painelPost('numeroMesa'),
                    'descricao' => painelPost('descricao'),
                    'ativo' => 'S',
                ]);
                painelRedirecionar('Mesa cadastrada.');

            case 'administrador':
                painelExigirPerfil(['MASTER']);
                $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
                $email = filter_var(painelPost('email'), FILTER_VALIDATE_EMAIL);
                if (!$email || strlen($senha) < 8) {
                    throw new InvalidArgumentException('Confira o e-mail e use uma senha com pelo menos 8 caracteres.');
                }
                $controle->administrador([
                    'nome' => painelPost('nome'),
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'perfil' => painelPost('perfil'),
                    'ativo' => 'S',
                ]);
                painelRedirecionar('Acesso cadastrado.');

            case 'pedido':
                painelExigirPerfil(['MASTER', 'GARCOM']);
                $controle->pedido([
                    'mesa_id' => painelPost('mesa_id'),
                    'admin_id' => $usuario['id'],
                    'status' => 'ABERTO',
                    'observacao' => painelPost('observacao'),
                ]);
                painelRedirecionar('Pedido aberto.');

            case 'itemPedido':
                painelExigirPerfil(['MASTER', 'GARCOM']);
                $controle->itemPedido([
                    'pedido_id' => painelPost('pedido_id'),
                    'produto_id' => painelPost('produto_id'),
                    'quantidade' => painelPost('quantidade'),
                ]);
                painelRedirecionar('Item adicionado ao pedido.');

            case 'atualizarPedido':
                $perfil = $usuario['perfil'];
                painelExigirPerfil(['MASTER', 'GARCOM', 'COZINHA']);
                $dados = ['pedido_id' => painelPost('pedido_id')];
                $status = painelPost('status');
                if ($status !== '') $dados['status'] = $status;
                if ($perfil !== 'COZINHA') {
                    $formaPagamento = painelPost('forma_pagamento');
                    if ($formaPagamento !== '') $dados['forma_pagamento'] = $formaPagamento;
                } elseif (count($dados) === 1) {
                    throw new InvalidArgumentException('A cozinha pode alterar somente o status do pedido.');
                }
                $controle->atualizarPedido($dados);
                painelRedirecionar('Pedido atualizado.');

            case 'pagamento':
                painelExigirPerfil(['MASTER', 'GARCOM']);
                $pedidoId = painelPost('pedido_id');
                $formaPagamento = painelPost('forma_pagamento');
                if ($pedidoId === '' || $formaPagamento === '') {
                    throw new InvalidArgumentException('Selecione o pedido e a forma de pagamento.');
                }
                $controle->atualizarPedido([
                    'pedido_id' => $pedidoId,
                    'statusPagamento' => 'PENDENTE',
                    'forma_pagamento' => $formaPagamento,
                ]);
                $controle->pagamento(['pedido_id' => $pedidoId, 'status' => 'PENDENTE']);
                painelRedirecionar('Pagamento pendente registrado.');

            case 'pagamentoAprovado':
                painelExigirPerfil(['MASTER', 'GARCOM']);
                $controle->pagamentoAprovado([
                    'pagamento_id' => painelPost('pagamento_id'),
                    'responsavel_id' => $usuario['id'],
                ]);
                painelRedirecionar('Pagamento aprovado.');

            default:
                throw new InvalidArgumentException('Operação inválida.');
        }
    }
} catch (Throwable $falha) {
    $erro = $falha instanceof PDOException || $falha instanceof Doctrine\DBAL\Exception
        ? 'Não foi possível acessar o banco de dados.'
        : $falha->getMessage();
    error_log('Painel Kriko’s: ' . $falha->getMessage());
}

$usuario = $_SESSION['krikos_usuario'] ?? null;
$categorias = $mesas = $produtos = $pedidos = $pagamentos = [];
if ($usuario && $entityManager) {
    try {
        $conexao = $entityManager->getConnection();
        $categorias = $conexao->fetchAllAssociative('SELECT id, nome FROM categorias ORDER BY nome');
        $mesas = $conexao->fetchAllAssociative("SELECT id, numero_mesa FROM mesas WHERE ativo = 'S' ORDER BY numero_mesa");
        $produtos = $conexao->fetchAllAssociative("SELECT id, nome, preco FROM produtos WHERE ativo = 'S' ORDER BY nome");
        $pedidos = $conexao->fetchAllAssociative(
            'SELECT p.id, p.status, p.status_pagamento, p.valor_total, m.numero_mesa, a.nome AS atendente
             FROM pedidos p JOIN mesas m ON m.id = p.mesa JOIN administradores a ON a.id = p.admin
             ORDER BY p.id DESC LIMIT 30'
        );
        $pagamentos = $conexao->fetchAllAssociative(
            "SELECT id, pedido, valor, metodo, status FROM pagamentos WHERE status = 'PENDENTE' ORDER BY id DESC LIMIT 30"
        );
    } catch (Throwable $falha) {
        $erro = 'Não foi possível carregar os dados do painel.';
        error_log('Painel Kriko’s: ' . $falha->getMessage());
    }
}

$page = $usuario ? 'admin' : 'login';
$title = $usuario ? 'Kriko’s — painel' : 'Login | Kriko’s';
require dirname(__DIR__) . '/includes/header.php';
?>
<?php if (!$usuario): ?>
<main class="login-shell">
  <div class="login-container">
    <header class="login-brand">
      <img class="login-logo" src="imagens/logo.png" alt="Kriko’s">
      <p class="login-tagline">Sabor que faz você voltar!</p>
    </header>

    <section class="login-box" aria-labelledby="entrarTitulo">
      <div class="login-user-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" role="img"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-5 0-9 2.5-9 5.5V22h18v-2.5C21 16.5 17 14 12 14Z"/></svg>
      </div>
      <h1 id="entrarTitulo">Faça seu login</h1>
      <p class="login-subtitle">Entre com seu e-mail e senha para continuar</p>

      <?php if ($mensagem !== ''): ?><div class="login-alert login-alert-success" role="status"><?= painelEsc($mensagem) ?></div><?php endif; ?>
      <?php if ($erro !== ''): ?><div class="login-alert login-alert-error" role="alert"><?= painelEsc($erro) ?></div><?php endif; ?>

      <form method="post" action="painel.php">
        <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
        <input type="hidden" name="acao" value="entrar">

        <label class="login-field" for="email">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h16a2 2 0 0 0 2-2V6c0-1.1-.9-2-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
          <span class="visually-hidden">E-mail</span>
          <input id="email" name="email" type="email" placeholder="E-mail" autocomplete="username" required autofocus>
        </label>

        <label class="login-field" for="senha">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8h-1V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2Zm-6 9a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm3.1-9H8.9V6a3.1 3.1 0 0 1 6.2 0v2Z"/></svg>
          <span class="visually-hidden">Senha</span>
          <input id="senha" name="senha" type="password" placeholder="Senha" autocomplete="current-password" required>
        </label>

        <button class="login-submit" type="submit">Entrar</button>
      </form>

      <a class="login-forgot" href="esqueci_senha.php">Esqueci minha senha</a>
    </section>

    <footer class="login-footer">© <?= date('Y') ?> KRIKO’S — Todos os direitos reservados.</footer>
  </div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
<?php return; ?>
<?php endif; ?>
<main class="container py-4 py-lg-5">
  <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h2 mb-1">Painel Kriko’s</h1>
      <p class="mb-0 text-body-secondary">Área interna</p>
    </div>
    <a class="btn btn-outline-dark" href="index.php">Ver a loja</a>
  </header>

  <?php if ($mensagem !== ''): ?><div class="alert alert-success" role="status"><?= painelEsc($mensagem) ?></div><?php endif; ?>
  <?php if ($erro !== ''): ?><div class="alert alert-danger" role="alert"><?= painelEsc($erro) ?></div><?php endif; ?>

  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <p class="mb-0">Olá, <strong><?= painelEsc($usuario['nome']) ?></strong> · <?= painelEsc($usuario['perfil']) ?></p>
    <form method="post" action="painel.php">
      <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
      <input type="hidden" name="acao" value="sair">
      <button class="btn btn-sm btn-outline-dark" type="submit">Sair</button>
    </form>
  </div>

  <div class="row g-4">
    <?php if ($usuario['perfil'] === 'MASTER'): ?>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="categoriaTitulo">
        <div class="card-body">
          <h2 class="h5" id="categoriaTitulo">Nova categoria</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="categoria">
            <div class="col-12"><label class="form-label" for="categoriaNome">Nome</label><input class="form-control" id="categoriaNome" name="nome" maxlength="50" required></div>
            <div class="col-12"><label class="form-label" for="categoriaDescricao">Descrição</label><textarea class="form-control" id="categoriaDescricao" name="descricao" maxlength="255" rows="2"></textarea></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Cadastrar categoria</button></div>
          </form>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="produtoTitulo">
        <div class="card-body">
          <h2 class="h5" id="produtoTitulo">Novo produto</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="produto">
            <div class="col-12"><label class="form-label" for="produtoNome">Nome</label><input class="form-control" id="produtoNome" name="nome" maxlength="100" required></div>
            <div class="col-md-7"><label class="form-label" for="produtoCategoria">Categoria</label><select class="form-select" id="produtoCategoria" name="categoria_id" required><option value="">Selecione</option><?php foreach ($categorias as $categoria): ?><option value="<?= (int) $categoria['id'] ?>"><?= painelEsc($categoria['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-5"><label class="form-label" for="produtoPreco">Preço (R$)</label><input class="form-control" id="produtoPreco" name="preco" inputmode="decimal" placeholder="12,50" required></div>
            <div class="col-12"><label class="form-label" for="produtoDescricao">Descrição</label><textarea class="form-control" id="produtoDescricao" name="descricao" rows="2"></textarea></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Cadastrar produto</button></div>
          </form>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="adminTitulo">
        <div class="card-body">
          <h2 class="h5" id="adminTitulo">Novo acesso</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="administrador">
            <div class="col-md-6"><label class="form-label" for="adminNome">Nome</label><input class="form-control" id="adminNome" name="nome" maxlength="100" required></div>
            <div class="col-md-6"><label class="form-label" for="adminEmail">E-mail</label><input class="form-control" id="adminEmail" name="email" type="email" maxlength="150" required></div>
            <div class="col-md-6"><label class="form-label" for="adminSenha">Senha</label><input class="form-control" id="adminSenha" name="senha" type="password" minlength="8" required></div>
            <div class="col-md-6"><label class="form-label" for="adminPerfil">Perfil</label><select class="form-select" id="adminPerfil" name="perfil"><option>GARCOM</option><option>COZINHA</option><option>MASTER</option></select></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Cadastrar acesso</button></div>
          </form>
        </div>
      </section>
    </div>
    <?php endif; ?>

    <?php if (in_array($usuario['perfil'], ['MASTER', 'GARCOM'], true)): ?>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="mesaTitulo">
        <div class="card-body">
          <h2 class="h5" id="mesaTitulo">Nova mesa</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="mesa">
            <div class="col-md-4"><label class="form-label" for="numeroMesa">Número</label><input class="form-control" id="numeroMesa" name="numeroMesa" type="number" min="1" required></div>
            <div class="col-md-8"><label class="form-label" for="mesaDescricao">Descrição</label><input class="form-control" id="mesaDescricao" name="descricao" maxlength="100"></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Cadastrar mesa</button></div>
          </form>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="pedidoNovoTitulo">
        <div class="card-body">
          <h2 class="h5" id="pedidoNovoTitulo">Abrir pedido</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="pedido">
            <div class="col-12"><label class="form-label" for="pedidoMesaPainel">Mesa</label><select class="form-select" id="pedidoMesaPainel" name="mesa_id" required><option value="">Selecione</option><?php foreach ($mesas as $mesa): ?><option value="<?= (int) $mesa['id'] ?>">Mesa <?= (int) $mesa['numero_mesa'] ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><label class="form-label" for="pedidoObsPainel">Observação</label><input class="form-control" id="pedidoObsPainel" name="observacao" maxlength="500"></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Abrir pedido</button></div>
          </form>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="itemPedidoTitulo">
        <div class="card-body">
          <h2 class="h5" id="itemPedidoTitulo">Adicionar item</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="itemPedido">
            <div class="col-md-6"><label class="form-label" for="itemPedidoId">Pedido</label><input class="form-control" id="itemPedidoId" name="pedido_id" type="number" min="1" required></div>
            <div class="col-md-6"><label class="form-label" for="itemPedidoQuantidade">Quantidade</label><input class="form-control" id="itemPedidoQuantidade" name="quantidade" type="number" min="1" max="50" required></div>
            <div class="col-12"><label class="form-label" for="itemPedidoProduto">Produto ativo</label><select class="form-select" id="itemPedidoProduto" name="produto_id" required><option value="">Selecione</option><?php foreach ($produtos as $produto): ?><option value="<?= (int) $produto['id'] ?>"><?= painelEsc($produto['nome']) ?> — R$ <?= number_format((int) $produto['preco'] / 100, 2, ',', '.') ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Adicionar item</button></div>
          </form>
        </div>
      </section>
    </div>
    <?php endif; ?>

    <?php if (in_array($usuario['perfil'], ['MASTER', 'GARCOM', 'COZINHA'], true)): ?>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="atualizarPedidoTitulo">
        <div class="card-body">
          <h2 class="h5" id="atualizarPedidoTitulo">Atualizar pedido</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="atualizarPedido">
            <div class="col-md-4"><label class="form-label" for="atualizarPedidoId">Pedido</label><input class="form-control" id="atualizarPedidoId" name="pedido_id" type="number" min="1" required></div>
            <div class="col-md-8"><label class="form-label" for="pedidoStatus">Status</label><select class="form-select" id="pedidoStatus" name="status"><option value="">Manter status</option><option>ABERTO</option><option>EM_PREPARACAO</option><option>PRONTO</option><option>FINALIZADO</option><option>CANCELADO</option></select></div>
            <?php if ($usuario['perfil'] !== 'COZINHA'): ?>
            <div class="col-12"><label class="form-label" for="pedidoForma">Forma de pagamento <span class="text-body-secondary">(opcional)</span></label><select class="form-select" id="pedidoForma" name="forma_pagamento"><option value="">Manter forma atual</option><option value="PIX">Pix</option><option value="DINHEIRO">Dinheiro</option><option value="CARTAO_CREDITO">Cartão de crédito</option><option value="CARTAO_DEBITO">Cartão de débito</option></select></div>
            <?php endif; ?>
            <div class="col-12"><button class="btn btn-dark" type="submit">Atualizar</button></div>
          </form>
        </div>
      </section>
    </div>
    <?php endif; ?>

    <?php if (in_array($usuario['perfil'], ['MASTER', 'GARCOM'], true)): ?>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="pagamentoTitulo">
        <div class="card-body">
          <h2 class="h5" id="pagamentoTitulo">Registrar pagamento pendente</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="pagamento">
            <div class="col-12"><label class="form-label" for="pagamentoPedidoId">Pedido</label><input class="form-control" id="pagamentoPedidoId" name="pedido_id" type="number" min="1" required></div>
            <div class="col-12"><label class="form-label" for="pagamentoForma">Forma de pagamento</label><select class="form-select" id="pagamentoForma" name="forma_pagamento" required><option value="PIX">Pix</option><option value="DINHEIRO">Dinheiro</option><option value="CARTAO_CREDITO">Cartão de crédito</option><option value="CARTAO_DEBITO">Cartão de débito</option></select></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Registrar pagamento</button></div>
          </form>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-6">
      <section class="card h-100 shadow-sm" aria-labelledby="aprovarPagamentoTitulo">
        <div class="card-body">
          <h2 class="h5" id="aprovarPagamentoTitulo">Aprovar pagamento</h2>
          <form method="post" action="painel.php" class="row g-3">
            <input type="hidden" name="csrf" value="<?= painelEsc($_SESSION['krikos_csrf']) ?>">
            <input type="hidden" name="acao" value="pagamentoAprovado">
            <div class="col-12"><label class="form-label" for="pagamentoId">Pagamento pendente</label><select class="form-select" id="pagamentoId" name="pagamento_id" required><option value="">Selecione</option><?php foreach ($pagamentos as $pagamento): ?><option value="<?= (int) $pagamento['id'] ?>">#<?= (int) $pagamento['id'] ?> · pedido <?= (int) $pagamento['pedido'] ?> · R$ <?= number_format((int) $pagamento['valor'] / 100, 2, ',', '.') ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><label class="form-label" for="pagamentoReferencia">Referência</label><input class="form-control" id="pagamentoReferencia" name="referencia" maxlength="255" required></div>
            <div class="col-12"><button class="btn btn-dark" type="submit">Aprovar pagamento</button></div>
          </form>
        </div>
      </section>
    </div>
    <?php endif; ?>
  </div>

  <section class="card shadow-sm mt-4" aria-labelledby="pedidosTitulo">
    <div class="card-body">
      <h2 class="h5" id="pedidosTitulo">Pedidos recentes</h2>
      <div class="table-responsive">
        <table class="table align-middle mb-0"><thead><tr><th>Pedido</th><th>Mesa</th><th>Status</th><th>Pagamento</th><th>Total</th><th>Atendente</th></tr></thead><tbody>
          <?php foreach ($pedidos as $pedido): ?>
          <tr><th scope="row">#<?= (int) $pedido['id'] ?></th><td><?= (int) $pedido['numero_mesa'] ?></td><td><?= painelEsc($pedido['status']) ?></td><td><?= painelEsc($pedido['status_pagamento'] ?? '—') ?></td><td>R$ <?= number_format((int) $pedido['valor_total'] / 100, 2, ',', '.') ?></td><td><?= painelEsc($pedido['atendente']) ?></td></tr>
          <?php endforeach; ?>
          <?php if ($pedidos === []): ?><tr><td colspan="6" class="text-body-secondary">Nenhum pedido cadastrado.</td></tr><?php endif; ?>
        </tbody></table>
      </div>
    </div>
  </section>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
