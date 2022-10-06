<?php

namespace Matt\Php\Web\Login\Service;

use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\Session;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Repository\SessionRepository;
use Matt\Php\Web\Login\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value)
{
    echo "$name : $value";
}

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    
    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = "password";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("matt");
        
        $this->expectOutputRegex("[X-PZN-SESSION : $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals("matt", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "matt";

        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();
        $this->expectOutputRegex("[X-PZN-SESSION : ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "matt";

        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}