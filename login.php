
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | KRIKO'S</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="container">

        <header class="logo">
            <div class="mascote">🥟</div>

            <h1>KRIKO'S</h1>
            <h2>PASTÉIS E CHURROS</h2>

            <p>Sabor que faz você voltar!</p>
        </header>

        <section class="login-box">

            <div class="usuario">👤</div>

            <h3>Faça seu login</h3>

            <p class="subtitulo">
                Entre com seu e-mail e senha para continuar
            </p>

            <form action="#" method="POST">

                <div class="campo">
                    <span>✉</span>
                    <input
                        type="email"
                        name="email"
                        placeholder="E-mail"
                        required
                    >
                </div>

                <div class="campo">
                    <span>🔒</span>
                    <input
                        type="password"
                        name="senha"
                        placeholder="Senha"
                        required
                    >
                </div>

                <button type="submit">
                    Entrar
                </button>

            </form>

            <a href="esqueci_senha.php" class="esqueceu">
                Esqueci minha senha
            </a>

        </section>

        <footer>
            © 2026 KRIKO'S - Todos os direitos reservados.
        </footer>

    </main>

</body>
</html>
