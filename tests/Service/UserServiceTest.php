<?php

namespace Matt\Php\Web\Login\Service;

use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Exception\ValidationException;
use Matt\Php\Web\Login\Model\UserRegisterRequest;
use Matt\Php\Web\Login\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

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
}