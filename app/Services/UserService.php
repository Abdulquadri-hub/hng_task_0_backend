<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService implements UserServiceInterface
{
    public function __construct(protected FactProvider $factProvider){}

    public function createProfile(array $userData): ?User
    {
        try {
            $user =  DB::transaction(function () use ($userData) {
                return User::create($userData);
            });

            if(!$user) {
                throw new Exception("Error creating user profile");
            }

            return $user;

        } catch (\Throwable $th) {
           throw new Exception("Error creating user profile {$th->getMessage()}");
           Log::error("Error creating user profile {$th->getMessage()}");

        }
    }

    public function generateProfile(): array
    {
        try {
            $user = User::find(1);
            if(!$user) {
                throw new Exception("User profile not found");
            }

            $generatedFact = $this->factProvider->generateCatFacts();
            if(!isset($generatedFact['fact']) || $generatedFact['fact'] == "") {
                throw new Exception("Error generating facts: {$generatedFact}");
            }

            return [
                "user" => $user,
                'timestamp' => Carbon::now('UTC')->toIso8601String(),
                'fact' => $generatedFact['fact']
            ]; 
        } catch (\Throwable $th) {
           throw new Exception("Error generating user profile {$th->getMessage()}");
           Log::error("Error generating user profile {$th->getMessage()}");
        }
    }
}
