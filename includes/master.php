<?php

use Kriko\Controller\UserControl;

require_once __DIR__ . '/auth.php';
krikosExigirPerfil(['MASTER']);

$erro = '';
$categorias = $produtos = $mesas = [];
try {
    require dirname(__DIR__) . '/bootstrap.php';
    $conexao = $entityManager->getConnection();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrfValido($_POST['csrf'] ?? null)) {
            throw new DomainException('Atualize a página e tente novamente.');
        }
        $tipo = $_POST['tipo'] ?? '';
        if (!in_array($tipo, ['categoria', 'produto', 'mesa', 'administrador'], true)) {
            throw new DomainException('Operação inválida.');
        }
        $controle = new UserControl($entityManager);
        if ($tipo === 'categoria') {
            $controle->categoria([
                'nome' => $_POST['nome_categoria'] ?? '',
                'descricao' => $_POST['descricao_categoria'] ?? '',
            ]);
        } elseif ($tipo === 'mesa') {
            $controle->mesa([
                'numeroMesa' => $_POST['numero_mesa'] ?? '',
                'descricao' => $_POST['descricao_mesa'] ?? '',
                'ativo' => $_POST['ativo'] ?? 'S',
            ]);
        } elseif ($tipo === 'administrador') {
            $controle->administrador([
                'nome' => postTexto('nome'),
                'email' => postTexto('email'),
                'senha' => is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '',
                'perfil' => postTexto('perfil'),
                'ativo' => 'S',
            ]);
        } else {
            $controle->produto([
                'categoria_id' => $_POST['id_categoria'] ?? '',
                'nome' => $_POST['nome_produto'] ?? '',
                'descricao' => $_POST['descricao_produto'] ?? '',
                'preco' => precoCentavos(postTexto('preco')),
                'ativo' => $_POST['ativo'] ?? 'S',
            ]);
        }
        header('Location: master.php', true, 303);
        exit();
    }
    $categorias = $conexao->fetchAllAssociative('SELECT * FROM categorias ORDER BY nome');
    $produtos = $conexao->fetchAllAssociative(
        'SELECT p.*, c.nome AS categoria_nome FROM produtos p JOIN categorias c ON c.id = p.categoria ORDER BY p.nome',
    );
    $mesas = $conexao->fetchAllAssociative('SELECT * FROM mesas ORDER BY numero_mesa');
} catch (Throwable $falha) {
    error_log('Master Krikos: ' . $falha->getMessage());
    $erro =
        $falha instanceof DomainException
            ? $falha->getMessage()
            : 'Não foi possível concluir a operação. Confira os dados e a conexão com o banco.';
    http_response_code($falha instanceof DomainException ? 422 : 503);
}
