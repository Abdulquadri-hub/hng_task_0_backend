<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService){}


    public function save(Request $request) {
        try {
            $validatedData = $request->validate([
                "name" => "required|string",
                "email" => "required|string|email",
                "stack"  => "required|string"
            ]);

            $user = $this->userService->createProfile($validatedData);
            if(!$user) {
                return $this->errorResponse(
                    "Error creating user profile",
                    442,
                );
            }

            return $this->successResponse(
                $user, 
                "User profile created successfully"
            );

        } catch (\Throwable $th) {
           return $this->errorResponse(
                "Error creating user profile: {$th->getMessage()}",
                500,
           );
        }
    }

    public function profile() {
         try {
            $profile = $this->userService->generateProfile();
            if(!$profile) {
                return $this->errorResponse(
                    "Error generating user profile",
                    442,
                );
            }

            return $profile;
        } catch (\Throwable $th) {
           return $this->errorResponse(
                "Error generating user profile: {$th->getMessage()}",
                500,
           );
        }
    }
}
