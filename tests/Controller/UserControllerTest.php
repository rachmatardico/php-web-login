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
        
        public function testLogout()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION : ]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[matt]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[rachmat]");

        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "Budi";
            $this->userController->postUpdateProfile();
            
            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("matt");
            self::assertEquals("Budi", $result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "";
            $this->userController->postUpdateProfile();
            
            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[matt]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id, Name can't be blank!]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[matt]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "password";
            $_POST['newPassword'] = "budi";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("budi", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "";
            $_POST['newPassword'] = "";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[matt]");
            $this->expectOutputRegex("[Id, Old Password, New Password can't be blank!]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = "matt";
            $user->name = "rachmat";
            $user->password = password_hash("password", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "salah";
            $_POST['newPassword'] = "baru";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[matt]");
            $this->expectOutputRegex("[Old password is wrong!]");
        }
    }
}
