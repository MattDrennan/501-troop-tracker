<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Utilities\DatabaseConnection;
use App\Requests\LoginRequest;
use App\Domain\Responses\LoginResponse;

class AuthenticationService
{
    public function __construct(
        private readonly DatabaseConnection $db
    ) {
    }

    public function login(LoginRequest $request): LoginResponse
    {
        // global $forumURL;

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
//--------------------
        // $sql = "SELECT * FROM troopers WHERE forum_id = ? LIMIT 1";
        // $db_user = $this->db->fetchOne($sql, 's', [$username]);

        // if ($db_user) {
        //     // In a real scenario, you'd use password_verify() and the forum check.
        //     // For this example, we'll assume success if a user is found.
        //     // A real implementation would be:
        //     // $forumLogin = loginWithForum($username, $password);
        //     // if (($forumLogin['success'] ?? 0) === 1 || password_verify($password, $db->password)) {

        //     if ($db_user->approved != 0) {
        //         // Set session
        //         $_SESSION['id'] = $db_user->id;
        //         $_SESSION['tkid'] = $db_user->tkid;

        //         // Set log in cookie, if set to keep logged in
        //         if ($keep_logged_in) {
        //             setcookie("TroopTrackerUsername", $db_user->forum_id, time() + (10 * 365 * 24 * 60 * 60), "/");
        //             setcookie("TroopTrackerPassword", $password, time() + (10 * 365 * 24 * 60 * 60), "/");
        //         }

        //         $redirect_url = 'index.php';
        //         if (isset($_COOKIE["TroopTrackerLastEvent"])) {
        //             $redirect_url = 'index.php?event=' . cleanInput($_COOKIE["TroopTrackerLastEvent"]);
        //             setcookie("TroopTrackerLastEvent", "", time() - 3600, "/");
        //         }
        //         $this->redirectResponder->redirect($redirect_url);
        //     } else {
        //         // Account not approved
        //         $this->redirectResponder->redirectWithMessage('adr.php?action=login', 'Your access has not been approved yet.');
        //     }
        // } else {
        //     // User not found or password incorrect
        //     $this->redirectResponder->redirectWithMessage('adr.php?action=login', 'Incorrect username or password.');
        // }

        return new LoginResponse(false, 'Incorrect username or password.');
    }
}