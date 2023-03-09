<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getUserByID($userID)
    {
        return User::findOrFail($userID);
    }

    public function deleteUser($userID): int
    {
        User::destroy($userID);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userID, array $newDetails): int
    {
        return User::query()->where('id', '=', $userID)->update($newDetails);
    }

}
