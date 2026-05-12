<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Core\Router;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

use App\Core\Database;

Database::connect();
echo "Connexion base de données OK";
exit;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->dispatch();