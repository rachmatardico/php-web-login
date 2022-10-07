<?php
namespace Matt\Php\Web\Login\Middleware
{
    require_once __DIR__ . '/../Helper/helper.php';
    
    use Matt\Php\Web\Login\Config\Database;
    use Matt\Php\Web\Login\Domain\Session;
    use Matt\Php\Web\Login\Domain\User;
    use Matt\Php\Web\Login\Repository\SessionRepository;
    use Matt\Php\Web\Login\Repository\UserRepository;
    use Matt\Php\Web\Login\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
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

            $this->middleware->before();
            $this->expectOutputString("");
        }
    }
}
