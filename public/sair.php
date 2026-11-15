<?php

require_once dirname(__DIR__) . '/includes/helpers.php';

require_once dirname(__DIR__) . '/includes/session.php';
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit();
}
if (!csrfValido($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Atualize a página e tente novamente.');
}
$_SESSION = [];
session_regenerate_id(true);
header('Location: painel.php', true, 303);
exit();
