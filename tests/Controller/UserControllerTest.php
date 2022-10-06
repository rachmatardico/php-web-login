<?php

namespace Matt\Php\Web\Login\App
{
    function header(string $value)
    {
        echo $value;
    }
}

namespace Matt\Php\Web\Login\Service
{
    function setcookie(string $name, string $value)
    {
        echo "$name : $value";
    }
}

namespace Matt\Php\Web\Login\Controller
{
    use Matt\Php\Web\Login\Config\Database;
    use Matt\Php\Web\Login\Domain\Session;
    use Matt\Php\Web\Login\Domain\User;
    use Matt\Php\Web\Login\Repository\SessionRepository;
    use Matt\Php\Web\Login\Repository\UserRepository;
    use Matt\Php\Web\Login\Service\SessionService;
    use PHPUnit\Framework\TestCase;
    
    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
    
        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
            
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }
    
        public function testRegister()
        {
            $this->userController->register();
    
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }
    
        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'matt';
            $_POST['name'] = "rachmat";
            $_POST['password'] = "password";
    
            $this->userController->postRegister();
            
            $this->expectOutputRegex("[Location: /users/login]");
        }
    
        public function testPostRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = "";
            $_POST['password'] = "password";
    
            $this->userController->postRegister();
    
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can't be blank!]");
    
        }
    
        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = "password";
    
            $this->userRepository->save($user);
    
            $_POST['id'] = 'matt';
            $_POST['name'] = "rachmat";
            $_POST['password'] = "password";
    
            $this->userController->postRegister();
    
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists!]");
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $_POST['id'] = "matt";
            $_POST['password'] = "password";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION : ]");
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = "";
            $_POST['password'] = "";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can't be blank!]");
        }

        public function testLoginUserNotFound()
        {
            $_POST['id'] = "awokawokoa";
            $_POST['password'] = "password";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or Password is wrong]");
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $_POST['id'] = "matt";
            $_POST['password'] = "salah";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or Password is wrong]");
        }
    }
}
