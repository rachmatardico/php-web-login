<?php

namespace Matt\Php\Web\Login\Service;

use Exception;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Exception\ValidationException;
use Matt\Php\Web\Login\Model\{UserRegisterRequest, UserRegisterResponse};
use Matt\Php\Web\Login\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request):UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user != null) {
                throw new ValidationException("User Id already exists!");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;
            Database::commitTransaction();
            return $response;
        }catch(Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || 
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password == "")) {
                throw new ValidationException("Id, Name, Password can't be blank!");
        }
    }
}