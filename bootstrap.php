<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

date_default_timezone_set('America/Sao_Paulo');

if (PHP_VERSION_ID < 80401) {
    throw new RuntimeException('O Kriko’s precisa de PHP 8.4.1 ou superior.');
}

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    throw new RuntimeException('Execute composer install para carregar o backend.');
}
require_once $autoload;

$config = ORMSetup::createXMLMetadataConfig(
    paths: [__DIR__ . '/config/xml'],
    isDevMode: true,
);

$config->setProxyDir(__DIR__ . '/proxies');
$config->setProxyNamespace('Kriko\Proxies');

// O site e os serviços do backend usam a mesma configuração de banco.
$param = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'krikos_db'
];

$config->enableNativeLazyObjects(true);

$connection = DriverManager::getConnection($param, $config);

$entityManager = new EntityManager($connection, $config);
