<?php

$whatsApp = preg_replace('/\D+/', '', (string) getenv('KRIKOS_WHATSAPP'));
$supportEmail = filter_var((string) getenv('KRIKOS_SUPPORT_EMAIL'), FILTER_VALIDATE_EMAIL) ?: '';
$supportUrl =
    $whatsApp !== ''
        ? 'https://wa.me/' . $whatsApp
        : ($supportEmail !== ''
            ? 'mailto:' . $supportEmail
            : '');

$page = 'login';
$title = 'Recuperar acesso | Kriko’s';
require dirname(__DIR__) . '/includes/header.php';
?>
<main class="login-shell">
    <div class="login-container">
        <header class="login-brand">
            <img class="login-logo" src="imagens/logo.png" alt="Kriko’s">
            <p class="login-tagline">Sabor que faz você voltar!</p>
        </header>

        <section class="login-box login-support" aria-labelledby="recuperarTitulo">
            <div class="login-user-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" role="img">
                    <path
                        d="M18 8h-1V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2Zm-6 9a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm3.1-9H8.9V6a3.1 3.1 0 0 1 6.2 0v2Z" />
                </svg>
            </div>
            <h1 id="recuperarTitulo">Esqueceu sua senha?</h1>
            <p class="login-subtitle">Para recuperar o acesso à sua conta, entre em contato com o suporte.</p>

            <?php if ($supportUrl !== ''): ?>

                <a class="login-submit login-support-button"
                    href="<?= htmlspecialchars($supportUrl, ENT_QUOTES, 'UTF-8') ?>" rel="noopener noreferrer">Entre em
                    contato com o suporte</a>

            <?php else: ?>

                <p class="login-support-guidance">Solicite a redefinição da senha ao administrador responsável.</p>

            <?php endif; ?>

            <a class="login-forgot" href="painel.php">Voltar para o login</a>
        </section>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
