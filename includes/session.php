<?php

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
ini_set('session.use_strict_mode', '1');
session_name('krikos_session');
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
