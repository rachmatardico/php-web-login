<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Matt\Php\Web\Login\App\Router;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Controller\HomeController;
use Matt\Php\Web\Login\Controller\UserController;

Database::getConnection("prod");

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);
Router::add('GET', '/users/logout', UserController::class, 'logout', []);

Router::run();