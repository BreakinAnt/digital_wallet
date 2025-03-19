<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    // Add your methods and logic here

    /**
     * Example method to find a user by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return User::find($id);
    }

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