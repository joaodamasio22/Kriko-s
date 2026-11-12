
<?php

session_start();

// Remove os dados da sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona para o login
header("Location: login.php");
exit;
