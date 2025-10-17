<?php

/**
 * Process post insert webhooks for the Troop Tracker mobile app.
 *
 * @author Matthew Drennan
 */

// Include config file
include "../../../config.php";

use Kreait\Firebase\Factory;

// Set up factory - path to your JSON file
$factory = (new Factory)->withServiceAccount('troop-tracker-dfd22-firebase-adminsdk-tfh9o-da0e5ec460.json');

// Capture raw POST data
$input = file_get_contents('php://input');

// Attempt to decode JSON
$data = json_decode($input, true);

// Ignore if the action is an update (post edit)
if ($data['event'] !== 'insert') {
    http_response_code(200); // Respond OK, but don't process
    exit('Ignoring post.');
}

// Log any JSON decoding errors
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(200); // Bad Request
    exit('Invalid JSON.');
}

// Validate data structure
if (!isset($data['data']) || !isset($data['content_type'])) {
    http_response_code(200); // Bad Request
    exit('Invalid data format.');
}

// Extract post details from 'data'
$post = $data['data'];
$postId = $post['post_id'] ?? null;
$thread_id = $post['thread_id'] ?? null;
$userId = $post['user_id'] ?? 'N/A';
$username = $post['username'] ?? 'N/A';
$postMessage = $post['message'] ?? 'N/A';
$postDate = date('Y-m-d H:i:s', $post['post_date'] ?? time());

// Don't proceed if no data
if (!is_numeric($thread_id) || !is_numeric($postId)) {
    http_response_code(200);
    exit('Invalid thread_id or post_id.');
}

// Prepare and execute query to get the event by thread_id
$sql = "
    SELECT 
        events.id AS event_id,
        events.name AS event_name,
        events.thread_id
    FROM 
        events
    WHERE 
        events.thread_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($event = $result->fetch_assoc()) {
        // Fetch event sign-ups for the event
        $event_id = $event['event_id'];
        $signup_sql = "
            SELECT 
                event_sign_up.trooperid 
            FROM 
                event_sign_up
            WHERE 
                event_sign_up.troopid = ?";
        
        $signup_stmt = $conn->prepare($signup_sql);
        $signup_stmt->bind_param("i", $event_id);
        $signup_stmt->execute();
        $signup_result = $signup_stmt->get_result();

        if ($signup_result->num_rows > 0) {
            while ($signup = $signup_result->fetch_assoc()) {
                $trooper_id = $signup['trooperid'];

                // Fetch user_id from troopers table
                $trooper_sql = "
                    SELECT 
                        troopers.user_id 
                    FROM 
                        troopers
                    WHERE 
                        troopers.id = ?";
                
                $trooper_stmt = $conn->prepare($trooper_sql);
                $trooper_stmt->bind_param("i", $trooper_id);
                $trooper_stmt->execute();
                $trooper_result = $trooper_stmt->get_result();

                if ($trooper_result->num_rows > 0) {
                    while ($trooper = $trooper_result->fetch_assoc()) {
                        //echo "User ID: " . $trooper['user_id'] . "<br>";

                        // Fetch FCM tokens for the user_id
                        $userid = $trooper['user_id'];
                        $fcm_sql = "
                            SELECT 
                                fcm 
                            FROM 
                                mobile_app
                            WHERE 
                                userid = ?";

                        $fcm_stmt = $conn->prepare($fcm_sql);
                        $fcm_stmt->bind_param("s", $userid);
                        $fcm_stmt->execute();
                        $fcm_result = $fcm_stmt->get_result();

                        if ($fcm_result->num_rows > 0) {
                            while ($fcm = $fcm_result->fetch_assoc()) {
                                //echo $fcm['fcm'] . "<br>";
                                $messaging = $factory->createMessaging();

                                // Create the notification payload
                                $message = [
                                    'token' => $fcm['fcm'],
                                    'notification' => [
                                        'title' => $event['event_name'] . ': ' . $username,
                                        'body' => $postMessage,
                                    ],
                                    'data' => [
                                        'troopName' => $event['event_name'],
                                        'threadId' => (string)$thread_id,
                                        'postId' => (string)$postId,
                                    ],
                                ];

                                try {
                                    $response = $messaging->send($message);
                                    echo 'Successfully sent message: ' . json_encode($response);
                                } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                                    echo 'Error sending message: ' . $e->getMessage();
                                } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
                                    echo 'Firebase error: ' . $e->getMessage();
                                }
                            }
                        }
                        $fcm_stmt->close();
                    }
                } else {
                    //echo "No troopers found for Trooper ID: $trooper_id<br>";
                }
                $trooper_stmt->close();
            }
        } else {
            //echo "No sign-ups found for Event ID: $event_id<br>";
        }
        $signup_stmt->close();
    }
} else {
    //echo "No events found for Thread ID: $thread_id<br>";
}

// Close connection
$conn->close();

// Respond to XenForo webhook
http_response_code(200);
?>
