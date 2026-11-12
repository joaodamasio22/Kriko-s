
<?php

session_start();

require_once __DIR__ . "/../config/conexao.php";

// 1. Verifica se a requisição foi feita por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /krikos/");
    exit;
}

// 2. Recebe os dados do formulário
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

// 3. Verifica se os campos foram preenchidos
if ($email === "" || $senha === "") {
    header("Location: /krikos/?erro=1");
    exit;
}

// 4. Consulta o administrador pelo e-mail
$sql = "
    SELECT id, nome, email, senha, perfil
    FROM administradores
    WHERE email = :email
      AND ativo = 'S'
    LIMIT 1
";

$consulta = $conexao->prepare($sql);

$consulta->bindValue(":email", $email);

$consulta->execute();

// 5. Recupera o administrador encontrado
$administrador = $consulta->fetch();

// 6. Verifica se o administrador existe
// e se a senha corresponde ao hash armazenado
if (
    $administrador &&
    password_verify($senha, $administrador["senha"])
) {

    // 7. Gera um novo identificador de sessão
    session_regenerate_id(true);

    // 8. Armazena os dados necessários na sessão
    $_SESSION["admin_id"] = $administrador["id"];

    $_SESSION["admin_nome"] = $administrador["nome"];

    $_SESSION["admin_email"] = $administrador["email"];

    $_SESSION["admin_perfil"] = $administrador["perfil"];

    // 9. Redireciona para o painel
    header("Location: /krikos/painel.php");
    exit;

} else {

    // 10. Login inválido
    header("Location: /krikos/?erro=1");
    exit;

}
