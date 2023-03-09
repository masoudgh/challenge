<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsers();

    public function getUserById($userID);

    public function deleteUser($userID);

    public function createUser(array $userDetails);

    public function updateUSer($userID, array $newDetails);
}
