<?php

namespace Matt\Php\Web\Login\Service;

use Exception;
use Matt\Php\Web\Login\Config\Database;
use Matt\Php\Web\Login\Domain\User;
use Matt\Php\Web\Login\Exception\ValidationException;
use Matt\Php\Web\Login\Model\{UserLoginRequest, UserLoginResponse, UserProfileUpdateRequest, UserProfileUpdateResponse, UserRegisterRequest, UserRegisterResponse};
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

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or Password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        }else {
            throw new ValidationException("Id or Password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || 
            trim($request->id) == "" || trim($request->password == "")) {
                throw new ValidationException("Id, Password can't be blank!");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request):UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found!");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);
            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null || 
            trim($request->id) == "" || trim($request->name == "")) {
                throw new ValidationException("Id, Name can't be blank!");
        }
    }
}