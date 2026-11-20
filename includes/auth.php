<?php

require_once __DIR__ . '/helpers.php';

require_once __DIR__ . '/session.php';

function krikosDestino(string $perfil): ?string
{
    return match ($perfil) {
        'MASTER' => 'master.php',
        'GARCOM' => 'index.php',
        'COZINHA' => 'cozinha.php',
        default => null,
    };
}

function krikosPerfilAutenticado(): ?string
{
    $usuario = $_SESSION['krikos_usuario'] ?? null;
    if (!is_array($usuario) || !is_int($usuario['id'] ?? null) || $usuario['id'] < 1) {
        return null;
    }

    $perfil = $usuario['perfil'] ?? null;
    return is_string($perfil) && krikosDestino($perfil) !== null ? $perfil : null;
}

function krikosExigirLogin(): string
{
    header('Cache-Control: no-store');
    $perfil = krikosPerfilAutenticado();
    if ($perfil === null) {
        header('Location: painel.php', true, 302);
        exit();
    }
    return $perfil;
}

function krikosExigirPerfil(array $perfis): void
{
    $perfil = krikosExigirLogin();
    if (!in_array($perfil, $perfis, true)) {
        header('Location: ' . krikosDestino($perfil), true, 302);
        exit();
    }
}
