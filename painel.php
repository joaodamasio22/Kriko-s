
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: autenticacao/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Krikos</title>
</head>

<body>

    <h1>
        Bem-vindo,
        <?= htmlspecialchars($_SESSION["admin_nome"]) ?>!
    </h1>

    <p>
        Seu perfil é:
        <?= htmlspecialchars($_SESSION["admin_perfil"]) ?>
    </p>

    <a href="autenticacao/logout.php">
        Sair
    </a>

</body>

</html>
