<?php

use Kriko\Entity\Administrador;

require_once __DIR__ . '/session.php';

function authUsuario(): ?array
{
    $usuario = $_SESSION['krikos_usuario'] ?? null;

    return is_array($usuario) ? $usuario : null;
}

function authEntrar(Administrador $administrador): void
{
    session_regenerate_id(true);
    $_SESSION['krikos_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['krikos_usuario'] = [
        'id' => (int) $administrador->getId(),
        'nome' => $administrador->getNome(),
        'perfil' => $administrador->getPerfil(),
    ];
}

function authSair(): void
{
    unset($_SESSION['krikos_usuario']);
    session_regenerate_id(true);
    $_SESSION['krikos_csrf'] = bin2hex(random_bytes(32));
}

function authExigirPerfil(array $perfis): void
{
    $perfil = authUsuario()['perfil'] ?? '';
    if (!in_array($perfil, $perfis, true)) {
        throw new RuntimeException('Seu perfil não pode executar essa operação.');
    }
}

function authExigirLogin(): array
{
    $usuario = authUsuario();
    if ($usuario === null) {
        header('Location: login.php', true, 303);
        exit;
    }

    return $usuario;
}
