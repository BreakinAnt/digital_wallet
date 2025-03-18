<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

    public function loginUser(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        if (!$this->validatePassword($user, $password)) {
            throw new \Exception('Invalid password.');
        }

        Auth::login($user);

        return $user;
    }

    public function validatePassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    public function verifyEmail(User $user): User
    {
        if($user->email_verified_at) {
            return $user;
        }
        
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}
