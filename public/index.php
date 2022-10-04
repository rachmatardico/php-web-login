<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Matt\Php\Web\Login\App\Router;
use Matt\Php\Web\Login\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();