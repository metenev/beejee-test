<?php

// Load configuration

$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['ENV_DIR']);
$dotenv->load();

// Define router rules

$router = new \BeeJeeTest\Core\Router();

$router->rule('index', [
    'controller' => 'Index',
    'action' => 'index',
]);

$router->rule('auth', [
    'controller' => 'Auth',
    'action' => 'index',
]);

$router->process();
