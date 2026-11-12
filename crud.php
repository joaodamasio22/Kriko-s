
<?php

// ==========================================
// CONEXAO COM O BANCO
// ==========================================

$host = "localhost";
$banco = "krikos_db";
$usuario = "root";
$senhaBanco = "12345678";

try {

    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senhaBanco
    );

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "\nConectado ao banco de dados!\n";

} catch (PDOException $erro) {

    echo "\nErro ao conectar ao banco de dados.\n";
    exit;

}


// ==========================================
// FUNCAO PARA LER DADOS DO TERMINAL
// ==========================================

function ler($mensagem) {

    echo $mensagem;

    return trim(fgets(STDIN));
}


// ==========================================
// CADASTRAR ADMINISTRADOR - CREATE
// ==========================================

function cadastrar($conexao) {

    echo "\n===== CADASTRAR ADMINISTRADOR =====\n";

    $nome = ler("Nome: ");
    $email = ler("E-mail: ");
    $senha = ler("Senha: ");

    echo "\n1 - MASTER\n";
    echo "2 - ADMIN\n";

    $opcao = ler("Escolha o perfil: ");

    if ($nome === "" || $email === "" || $senha === "") {
        echo "\nPreencha todos os campos!\n";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "\nE-mail inválido!\n";
        return;
    }

    if (strlen($senha) < 8) {
        echo "\nA senha precisa ter pelo menos 8 caracteres!\n";
        return;
    }

    if ($opcao === "1") {
        $perfil = "MASTER";
    } elseif ($opcao === "2") {
        $perfil = "ADMIN";
    } else {
        echo "\nPerfil inválido!\n";
        return;
    }

    // Transforma a senha em hash
    $hash = password_hash($senha, PASSWORD_DEFAULT);

    try {

        $sql = "
            INSERT INTO administradores
            (nome, email, senha, perfil)
            VALUES
            (:nome, :email, :senha, :perfil)
        ";

        $consulta = $conexao->prepare($sql);

        $consulta->execute([
            ":nome" => $nome,
            ":email" => $email,
            ":senha" => $hash,
            ":perfil" => $perfil
        ]);

        echo "\nAdministrador cadastrado com sucesso!\n";

    } catch (PDOException $erro) {

        if ($erro->getCode() === "23000") {
            echo "\nEste e-mail já está cadastrado!\n";
        } else {
            echo "\nErro ao cadastrar administrador.\n";
        }

    }

}


// ==========================================
// LISTAR ADMINISTRADORES - READ
// ==========================================

function listar($conexao) {

    echo "\n===== LISTA DE ADMINISTRADORES =====\n";

    $sql = "
        SELECT id, nome, email, perfil, ativo, data_cadastro
        FROM administradores
        ORDER BY id
    ";

    $consulta = $conexao->query($sql);

    $administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if (count($administradores) === 0) {
        echo "\nNenhum administrador cadastrado.\n";
        return;
    }

    foreach ($administradores as $admin) {

        echo "\n-----------------------------\n";

        echo "ID: " . $admin["id"] . "\n";
        echo "Nome: " . $admin["nome"] . "\n";
        echo "E-mail: " . $admin["email"] . "\n";
        echo "Perfil: " . $admin["perfil"] . "\n";

        if ($admin["ativo"] === "S") {
            echo "Status: ATIVO\n";
        } else {
            echo "Status: INATIVO\n";
        }

        echo "Cadastro: " . $admin["data_cadastro"] . "\n";

    }

    echo "\n-----------------------------\n";

}


// ==========================================
// EDITAR ADMINISTRADOR - UPDATE
// ==========================================

function editar($conexao) {

    echo "\n===== EDITAR ADMINISTRADOR =====\n";

    $id = ler("Digite o ID do administrador: ");

    if (!ctype_digit($id) || (int)$id <= 0) {
        echo "\nID inválido!\n";
        return;
    }

    $sql = "
        SELECT *
        FROM administradores
        WHERE id = :id
    ";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":id" => $id
    ]);

    $admin = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "\nAdministrador não encontrado!\n";
        return;
    }

    echo "\nDeixe o campo vazio para manter o valor atual.\n";

    $nome = ler("Novo nome (" . $admin["nome"] . "): ");
    $email = ler("Novo e-mail (" . $admin["email"] . "): ");
    $senha = ler("Nova senha: ");

    echo "\n1 - MASTER\n";
    echo "2 - ADMIN\n";
    echo "ENTER - Manter perfil atual (" . $admin["perfil"] . ")\n";

    $opcao = ler("Novo perfil: ");

    if ($nome === "") {
        $nome = $admin["nome"];
    }

    if ($email === "") {
        $email = $admin["email"];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "\nE-mail inválido!\n";
        return;
    }

    if ($senha !== "" && strlen($senha) < 8) {
        echo "\nA senha precisa ter pelo menos 8 caracteres!\n";
        return;
    }

    if ($opcao === "1") {
        $perfil = "MASTER";
    } elseif ($opcao === "2") {
        $perfil = "ADMIN";
    } elseif ($opcao === "") {
        $perfil = $admin["perfil"];
    } else {
        echo "\nPerfil inválido!\n";
        return;
    }

    try {

        if ($senha !== "") {

            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "
                UPDATE administradores
                SET nome = :nome,
                    email = :email,
                    senha = :senha,
                    perfil = :perfil
                WHERE id = :id
            ";

            $parametros = [
                ":nome" => $nome,
                ":email" => $email,
                ":senha" => $hash,
                ":perfil" => $perfil,
                ":id" => $id
            ];

        } else {

            $sql = "
                UPDATE administradores
                SET nome = :nome,
                    email = :email,
                    perfil = :perfil
                WHERE id = :id
            ";

            $parametros = [
                ":nome" => $nome,
                ":email" => $email,
                ":perfil" => $perfil,
                ":id" => $id
            ];

        }

        $consulta = $conexao->prepare($sql);
        $consulta->execute($parametros);

        echo "\nAdministrador atualizado com sucesso!\n";

    } catch (PDOException $erro) {

        if ($erro->getCode() === "23000") {
            echo "\nEste e-mail já está cadastrado!\n";
        } else {
            echo "\nErro ao atualizar administrador.\n";
        }

    }

}


// ==========================================
// DESATIVAR ADMINISTRADOR - DELETE LOGICO
// ==========================================

function desativar($conexao) {

    echo "\n===== DESATIVAR ADMINISTRADOR =====\n";

    $id = ler("Digite o ID do administrador: ");

    if (!ctype_digit($id) || (int)$id <= 0) {
        echo "\nID inválido!\n";
        return;
    }

    $sql = "
        SELECT id, nome, ativo
        FROM administradores
        WHERE id = :id
    ";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":id" => $id
    ]);

    $admin = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "\nAdministrador não encontrado!\n";
        return;
    }

    if ($admin["ativo"] === "N") {
        echo "\nEste administrador já está desativado!\n";
        return;
    }

    echo "\nAdministrador: " . $admin["nome"] . "\n";

    $confirmacao = ler("Deseja realmente desativar? (S/N): ");

    if (strtoupper($confirmacao) !== "S") {
        echo "\nOperação cancelada.\n";
        return;
    }

    $sql = "
        UPDATE administradores
        SET ativo = 'N'
        WHERE id = :id
    ";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":id" => $id
    ]);

    echo "\nAdministrador desativado com sucesso!\n";

}


// ==========================================
// REATIVAR ADMINISTRADOR
// ==========================================

function reativar($conexao) {

    echo "\n===== REATIVAR ADMINISTRADOR =====\n";

    $id = ler("Digite o ID do administrador: ");

    if (!ctype_digit($id) || (int)$id <= 0) {
        echo "\nID inválido!\n";
        return;
    }

    $sql = "
        UPDATE administradores
        SET ativo = 'S'
        WHERE id = :id
          AND ativo = 'N'
    ";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        ":id" => $id
    ]);

    if ($consulta->rowCount() > 0) {
        echo "\nAdministrador reativado com sucesso!\n";
    } else {
        echo "\nAdministrador não encontrado ou já está ativo!\n";
    }

}


// ==========================================
// MENU PRINCIPAL
// ==========================================

do {

    echo "\n";
    echo "===============================\n";
    echo "       CRUD - KRIKOS\n";
    echo "===============================\n";

    echo "1 - Cadastrar administrador\n";
    echo "2 - Listar administradores\n";
    echo "3 - Editar administrador\n";
    echo "4 - Desativar administrador\n";
    echo "5 - Reativar administrador\n";
    echo "0 - Sair\n";

    echo "===============================\n";

    $opcao = ler("Escolha uma opção: ");

    switch ($opcao) {

        case "1":
            cadastrar($conexao);
            break;

        case "2":
            listar($conexao);
            break;

        case "3":
            editar($conexao);
            break;

        case "4":
            desativar($conexao);
            break;

        case "5":
            reativar($conexao);
            break;

        case "0":
            echo "\nEncerrando o CRUD...\n";
            break;

        default:
            echo "\nOpção inválida!\n";
            break;

    }

} while ($opcao !== "0");

?>
