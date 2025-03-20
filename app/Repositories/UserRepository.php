<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Find a user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): User
    {
        return User::create($data);
    }
}