<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser(string $email): User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function registerUser(string $name, string $email, string $password): User
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception('Email already exists.');
        }

        $data = [
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make($password)
        ];

        return $this->userRepository->create($data);
    }

    public function validateUser(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            throw new \Exception('User not found.');
        }

        if (!$this->validatePassword($user, $password)) {
            throw new \Exception('Invalid password.');
        }

        if (!$user->email_verified_at) {
            throw new UserException('Email not verified.');
        }

        return $user;
    }

    public function validatePassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    public function markEmailAsVerified(User $user): User
    {
        if($user->email_verified_at) {
            return $user;
        }
        
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}
