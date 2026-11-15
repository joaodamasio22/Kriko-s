<?php

require_once __DIR__ . '/helpers.php';

require_once __DIR__ . '/session.php';

function krikosDestino(string $perfil): string
{
    return match ($perfil) {
        'MASTER' => 'master.php',
        'GARCOM' => 'index.php',
        'COZINHA' => 'cozinha.php',
        default => 'painel.php',
    };
}

function krikosExigirPerfil(array $perfis): void
{
    $perfil = $_SESSION['krikos_usuario']['perfil'] ?? '';
    if (!in_array($perfil, $perfis, true)) {
        header('Location: ' . krikosDestino($perfil), true, 302);
        exit();
    }
    header('Cache-Control: no-store');
}
