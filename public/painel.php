<?php

use Kriko\Services\Autenticacao;

require_once dirname(__DIR__) . '/includes/auth.php';
header('Cache-Control: no-store');

$perfil = krikosPerfilAutenticado();
if ($perfil !== null) {
    header('Location: ' . krikosDestino($perfil), true, 302);
    exit();
}
unset($_SESSION['krikos_usuario']);

$erro = '';
$mensagem = $_SESSION['painel_mensagem'] ?? '';
unset($_SESSION['painel_mensagem']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!csrfValido($_POST['csrf'] ?? null)) {
            throw new DomainException('A página expirou. Atualize e tente de novo.');
        }
        if (postTexto('acao') !== 'entrar') {
            throw new DomainException('Operação inválida.');
        }
        require dirname(__DIR__) . '/bootstrap.php';
        $usuario = new Autenticacao($entityManager)->entrar(
            postTexto('email'),
            is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '',
        );
        $destino = krikosDestino($usuario['perfil']);
        if ($destino === null) {
            throw new DomainException('Seu acesso está sem um perfil válido.');
        }
        session_regenerate_id(true);
        $_SESSION['krikos_usuario'] = $usuario;
        $_SESSION['krikos_csrf'] = bin2hex(random_bytes(32));
        header('Location: ' . $destino, true, 303);
        exit();
    } catch (Throwable $falha) {
        error_log('Login Krikos: ' . $falha->getMessage());
        $erro =
            $falha instanceof DomainException
                ? $falha->getMessage()
                : 'Não foi possível acessar o sistema. Tente novamente.';
        http_response_code($falha instanceof DomainException ? 422 : 503);
    }
}

$page = 'login';
$title = 'Login | Kriko’s';
require dirname(__DIR__) . '/includes/header.php';
?>
<main class="login-shell">
    <div class="login-container">
        <header class="login-brand">
            <img class="login-logo" src="imagens/logo.png" alt="Kriko’s">
            <p class="login-tagline">Sabor que faz você voltar!</p>
        </header>

        <section class="login-box" aria-labelledby="entrarTitulo">
            <div class="login-user-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" role="img">
                    <path
                        d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-5 0-9 2.5-9 5.5V22h18v-2.5C21 16.5 17 14 12 14Z" />
                </svg>
            </div>
            <h1 id="entrarTitulo">Faça seu login</h1>
            <p class="login-subtitle">Entre com seu e-mail e senha para continuar</p>

            <?php if ($mensagem !== ''): ?>
                <div class="login-alert login-alert-success" role="status"><?= e($mensagem) ?></div>
            <?php endif; ?>

            <?php if ($erro !== ''): ?>
                <div class="login-alert login-alert-error" role="alert"><?= e($erro) ?></div>
            <?php endif; ?>

            <form method="post" action="painel.php">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['krikos_csrf']) ?>">
                <input type="hidden" name="acao" value="entrar">

                <label class="login-field" for="email">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M20 4H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h16a2 2 0 0 0 2-2V6c0-1.1-.9-2-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z" />
                    </svg>
                    <span class="visually-hidden">E-mail</span>
                    <input id="email" name="email" type="email" placeholder="E-mail" autocomplete="username" required
                        autofocus>
                </label>

                <label class="login-field" for="senha">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M18 8h-1V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2Zm-6 9a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm3.1-9H8.9V6a3.1 3.1 0 0 1 6.2 0v2Z" />
                    </svg>
                    <span class="visually-hidden">Senha</span>
                    <input id="senha" name="senha" type="password" placeholder="Senha" autocomplete="current-password"
                        required>
                </label>

                <button class="login-submit" type="submit">Entrar</button>
            </form>

            <a class="login-forgot" href="esqueci_senha.php">Esqueci minha senha</a>
        </section>

        <footer class="login-footer">© <?= date('Y') ?> KRIKO’S — Todos os direitos reservados.</footer>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
