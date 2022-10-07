<?php

namespace Matt\Php\Web\Login\Repository;

use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{

    private UserRepository $userRepository;
    private SessionRepository $sesionRepository;

    protected function setUp():void
    {
        $this->sesionRepository = new SessionRepository(Database::getConnection());
        $this->sesionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "Rachmat";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("notfound");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "matt";
        $user->name = "rachmat";
        $user->password = "rahasia";
        $this->userRepository->save($user);
        
        $user->name = "Budi";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
}