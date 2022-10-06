<?php

namespace Matt\Php\Web\Login\Controller;

use Matt\Php\Web\Login\App\View;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Repository\SessionRepository;
use Matt\Php\Web\Login\Repository\UserRepository;
use Matt\Php\Web\Login\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    
    function index()
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::render('Home/index', [
                "title" => "PHP login Management"
            ]);
        }else {
            View::render('Home/dashboard', [
                "title" => "Dashboard",
                "user"  => [
                    "name" => $user->name
                ]
            ]);
        }
    }
}