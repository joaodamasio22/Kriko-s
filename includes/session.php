<?php

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
  'httponly' => true,
  'secure' => $https,
  'samesite' => 'Lax',
  'path' => '/',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$_SESSION['krikos_csrf'] ??= bin2hex(random_bytes(32));
