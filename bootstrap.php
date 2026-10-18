<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once "vendor/autoload.php";

$config = ORMSetup::createXMLMetadataConfig(
    paths: [__DIR__ . '/config/xml'],
    isDevMode: true,
);

$config->setProxyDir(__DIR__ . '/proxies');
$config->setProxyNamespace('Kriko\Proxies');

$param = ['driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'krikos_db',];

$connection = DriverManager::getConnection($param, $config);

$entityManager = new EntityManager($connection, $config);
