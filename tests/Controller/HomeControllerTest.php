<?php

namespace Matt\Php\Web\Login\Controller;

use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\Session;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Repository\SessionRepository;
use Matt\Php\Web\Login\Repository\UserRepository;
use Matt\Php\Web\Login\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[PHP login Management]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "rachmat";
        $user->password = "password";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Hello rachmat]");
    }
}