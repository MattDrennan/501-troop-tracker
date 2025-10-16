<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Utilities\DatabaseConnection;
use App\Payloads\LoginPayload;
use App\Domain\Results\LoginResult;

class AuthenticationService
{
    public function __construct(
        private readonly DatabaseConnection $db
    ) {
    }

    public function login(LoginPayload $payload): LoginResult
    {
        // $curl = curl_init();

        // curl_setopt_array($curl, [
        //   CURLOPT_URL => $forumURL . "api/auth",
        //   CURLOPT_POST => 1,
        //   CURLOPT_POSTFIELDS => "login=" . urlencode($username) . "&password=" . urlencode($password) . "",
        //   CURLOPT_CUSTOMREQUEST => "POST",
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => "",
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_HTTPHEADER => [
        //     "XF-Api-Key: " . xenforoAPI_superuser,
        //     "XF-Api-User: " . xenforoAPI_userID,
        //   ],
        // ]);

        // $response = curl_exec($curl);

        // curl_close($curl);

        // return json_decode($response, true);

        return new LoginResult(false, 'Invalid Credentials.');
    }
}