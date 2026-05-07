<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "role" => $validated["role"],
        ]);

        $token = $user->createToken("user_token")->accessToken;

        $response = [
            "user" => new UserResource($user),
            "token" => $token
        ];


        return ApiResponseService::Response(201, "user created successfully", $response);
    }

    public function login(LoginRequest $request)
    {

        $validated = $request->validated();

        $user = user::where("email", $validated["email"])->first();

        if (!$user || !Hash::check($validated["password"], $user->password)) {


            return ApiResponseService::Response(404, "invalid credentials", []);
        }

        $token = $user->createToken("auth_token")->accessToken;

        $response = [
            "user" => new UserResource($user),
            "token" => $token
        ];


        return ApiResponseService::Response(200, "user logedin successfully", $response);
    }

    public function user(Request $request)
    {

        $response = [
            "user" => new UserResource($request->user())
        ];

        return ApiResponseService::Response(200, "get user", $response);
    }


    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();
        return ApiResponseService::Response(200, "user loged out", []);
    }
}
