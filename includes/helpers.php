<?php

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function moeda(int $centavos): string
{
    return 'R$ ' . number_format($centavos / 100, 2, ',', '.');
}

function postTexto(string $campo): string
{
    return is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
}

function csrfValido(mixed $token): bool
{
    return is_string($token) &&
        isset($_SESSION['krikos_csrf']) &&
        hash_equals($_SESSION['krikos_csrf'], $token);
}

function precoCentavos(string $valor): int
{
    $valor = str_replace(',', '.', $valor);
    if (!preg_match('/\A[0-9]{1,7}(?:\.[0-9]{1,2})?\z/', $valor)) {
        throw new DomainException('Informe o preço em reais, por exemplo 12,50.');
    }
    [$reais, $centavos] = array_pad(explode('.', $valor, 2), 2, '0');
    return (int) $reais * 100 + (int) str_pad($centavos, 2, '0');
}
