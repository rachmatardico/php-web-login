<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Matt\Php\Web\Login\App\Router;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Controller\HomeController;
use Matt\Php\Web\Login\Controller\UserController;
use Matt\Php\Web\Login\Middleware\MustLoginMiddleware;
use Matt\Php\Web\Login\Middleware\MustNotLoginMiddleware;

Database::getConnection("prod");

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);

Router::run();