<?php

namespace Matt\Php\Web\Login\Controller;

use Matt\Php\Web\Login\App\View;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Exception\ValidationException;
use Matt\Php\Web\Login\Model\UserRegisterRequest;
use Matt\Php\Web\Login\Repository\UserRepository;
use Matt\Php\Web\Login\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function register()
    {
        View::render('User/register', [
            "title" => "Register new User"
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect("/users/login");
        } catch (ValidationException $exception) {
            View::render('User/register', [
                "title" => "Register new User",
                "error" => $exception->getMessage()
            ]);
        }
    }
}