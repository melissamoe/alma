<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Core\Router;
use App\Controllers\NewsletterController;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

use App\Core\Database;

Database::connect();
echo "Connexion base de données OK";
exit;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
$router->get('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);

$router->dispatch();