<?php

namespace App\Contracts\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function createProfile(array $userData): ?User;
    public function generateProfile(): array;
}
