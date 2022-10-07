<?php

namespace Matt\Php\Web\Login\Service;

use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Exception\ValidationException;
use Matt\Php\Web\Login\Model\UserLoginRequest;
use Matt\Php\Web\Login\Model\UserPasswordUpdateRequest;
use Matt\Php\Web\Login\Model\UserProfileUpdateRequest;
use Matt\Php\Web\Login\Model\UserRegisterRequest;
use Matt\Php\Web\Login\Repository\SessionRepository;
use Matt\Php\Web\Login\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sesionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->sesionRepository = new SessionRepository($connection);
        $this->userService = new UserService($this->userRepository);
        
        $this->sesionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "matt";
        $request->name = "Rachmat";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $response = $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "matt";
        $request->name = "Rachmat";
        $request->password = "rahasia";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "matt";
        $request->password = "matt";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = password_hash("password", PASSWORD_BCRYPT);


        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "matt";
        $request->password = "salah";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = password_hash("password", PASSWORD_BCRYPT);


        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "matt";
        $request->password = "password";

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "matt";
        $request->name = "Budi";

        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "matt";
        $request->name = "Budi";

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "matt";
        $request->oldPassword = "password";
        $request->newPassword = "new";

        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserPasswordUpdateRequest();
        $request->id = "matt";
        $request->oldPassword = "";
        $request->newPassword = "";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "matt";
        $request->oldPassword = "wrong";
        $request->newPassword = "new";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "notfound";
        $request->oldPassword = "password";
        $request->newPassword = "new";

        $this->userService->updatePassword($request);
    }
}