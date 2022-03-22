<?php

namespace Controllers;

use Exception;
use Services\UserService;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    private $service;

    // initialize services
    function __construct()
    {
        $this->service = new UserService();
    }

    function login() {
        $postedUser = $this->createObjectFromPostedJson('Models\User');
        $user = $this->service->checkUsernamePassword($postedUser->username, $postedUser->password);
        if (!$user) {
            $this->respondWithError(403, 'Invalid credentials');
            return;
        }

        $data = [
            "username" => $user->username,
            "email" => $user->email
        ];

        $issuer = "http://localhost";
        $audience = "http://localhost";
        $issuedAt = time();
        $notBefore = time();
        $expires = time() + 600;

        $payload = [
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $issuedAt,
            "nbf" => $notBefore,
            "exp" => $expires,
            "data" => $data
        ];

        $jwt = JWT::encode($payload, getenv("SECRET"), 'HS256');

        $response = [
            "message" => "Logged in successfully",
            "timestamp" => $issuedAt,
            "JWT" => $jwt
            ];

        $this->respond($response);
    }
}