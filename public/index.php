<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;

$page = new HomeController();
$page->index();